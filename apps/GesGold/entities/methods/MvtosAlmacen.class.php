<?php

/**
 * Description of MvtosAlmacen
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class MvtosAlmacen extends MvtosAlmacenEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Devuelve el numero de documento que ha generado el apunte de caja
     * @return string El numero de documento
     */
    public function getNumeroDocumento() {

        $numeroDocumento = "";

        $documento = $this->getDocumento();
        if ($documento)
            $numeroDocumento = $documento->getNumeroDocumento();

        unset($documento);
        return $numeroDocumento;
    }

    /**
     * Devuelve el objeto que ha provocado el movimiento de almacen
     *
     * @return string El numero de documento
     */
    public function getDocumento() {

        $documento = NULL;

        if (($this->IDTipo->getId() != '') and ($this->IDDocumento != '')) {
            // Averiguar la entidad que lo ha generado
            $tipoMovimiento = new TiposMvtosAlmacen($this->IDTipo->getId());
            $entidad = $tipoMovimiento->getTipoDocumento();
            unset($tipo);
            if ($entidad)
                $documento = new $entidad($this->IDDocumento);
        }

        return $documento;
    }

    protected function load() {
        if ($this->Id == '') {
            $this->setIDAgente($_SESSION['USER']['user']['id']);
            $this->setFecha('');
            $this->setHora(date('H:i:s'));
        } else
            parent::load();
    }

    /**
     * Genera un movimiento de almacen y actualiza el stock
     *
     * @param string $documento El literal que identifica el controlador que provoca el movimiento
     * @param string $signo El signo que tendrá el movimiento (E = Entrada, S = Salida)
     * @param integer $idDocumento El id del documento que provoca el movimiento (albaran, pedido, traspaso, etc)
     * @param integer $idAlmacen El id del almacén
     * @param integer $idArticulo El id del articulo
     * @param integer $idLote El id del lote
     * @param integer $idUbicacion El id de la ubicacion
     * @param integer $flagDeposito Indica si el movimineto se ha de realiza contra stock de deposito o no (0, 1)
     * @param array $valores Array con los valores a actualizar
     * @return boolean
     */
    public function genera($documento, $signo, $idDocumento, $idAlmacen, $idArticulo, $idLote, $idUbicacion, $flagDeposito, array $valores) {

        $ok = false;

        $tipoMvto = new TiposMvtosAlmacen();
        $filtro = "TipoDocumento='{$documento}' and Signo='{$signo}'";
        $row = $tipoMvto->cargaCondicion("Id,Signo", $filtro);
        unset($tipoMvto);
        $idTipo = $row[0]['Id'];
        $signo = $row[0]['Signo'];

        if ($signo) {

            $articulo = new Articulos($idArticulo);

            $this->setIDTipo($idTipo);
            $this->setIDAlmacen($idAlmacen);
            $this->setIDArticulo($idArticulo);
            $this->setIDLote($idLote);
            $this->setIDUbicacion($idUbicacion);
            $this->setIDDocumento($idDocumento);

            switch ($signo) {
                case 'E':
                    $this->setUnidadesE($articulo->convertUnit($valores['UM'], 'UMA', $valores['Reales']));
                    $this->setPalesE($valores['Pales']);
                    $this->setCajasE($valores['Cajas']);
                    $ok = true;
                    break;

                case 'S':
                    $unidades = $articulo->convertUnit($valores['UM'], 'UMA', -1 * $valores['Reales']);

                    $exi = new Existencias();
                    $stock = $exi->getStock($idArticulo, $idAlmacen, $idLote, $idUbicacion, $flagDeposito, 'UMA');
                    unset($exi);

                    if ($stock['RE'] >= $unidades) {
                        $ok = true;
                        $this->setUnidadesS($unidades);
                        $this->setPalesS(-1 * $valores['Pales']);
                        $this->setCajasS(-1 * $valores['Cajas']);
                    } else {
                        $this->_errores = "No hay suficiente stock para " . $articulo->getDescripcion();
                    }
                    break;
            }

            unset($articulo);

            if ($ok and parent::create()) {
                $ok = true;
                $exi = new Existencias();
                $exi->actualiza($idAlmacen, $idArticulo, $idLote, $idUbicacion, $flagDeposito, $valores);
                unset($exi);
            }
        }

        return $ok;
    }

}

?>
