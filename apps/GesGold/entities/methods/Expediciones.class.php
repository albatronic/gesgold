<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 20.02.2012 00:11:14
 */

/**
 * @orm:Entity(expediciones)
 */
class Expediciones extends ExpedicionesEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Valida que la cantidad indicada esté disponible en
     * el almacen, ubicacion y lote
     * 
     * @return boolean
     */
    public function validaLogico() {

        $articulo = $this->getIDLineaAlbaran()->getIDArticulo();
        $udadMedidaVenta = $articulo->getUMV();
        $udadMedidaAlmacen = $articulo->getUMA();

        $almacen = new Almacenes($this->IDAlmacen);
        if ( ($almacen->getControlUbicaciones()->getIDTipo() == '1') and ($this->IDUbicacion == 0) ) {
                $this->_errores[] = "Debe indicar la ubicación";
        }
        unset($almacen);

        $stock = new Existencias();
        $existencias = $stock->getStock($articulo->getIDArticulo(), $this->IDAlmacen, $this->IDLote, $this->IDUbicacion);
        unset($stock);

        // Calcular las cantidades de ese articulo,almacen,lote y ubicacion que estan
        // en lineas de expediciones sin estar expedidas aún. O sea lo que está en
        // proceso de expedicion. No tengo en cuenta los valores de la BD de la línea actual
        // porque puede que hayan cambiado.
        $filtro = "IDLinea<>'{$this->IDLinea}' and Expedida='0' and IDAlmacen='{$this->IDAlmacen}' and IDLote='{$this->IDLote}' and IDUbicacion='{$this->IDUbicacion}'";
        $expedicion = new Expediciones();
        $rows = $expedicion->cargaCondicion("sum(Unidades) as Unidades,sum(Pales) as Pales,sum(Cajas) as Cajas", $filtro);
        $row = $rows[0];
        $row['Unidades'] += $this->Unidades;
        $row['Pales'] += $this->Pales;
        $row['Cajas'] += $this->Cajas;
        unset($expedicion);

        if ($this->Unidades == 0)
            $this->_errores[] = "Debe indicar una cantidad distinta a cero";
        if ($existencias['DI'] < $articulo->convertUnit('UMV', 'UMA', $row['Unidades']))
            $this->_errores[] = "Hay " . round($articulo->convertUnit('UMA', 'UMV', $existencias['DI']),2) ." {$udadMedidaVenta} (" . round($existencias['DI'],2) . " {$udadMedidaAlmacen}) disponibles para ese lote y ubicación";
        if ($existencias['CA'] < $row['Cajas'])
            $this->_errores[] = "Hay {$existencias['CA']} cajas disponibles para ese lote y ubicacion";
        if ($existencias['PT'] < $row['Pales'])
            $this->_errores[] = "Hay {$existencias['PT']} pales disponibles para ese lote y ubicacion";

        unset($articulo);

        return (count($this->_errores) == 0);
    }

}

?>