<?php

/**
 * Description of FrecibidasLineas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class FrecibidasLineas extends FrecibidasLineasEntity {

    /**
     * Borra una línea de factura recibida.
     * Si procede de una línea de pedido, la marca como no facturada
     *
     * @return boolean
     */
    public function erase() {

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            //Volver a poner la linea de pedido como pendiente de facturar.
            $query = "DELETE FROM frecibidas_lineas where (IDLinea='{$this->IDLinea}')";
            if ($this->_em->query($query)) {
                if ($this->IDPedido != 0) {
                    $query = "UPDATE pedidos_lineas SET UnidadesPtesFacturar = UnidadesPtesFacturar + {$this->Unidades} WHERE IDLinea='{$this->IDLineaPedido}';";
                    $this->_em->query($query);
                }
            } else
                $this->_errores[] = $this->_em->getError();

            $this->_em->desConecta();
        }
        unset($this->_em);

        return (count($this->_errores) == 0);
    }

    public function __toString() {
        return $this->getIDLinea();
    }

}

?>
