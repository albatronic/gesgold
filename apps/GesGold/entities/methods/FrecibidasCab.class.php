<?php

/**
 * Description of FrecibidasCab
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class FrecibidasCab extends FrecibidasCabEntity {

    public function __toString() {
        return $this->getNumeroFactura();
    }

    /**
     * Borra una factura recibida, sus líneas, los posibles recibos
     * y marca el/los pedidos asociados como no facturados
     * Siempre que no esté traspasa a contabilidad (Asiento=0)
     *
     * @return boolean
     */
    public function erase() {

        if ($this->Asiento == 0) {
            if ($this->borraVctos()) {
                if ($this->borraLineas()) {
                    // Actualiza la cabecera del pedido
                    $this->conecta();
                    if (is_resource($this->_dbLink)) {
                        $query = "UPDATE pedidos_cab SET IDFactura='0', IDEstado='2' WHERE IDFactura='{$this->IDFactura}'";
                        if (!$this->_em->query($query))
                            $this->_errores[] = $this->_em->getError();
                    }
                    // Borrar la cabecera de la factura
                    parent::erase();
                }
            }
        } else $this->_errores[] = "No se puede borrar. Está traspasada a contabilidad";

        return (count($this->_errores) == 0);
    }

    /**
     * Devuelve un array de objetos recibos de la factura
     * que están en el estado $idEstado. Por defecto todos
     *
     * @param integer $idEstado El estado de los recibos
     * @return array Objetos recibos de la factura
     */
    public function getRecibos($idEstado='') {
        $recibos = array();

        $filtro = "IDFactura='{$this->IDFactura}'";
        if ($idEstado != '')
            $filtro .= " AND IDEstado='{$idEstado}'";

        $recibo = new RecibosProveedores();
        $rows = $recibo->cargaCondicion("IDRecibo", $filtro, "Vencimiento ASC");
        foreach ($rows as $row) {
            $recibos[] = new RecibosProveedores($row['IDRecibo']);
        }
        unset($recibo);

        return $recibos;
    }

    /**
     * Crea los recibos de la factura en curso en base a la forma de pago.
     * Si el n. de vctos de la forma de pago es 0, no se crea ningún vencimiento.
     */
    public function creaVctos() {

        if ($this->Total == 0)
            return;

        $factura = new FrecibidasCab($this->IDFactura);

        //SI LA FACTURA ES SIN IVA, EN CASO DE CREAR RECIBOS SE MARCARÁN
        //CON N. DE ASIENTO 999999 PARA QUE NO SE TRASPASEN A CONTABILIDAD.
        $tieneiva = (($factura->getIva1() + $factura->getIva2() + $factura->getIva3()) != 0);
        if ($tieneiva)
            $asiento = 0;
        else
            $asiento=999999;

        $formaPago = $factura->getIDFP();
        $nvctos = $formaPago->getNumeroVctos();
        $fecha = new Fecha($factura->getFecha());
        $mes = $fecha->getmm();
        $dia = $fecha->getdd();
        $anio = $fecha->getaaaa();
        unset($fecha);

        if ($nvctos > 0) {
            $importe = ROUND($factura->getTotal() / $nvctos, 2);
            $diferencia = $factura->getTotal() - $importe * $nvctos;
            $i = 0;
            while ($i < $nvctos) {
                $i = $i + 1;
                if ($i == 1) {
                    $intervalo = $formaPago->getDiaPrimerVcto();
                    $importeRecibo = $importe + $diferencia;
                } else {
                    $intervalo = $intervalo + $formaPago->getDiasIntervalo();
                    $importeRecibo = $importe;
                }
                $numRecibo = str_pad($i, 2, "0", STR_PAD_LEFT) . str_pad($nvctos, 2, "0", STR_PAD_LEFT);
                $fVcto = date('Y-m-d', mktime(0, 0, 0, $mes, $dia + $intervalo, $anio));

                $recibo = new RecibosProveedores();
                $recibo->setRecibo($numRecibo);
                $recibo->setIDSucursal($factura->getIDSucursal()->getIDSucursal());
                $recibo->setIDFactura($factura->getIDFactura());
                $recibo->setIDProveedor($factura->getIDProveedor()->getIDProveedor());
                $recibo->setFecha($factura->getFecha());
                $recibo->setVencimiento($fVcto);
                $recibo->setImporte($importeRecibo);
                $recibo->setCBanco($factura->getIDProveedor()->getCtaCorriente());
                $recibo->setAsiento($asiento);
                $recibo->setIDEstado($formaPago->getEstadoRecibo()->getIDTipo());
                $recibo->setIDRemesa('');
                $recibo->setRemesar(1);
                $recibo->setCContable($formaPago->getCContable());
                $recibo->create();
                unset($recibo);
            }
        }
        unset($factura);
        unset($formaPago);
    }

    /**
     * Borrar los vencimientos de la factura
     * siempre y cuando no este traspasado a contabilidad (Asiento=0)
     *
     * Por lo tanto, borra los que no están traspasados; y los que si lo están
     * los deja.
     *
     * @return boolean
     */
    public function borraVctos() {
        $ok = false;

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "delete from recibos_proveedores where IDFactura='{$this->IDFactura}' and Asiento='0'";
            if (!$this->_em->query($query))
                $this->_errores[] = $this->_em->getError();
            else
                $ok = true;
            $this->_em->desConecta();
        }

        return $ok;
    }

    /**
     * Borrar las lineas de la factura y
     * pone en estado de no facturado (2) las lineas de pedido
     * de las que provienen
     *
     * @return boolean
     */
    private function borraLineas() {
        $ok = true;

        $linea = new FrecibidasLineas();
        $rows = $linea->cargaCondicion("IDLinea","IDFactura='{$this->IDFactura}'");
        unset($linea);
        foreach ($rows as $lineaFactura) {
            $lineaFactura = new FrecibidasLineas($lineaFactura['IDLinea']);
            // Cambia estado linea pedido
            $lineaPedido = $lineaFactura->getIDLineaPedido();
            $lineaPedido->setIDEstado(2);
            $lineaPedido->setUnidadesPtesFacturar($lineaPedido->getUnidadesPtesFacturar() + $lineaFactura->getUnidades());
            $lineaPedido->save();
            // Borrar linea factura
            $lineaFactura->erase();
        }
        return $ok;
    }
}

?>
