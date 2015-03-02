<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.04.2012 17:27:05
 */

/**
 * @orm:Entity(contratos)
 */
class Contratos extends ContratosEntity {

    public function __toString() {
        return $this->getIDContrato();
    }

    public function getNumeroDocumento() {
        return $this->NumeroContrato;
    }

    protected function load() {
        if ($this->IDContrato == '') {
            // Si el nº de contrato está vacio (se ha instanciado un objeto vacio),
            // asigno valores por defecto (agente,comercial,sucursal,almacen y cliente).
            $this->setIDAgente($_SESSION['USER']['user']['id']);

            $agente = new Agentes($_SESSION['USER']['user']['id']);
            $esComercial = ($agente->getEsComercial());
            if ($esComercial) {
                $idComercial = $_SESSION['USER']['user']['id'];
                $this->setIDComercial($idComercial);
            }

            $rows = $agente->getSucursales($_SESSION['emp']);
            $idSucursal = $rows[0]['Id'];
            $this->setIDSucursal($idSucursal);

            unset($agente);
        }

        parent::load();
    }

    /**
     * Crea un contrato
     *
     * @return boolean
     */
    public function create() {

        // Calcular el Número de contrato en base al contador
        $contador = new Contadores($this->IDContador);
        $this->setNumeroContrato($contador->asignaContador());
        $this->setClave(md5($this->NumeroContrato));
        unset($contador);

        $ok = parent::create();

        if ($ok) {
            $this->recalcula();
            $this->save();
        }

        return $ok;
    }

    /**
     * Guarda la informacion (update)
     *
     * @return boolean
     */
    public function save() {

        // Si no esta confirmado, se recalcula antes de guardar
        if ($this->IDEstado < 1)
            $this->recalcula();

        return parent::save();
    }

    /**
     * Borra un contrato y sus líneas de histórico
     * Siempre que esté en estado 0 (pte. confirmar)
     *
     * @return boolean
     */
    public function erase() {

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "DELETE FROM {$this->_dataBaseName}.contratos WHERE `IDContrato`='{$this->IDContrato}' AND IDEstado='0'";
            if ($this->_em->query($query)) {
                //Borrar líneas de histórico
                $query = "DELETE FROM {$this->_dataBaseName}.contratos_historico where `IDContrato`='{$this->IDContrato}'";
                if (!$this->_em->query($query))
                    $this->_errores = $this->_em->getError();
            } else
                $this->_errores = $this->_em->getError();
            $this->_em->desConecta();
        }
        unset($this->_em);

