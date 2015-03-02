<?php

/**
 * Description of Existencias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Existencias extends ExistenciasEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Devuelve un array con las existencias
     * del ARTICULO, ALMACEN (opcional), LOTE (opcional), UBICACION (opcional) y DEPOSITO (opcional) indicado.
     * Las cantidades se expresan en la Unidad de Medida ($um) indicada, por defecto la unidad de medida de almacen
     *
     * Si no se indican almacen, devuelve las existencias en todos los almacenes.
     *
     * El array tiene seis elementos:
     *
     *  'DI' => Existencias Disponibles para la venta (reales-reservadas).
     *  'RE' => Existencias Reales.
     *  'PT' => Pales.
     *  'CA' => Cajas.
     *  'RV' => Existencias Reservadas por albaranes confirmados sin expedir o traspasos en proceso.
     *  'PE' => Existencias Pendientes de entrada por pedidos no recepcionados o traspasos en proceso.
     *
     * @param integer $idArticulo El artículo a consultar
     * @param integer $idAlmacen El almacen a consultar. Por defecto todos los almacenes
     * @param integer $idLote El lote a consultar. Por defecto todos los lotes.
     * @param integer $idUbicacion La ubicacion a consultar. Por defecto todas la ubiaciones.
     * @param integer $enDeposito (0 = No en deposito, 1 = En deposito, -1 = Todas) Por defecto todas.
     * @param string  $um Unidad de medida en la que se expresarán las cantidades
     * @return array $stock
     */
    public function getStock($idArticulo, $idAlmacen=0, $idLote=0, $idUbicacion=0, $enDeposito=-1, $um='UMA') {

        $this->conecta();
        if (is_resource($this->_dbLink)) {

            //$query = "Call {$this->_dataBaseName}.getStock({$idArticulo},{$idAlmacen},{$idLote},{$idUbicacion},{$enDeposito})";
            ($idAlmacen == 0) ? $filtroAlmacen = "LIKE '%'" : $filtroAlmacen = "='{$idAlmacen}'";
            ($idLote == 0) ? $filtroLote = "LIKE '%'" : $filtroLote = "='{$idLote}'";
            ($idUbicacion == 0) ? $filtroUbicacion = "LIKE '%'" : $filtroUbicacion = "='{$idUbicacion}'";
            ($enDeposito == -1) ? $filtroDeposito = "LIKE '%'" : $filtroDeposito = "='{$enDeposito}'";
            
            $query = "SELECT
                        SUM(e.Reales) as RE,
                        SUM(e.Pales) as PT,
                        SUM(e.Cajas) as CA,
                        SUM(e.Reservadas) as RV,
                        SUM(e.Entrando) as PE
                      FROM {$this->_dataBaseName}.existencias e
                      WHERE
                        (e.IDAlmacen {$filtroAlmacen}) AND
                        (e.IDArticulo = '{$idArticulo}') AND
                        (e.IDLote {$filtroLote}) AND
                        (e.IDUbicacion {$filtroUbicacion}) AND
                        (e.EnDeposito {$filtroDeposito})";

            if ($this->_em->query($query)) {
                $rows = $this->_em->fetchResult();
                $row = $rows[0];
                $this->_em->desConecta();
                // Si la unidad de medida solicitada es distinta a la de almacenaje,
                // transformo las cantidades a la unidad de medida indicada
                if ($um != 'UMA') {
                    $articulo = new Articulos($idArticulo);
                    $unidadMedida = $articulo->{"get$um"}();
                    $row['RE'] = $articulo->convertUnit('UMA', $um, $row['RE']) . " " . $unidadMedida;
                    $row['RV'] = $articulo->convertUnit('UMA', $um, $row['RV']) . " " . $unidadMedida;
                    $row['PE'] = $articulo->convertUnit('UMA', $um, $row['PE']) . " " . $unidadMedida;
                    unset($articulo);
                }
                $row['DI'] = $row['RE'] - $row['RV'] . " " . $unidadMedida;
            } else {
                $this->_errores = $this->_em->getError();
            }

            $this->_em->desConecta();
        }
        unset($this->_em);

        return $row;
    }

    /*
     * Devuelve true o false según haya o no registro de existencias con valores
     * distintos a cero en las Reales,Reservadas y Pendiente de entrada.
     * 
     * @return boolean
     */

    public function hayRegistroExistencias($idArticulo) {
        $exi = $this->getStock($idArticulo);
        return ( ($exi['RE'] + $exi['RV'] + $exi['PE']) != 0);
    }

    /**
     * Actualiza (incrementa o decrementa según el signo) las existencias del artículo, almacén, ubicación y lote
     * con los valores que recibe en el array $valores que tiene siete elementos:
     *
     * UM (unidad de medida origen), Reales, Pales, Cajas, Reservadas, Entrando, Deposito
     *
     * Si no existiese ninguna entrada en el registro de existencias para
     * los valores recibidos, la crea.
     *
     * @param integer $idAlmacen id de almacén
     * @param integer $idArticulo id de artículo
     * @param integer $idLote id de lote
     * @param integer $idUbicacion id de la ubicación
     * @param integer $flagDeposito
     * @param array $valores array('UM',Reales','Pales','Cajas','Reservadas','Entrando')
     */
    public function actualiza($idAlmacen, $idArticulo, $idLote, $idUbicacion, $flagDeposito, array $valores) {

        $condicion = "(IDAlmacen='{$idAlmacen}') AND (IDArticulo='{$idArticulo}') AND (IDLote='{$idLote}') AND (IDUbicacion='{$idUbicacion}') AND (EnDeposito='{$flagDeposito}')";
        $rows = $this->cargaCondicion('*', $condicion);
        $this->setId($rows[0]['Id']);
        $this->load();

        $articulo = new Articulos($idArticulo);

        // Si existe la entrada, la actualizo
        if (count($rows)) {
            $this->setReales($this->getReales() + $articulo->convertUnit($valores['UM'], 'UMA', $valores['Reales'], 6));
            $this->setPales($this->getPales() + $valores['Pales']);
            $this->setCajas($this->getCajas() + $valores['Cajas']);
            $this->setReservadas($this->getReservadas() + $articulo->convertUnit($valores['UM'], 'UMA', $valores['Reservadas'], 6));
            $this->setEntrando($this->getEntrando() + $articulo->convertUnit($valores['UM'], 'UMA', $valores['Entrando'], 6));
            $this->save();
        } else {
            // Si no existe, la creo
            $this->setIDAlmacen($idAlmacen);
            $this->setIDUbicacion($idUbicacion);
            $this->setIDArticulo($idArticulo);
            $this->setIDLote($idLote);
            $this->setReales($articulo->convertUnit($valores['UM'], 'UMA', $valores['Reales'], 6));
            $this->setPales($valores['Pales']);
            $this->setCajas($valores['Cajas']);
            $this->setReservadas($articulo->convertUnit($valores['UM'], 'UMA', $valores['Reservadas'], 6));
            $this->setEntrando($articulo->convertUnit($valores['UM'], 'UMA', $valores['Entrando'], 6));
            $this->setEnDeposito($flagDeposito);
            $this->create();
        }

        // Si despues de actualizar, todas las columnas de valores se han quedado a cero, borro el registro
        if (($this->Reales + $this->Reservadas + $this->Entrando + $this->Pales + $this->Cajas) < 0.00009)
            $this->erase();

        unset($articulo);
    }

}

?>
