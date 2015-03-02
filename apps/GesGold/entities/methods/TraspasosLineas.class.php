<?php

/**
 * Description of TraspasosLineas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class TraspasosLineas extends TraspasosLineasEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {
        parent::save();

        //RECALCULAR EL TRASPASO
        $this->getIDTrapaso()->recalcula();
        $this->getIDTraspaso()->save();
    }

    /**
     * Crea un registro (insert)
     */
    public function create() {
        $lastId = parent::create($columns, $values);
        if ($lastId != NULL) {
            //RECALCULAR EL TRASPASO
            $this->getIDTraspaso()->recalcula();
            $this->getIDTraspaso()->save();
        }
        return $lastId;
    }

    /**
     * Validaciones antes de actualizar o crear
     */
    public function valida() {
        unset($this->_errores);

        //Para tener disponibles los datos de la
        //cabecera del traspaso
        $traspaso = new TraspasosCab($this->IDTraspaso);

        // Compruebo la existencia del artículo y que pertenezca a
        // la sucursal de la cabecera del albaran.
        if ($this->IDArticulo == '') {
            $this->_errores[] = "Debe indicar un código de artículo";
        } else {
            $articulo = new Articulos($this->IDArticulo);
        }

        // Si existe el articulo ...
        if (count($this->_errores) == 0) {

            $this->setDescripcion($articulo->getDescripcion());

            // Totalizar la linea de traspaso
            $this->setCosto($articulo->getPrecioCosto());
            $this->setImporteCosto($this->Unidades * $articulo->getPrecioCosto());


            // Si el artículo es inventariable:
            // Comprobar existencias sin tener en cuenta lote ni ubicación
            // de almacen. Solo se buscan existencias en el almacen indicado
            // en la línea del albarán
            if ($articulo->getInventario()->getIDTipo() == '1') {
                $existencias = new Existencias();
                $stock = $existencias->getStock($this->IDArticulo, $traspaso->IDAlmacenOrigen);

                if ($stock['DI'] < $this->getUnidades()) {
                    $this->_alertas[] = "Stock insuficiente";
                    $this->_alertas[] = "------------------";
                }

                $this->_alertas[] = "Disponible: " . $stock['DI'];
                $this->_alertas[] = "Real: " . $stock['RE'];
                $this->_alertas[] = "Reservado: " . $stock['RV'];
                $this->_alertas[] = "Pte. Entrada: " . $stock['PE'];
            }
        }

        unset($articulo);
        unset($traspaso);
        unset($existencias);

        return ( count($this->_errores) == 0 );
    }

    public function erase() {
        $trapaso = $this->getIDTraspaso();

        parent::erase();

        $traspaso->recalcula();
        $traspaso->save();
    }

}

?>