        return (count($this->_errores) == 0);
    }

    /**
     * Anula la confirmacion del contrato.
     *
     * Si está confirmado:
     *   crea el movimiento de almacen de anulacion y actualiza existencias
     *   quita el/los apunte/s de caja
     *
     */
    public function anula() {

        if ($this->IDEstado == '1') {
            // Quitar apunte de caja
            $apunteCaja = new CajaLineas();
            $rows = $apunteCaja->cargaCondicion("IDApunte", "Entidad='" . get_class($this) . "' and IDEntidad='{$this->IDContrato}'");
            foreach ($rows as $row) {
                $apunteCaja = new CajaLineas($row['IDApunte']);
                $apunteCaja->erase();
            }
            unset($apunteCaja);

            // Anular el movimiento de almacén
            if ($this->mvtoAlmacen('S')) {
                $this->setIDEstado(0);
                $this->save();

                $this->anotaHistorico(new OperacionesContratos(8));
            }
        }
    }

    /**
     * Confirma el contrato si el peso neto es distinto a cero, que consiste en:
     *
     *   1.- Marcarlo como finalizado
     *   2.- Generar el movimiento de almacen de entrada de producto
     *   3.- Generar el/los apuntes en caja para el pago del mismo
     *   4.- Generar el apunte en el histórico del contrato
     * 
     * @param array $pago Array con las eventuales formas de pago (hasta 2 diferentes)
     */
    public function confirma($pagos) {

        // Si el contrato es de compra, hay que actualizar existencias
        $articulo = new Articulos($this->IDArticulo);
        switch ($articulo->getIDVenta()->getIDVenta()) {

            case '1': // Contrato de compra

                if ($this->mvtoAlmacen('E')) {

                    $this->setIDEstado(1);
                    if ($this->save()) {
                        if ($this->anotaEnCaja($pagos))
                            $this->anotaHistorico(new OperacionesContratos(0));
                    }
                }

                break;

            case '2': // Contrato de Empeño

                if ($this->mvtoAlmacen('E')) {

                    $this->setIDEstado(1);
                    if ($this->save()) {
                        if ($this->anotaEnCaja())
                            $this->anotaHistorico(new OperacionesContratos(2));
                    }
                }

                break;
        }
    }

    /**
     * Renueva el contrato de empeño. Consiste en:
     *
     *   1.- Anotar ENTRADA en caja del importe de renovacion
     *   2.- Anotar en el historico del contrato
     *   3.- Actualizar la fecha de Vencimiento
     */
    public function renueva() {

        $articulo = new Articulos($this->IDArticulo);
        if ($articulo->getIDVenta()->getIDVenta() == '2') {

            $aux = $this->ImportePago;
            $this->ImportePago = -1 * $this->ImporteRenovacion;

            if ($this->anotaEnCaja()) {

                $this->ImportePago = $aux;
                $this->anotaHistorico(new OperacionesContratos(3));

                $fecha = new Fecha($this->FechaVcto);

                $contrato = new Contratos($this->IDContrato);
                $contrato->setFechaVcto($fecha->sumaDias($articulo->getNumeroDias()));
                unset($fecha);
                $contrato->save();
                unset($contrato);
            }
        }
        unset($articulo);
    }

    /**
     * Recupera el contrato de empeño. Consiste en:
     *
     *   1.- Anotar ENTRADA en caja del importe de recuperacion
     *   2.- Anotar en el historico del contrato
     *   3.- Hacer movimiento de almacen sacando el producto
     */
    public function recupera() {

        $articulo = new Articulos($this->IDArticulo);
        if ($articulo->getIDVenta()->getIDVenta() == '2') {

            if ($this->mvtoAlmacen('S')) {
                $this->ImportePago = -1 * $this->ImporteRecuperacion;
                if ($this->anotaEnCaja())
                    $this->anotaHistorico(new OperacionesContratos(4));
            }
        }
        unset($articulo);
    }

    /**
     * Realiza el movimiento de almacen
     *
     * No se realiza movimiento si el el peso neto es igual a 0.
     *
     * @param char(1) $signo E Entrada, S Salida
     * @return boolean TRUE si el mvto se hizo correctamente
     */
    private function mvtoAlmacen($signo) {

        $ok = false;

        if ($this->PesoNeto != 0) {
            $valores = array(
                'UM' => 'UMV',
                'Reales' => $this->PesoNeto,
                'Pales' => 0,
                'Cajas' => 0,
                'Reservadas' => 0,
                'Entrando' => 0,
            );

            $mvtoAlmacen = new MvtosAlmacen();
            $ok = $mvtoAlmacen->genera(
                    get_class($this), $signo, $this->IDContrato, // El Numero de contrato
                    $this->IDAlmacen, // El id del almacen
                    $this->IDArticulo, // El id del articulo
                    0, // El id del lote
                    0, // El id de la ubicacion
                    0, // Flag de deposito
                    $valores  // Valores con los que actualizar
            );
            unset($mvtoAlmacen);
        }

        return $ok;
    }

    /**
     * Recalcula el contrato según tipo (compra / empeño)
     */
    private function recalcula() {

        // Cálculos dependiendo del tipo de venta
        $articulo = new Articulos($this->IDArticulo);

        switch ($articulo->getIDVenta()->getIDVenta()) {

            case '1': // Compra
                $this->PorcentajeAlta = 0;
                $this->ImporteRecuperacion = 0;
                $this->ImporteRenovacion = 0;
                $this->ImportePago = $this->PesoNeto * $this->PrecioGramo;
                $this->FechaVcto = '0';
                break;

            case '2': //Empeño
                $this->PorcentajeAlta = $articulo->getPorcentajeAlta();
                $this->ImporteRecuperacion = $this->PesoNeto * $this->PrecioGramo;
                $this->ImporteRenovacion = $this->PorcentajeAlta * $this->ImporteRecuperacion / 100;
                $this->ImportePago = $this->ImporteRecuperacion - $this->ImporteRenovacion;
                $fecha = new Fecha($this->Fecha);
                $this->setFechaVcto($fecha->sumaDias($articulo->getNumeroDias()));
                unset($fecha);
                break;
        }

        unset($articulo);
    }

    /**
     * Chequea el riesgo del cliente
     *
     * Si tiene riesgo, pone en _errores el código de riesgo.
     *
     */
    public function validaLogico() {

        // Riesgo del cliente
        $cliente = new Clientes($this->IDCliente);
        if ($cliente->getCodigoRiesgo()) {
            $this->_errores[] = "Cliente con riesgo: " . $cliente->getCodigoRiesgo();
        }
    }

    /**
     * Comprueba que las formas de pago y que los importes cuadren con el
     * total del contrato.
     * 
     * Puede haber hasta dos formas de pago distintas por contrato
     * 
     * @param array $pago Array con las formas de pago y los importes
     * @return boolean TRUE si hay cuadre
     */
    public function validaPago($pagos) {

        $totalPagado = 0;

        foreach ($pagos as $pago)
            $totalPagado += $pago['Importe'];

        $ok = ($totalPagado == $this->ImportePago);

        if (!$ok)
            $this->_errores[] = "No cuadran los importes pagados ({$totalPagado}) con el importe del contrato ({$this->ImportePago})";

        return $ok;
    }

    /**
     * Hace el apunte de caja
     *
     * @return boolean
     */
    private function anotaEnCaja($pagos='') {

        $caja = new CajaArqueos();
        $ok = $caja->anotaEnCaja($this,$pagos);
        unset($caja);

        return $ok;
    }

    /**
     * Realiza un apunte en el histórico del contrato
     *
     * @param OperacionesContratos $operacion
     * @return boolean
     */
    private function anotaHistorico(OperacionesContratos $operacion) {

        $historico = new ContratosHistorico();
        $historico->setIDContrato($this->IDContrato);
        $historico->setIDOperacion($operacion->getIDTipo());
        $historico->setFecha(date('d/m/Y'));
        $historico->setDescripcion($operacion->getDescripcion() . " " . $this->getNumeroDocumento());
        $historico->setEntidad($this->getClassName());
        $historico->setIDEntidad($this->IDContrato);
        $ok = $historico->create();

        unset($historico);

        return $ok;
    }

// METODOS PARA LOS LISTADOS

    /**
     * Devuelve el Cif del cliente del contrato
     *
     * @return string EL Cif del cliente
     */
    public function getCifCliente() {
        return $this->getIDCliente()->getCif();
    }

    /**
     * Devuelve un string con la Razón social del cliente, la fecha de nacimiento,
     * el país y la dirección
     *
     * @return string
     */
    public function getDatosCliente() {

        $cliente = $this->getIDCliente();

        $string = $cliente->getRazonSocial() . "\n";
        $string .= $cliente->getFechaNacimiento() . " " . $cliente->getIDPais() . "\n";
        $string .= $cliente->getDireccion() . "\n";

        unset($cliente);

        return $string;
    }

}

?>