<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 11.04.2012 12:31:18
 */

/**
 * @orm:Entity(articulos)
 */
class Articulos extends ArticulosEntity {

    public function __toString() {
        return $this->getDescripcion();
    }

   protected function validaLogico() {

        // Si hay stock no se puede cambiar el estado de inventario ni las unidades de medida
        $exi = new Existencias();

        if ($exi->hayRegistroExistencias($this->IDArticulo)) {
            // Hay registro de existencias
            $articulo = new Articulos($this->IDArticulo);
            $this->setUMA($articulo->getUMA()->getId());
            $this->setUMC($articulo->getUMC()->getId());
            $this->setUMV($articulo->getUMV()->getId());
            unset($articulo);
            $this->_alertas[] = "Hay Stock, no puede cambiar ni el estado de Inventario ni las Unidades de Medida";
        }

        unset($exi);
    }

    /**
     * Transforma una cantidad expresada en la unidad de medida origen a
     * otra cantidad expresada en la unidad de medida destino redondeada
     * a los dígitos de precisión indicados (por defecto 2 dígitos decimales)
     *
     * @param <type> $unidadMediaOrigen Valores Posibles (UMC,UMV,UMA)
     * @param <type> $unidadMedidaDestino  Valores Posibles (UMC,UMV,UMA)
     * @param decimal $cantidad Cantidad a transformar
     * @param integer Digitos de precision del redondeo (defecto= 2)
     * @return <type>
     */
    public function convertUnit($unidadMedidaOrigen, $unidadMedidaDestino, $cantidad, $precision=3) {
        $cantidadUnidadMedidaBasica = $cantidad * $this->{"getC$unidadMedidaOrigen"}();
        $cantidadUnidadMedidaDestino = $cantidadUnidadMedidaBasica / $this->{"getC$unidadMedidaDestino"}();
        return round($cantidadUnidadMedidaDestino, $precision);
    }
}

?>