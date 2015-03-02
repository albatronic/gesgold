<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 15.04.2012 00:47:17
 */

/**
 * @orm:Entity(caja_arqueos)
 */
class CajaArqueos extends CajaArqueosEntity {

    public function __toString() {
        return $this->getIDArqueo();
    }

    /**
     * Si no esta cerrada, se recalcula antes de guardar
     */
    public function save() {

        if ($this->CajaCerrada == 0)
            $this->recalcula();

        parent::save();
    }

    /**
     * Realizar la apertura de caja.
     * Hace un apunte de apertura en la caja que se va a abrir
     * en base al importe del cierre de la caja anterior cerrada
     *
     * Comprobar que no exista otra caja para la misma sucursal, tpv y dia
     */
    public function create() {

        if ($this->estaAbierta())
            $this->_errores[] = "Ya existe un arqueo de caja para ese día";
        else
            return $this->apertura();
    }

    /**
     * Comprueba si está abierta la caja correspondiente
     * a la sucursal, tpv y día de las propiedades del objeto
     *
     * @return int EL id de arqueo si está abierta, o cero en caso contrario (está cerrada o no abierta)
     */
    public function estaAbierta() {

        $idArqueo = 0;

        $filtro = "IDSucursal='{$this->IDSucursal}' and IDTpv='{$this->IDTpv}' and Dia='{$this->Dia}' and CajaCerrada='0'";
        $arqueo = new CajaArqueos();
        $rows = $arqueo->cargaCondicion('IDArqueo', $filtro);
        unset($arqueo);

        $idArqueo = $rows[0]['IDArqueo'];

        return $idArqueo;
    }

    /**
     * Comprueba si está cerrada la caja correspondiente
     * a la sucursal, tpv y día de las propiedades del objeto
     *
     * @return boolean True si existe y está cerrada, false si no existe o existiendo está abierta
     */
    public function estaCerrada() {

        $filtro = "IDSucursal='{$this->IDSucursal}' and IDTpv='{$this->IDTpv}' and Dia='{$this->Dia}' and CajaCerrada='1'";
        $arqueo = new CajaArqueos();
        $rows = $arqueo->cargaCondicion('IDArqueo', $filtro);
        unset($arqueo);

        return (count($rows) == 1);
    }

    /**
     * Comprobar que la fecha indicada no sea anterior a la actual
     */
    public function validaLogico() {

        // Comprobar que la fecha indicada no sea anterior a la actual.
        if ($this->Dia < date('Y-m-d'))
            $this->_errores[] = "La fecha no puede ser anterior a la actual";
    }

    /**
     * Recalcula los totales de caja
     * !! OJO: no guarda. Hay que llamar al método save
     * @return boolean
     */
    public function recalcula() {

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            // Calcular el importe de los movimientos de apertura (Origen=0)
            $query = "select sum(Importe) as Importe from {$this->_dataBaseName}.caja_lineas where (IDArqueo='{$this->IDArqueo}' and Origen='0')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->setSaldoApertura($rows[0]['Importe']);

            // Calcular el importe del resto de los movimientos (Origen<>0)
            $query = "select sum(Importe) as Importe from {$this->_dataBaseName}.caja_lineas where (IDArqueo='{$this->IDArqueo}' and Origen<>'0')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->setSumaMvtos($rows[0]['Importe']);

            // Calcular el importe total de los movimientos
            $this->setSaldoCierre($this->SaldoApertura + $this->SumaMvtos);
        }
    }

    /**
     * Abre la caja y crea un apunte de apertura con el
     * saldo de cierre de la caja que esté cerrada inmediatamente
     * antes.
     *
     * @return integer El id del arqueo creado
     */
    public function apertura() {

        // Localizar el importe del arqueo anterior que esté cerrado
        $filtro = "IDSucursal='{$this->IDSucursal}' and IDTpv='{$this->IDTpv}' and CajaCerrada='1'";
        $arqueo = new CajaArqueos();
        $rows = $arqueo->cargaCondicion("Dia,SaldoCierre", $filtro, "Dia DESC");
        unset($arqueo);
        if (count($rows))
            $saldoCierreAnterior = $rows[0]['SaldoCierre'];
        else
            $saldoCierreAnterior = 0;

        // Abrir la caja
        $idArqueo = parent::create();

        if (($idArqueo) and ($saldoCierreAnterior)) {
            // Crear el apunte de apertura
            $dia = new Fecha($rows[0]['Dia']);
            $apunte = new CajaLineas();
            $apunte->setIDArqueo($idArqueo);
            $apunte->setFecha(date('Y-m-d H:i:s'));
            $apunte->setConcepto('APERTURA CON EL CIERRE DEL DIA ' . $dia->getddmmaaaa());
            $apunte->setIDFP(1); // Contado
            $apunte->setOrigen(0); // Apertura
            $apunte->setImporte($saldoCierreAnterior);
            $apunte->setIDAgente($_SESSION['USER']['user']['id']);
            $apunte->create();
            $this->_errores = $apunte->getErrores();
            unset($apunte);
            unset($dia);
        }

        return $idArqueo;
    }

    /**
     * Marca la caja como cerrada
     */
    public function cierra() {
        $this->setCajaCerrada(1);
        $this->save();
    }

    /**
     * Devuelve un array con los totales de movimientos por tipo de forma de cobro
     * El array es
     * array{
     *   0 => array(IDFP=> , Descripcion=> , Importe=>)
     *   . . .
     *   n => array(IDFP=> , Descripcion=> , Importe=>)
     * }
     * @return array Totalizacion de movimientos por tipo de forma de cobro
     */
    public function getResumen() {

        $resumen = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            // Calcular el importe de los movimientos de apertura (Origen=0)
            $query = "select t1.IDFP as IDFP, t2.Descripcion, sum(t1.Importe) as Importe from {$this->_dataBaseName}.caja_lineas as t1, {$this->_dataBaseName}.formas_pago as t2 where (t1.IDArqueo='{$this->IDArqueo}') and (t1.IDFP=t2.IDFP) group by t1.IDFP";
            $this->_em->query($query);
            $resumen = $this->_em->fetchResult();
        }

        return $resumen;
    }

    /**
     * Recibe un objeto y un array con formas de pago e importes
     * y realiza tantos apuntes de caja como formas de pago vengan en el array
     *
     * Si la caja no estuviera abierta, la abre.
     *
     * Los objetos posibles son:
     *
     *  Contratos
     *  FemitidasCab
     *  FrecibidasCab
     *  RecibosClientes
     *  RecibosProveedores
     *
     * El array de formas de pago puede tener N elementos. Cada elemento consiste en:
     * 
     *      * IDFP => El id de la forma de pago
     *      * Importe => El importe pagado
     * 
     *
     * @param object $objeto FemitidasCab, FrecibidasCab, RecibosClientes, RecibosProveedores
     * @param array $pagos Array con diferentes formas de pago (opcional)
     * @return boolean TRUE si se pudo crear, FALSE si no.
     */
    public function anotaEnCaja($objeto, $pagos = '') {

        $ok = false;

        if ($_SESSION['tpv'] == '') {
            $this->_errores[] = "No se ha establecido el TPV";
            return $ok;
        }

        $entidad = get_class($objeto);
        $idSucursal = $objeto->getIDSucursal()->getIDSucursal();

        switch ($entidad) {
            case 'Contratos':
                $concepto = "Pago Contrato " . $objeto->getIDArticulo()->getIDVenta() . " " . $objeto->getNumeroDocumento();
                $origen = 7;
                if (!is_array($pagos)) {
                    $pagos[] = array(
                        'IDFP' => $objeto->getIDFP()->getIDFP(),
                        'Importe' => $objeto->getImportePago(),
                    );
                }
                foreach ($pagos as $key => $pago)
                    $pagos[$key]['Importe'] = -1 * $pagos[$key]['Importe'];

                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'FemitidasCab':
                $concepto = "Cobro N/Fra. " . $objeto->getNumeroFactura();
                $origen = 2;
                if (!is_array($pagos)) {
                    $pagos[] = array(
                        'IDFP' => $objeto->getIDFP()->getIDFP(),
                        'Importe' => $objeto->getTotal(),
                    );
                }
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'RecibosClientes':
                $concepto = "Cobro N/Fra. " . $objeto->getIDFactura()->getNumeroFactura();
                $origen = 3;
                if (!is_array($pagos)) {
                    $pagos[] = array(
                        'IDFP' => $objeto->getIDFP()->getIDFP(),
                        'Importe' => $objeto->getImporte(),
                    );
                }
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'FrecibidasCab':
                $concepto = "Pago S/Fra. " . $objeto->getNumeroFactura();
                $origen = 4;

                if (!is_array($pagos)) {
                    $pagos[] = array(
                        'IDFP' => $objeto->getIDFP()->getIDFP(),
                        'Importe' => $objeto->getTotal(),
                    );
                }
                foreach ($pagos as $key => $pago)
                    $pagos[$key]['Importe'] = -1 * $pagos[$key]['Importe'];
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;

            case 'RecibosProveedores':
                $concepto = "Pago S/Fra. " . $objeto->getIDFactura()->getNumeroFactura();
                $origen = 5;

                if (!is_array($pagos)) {
                    $pagos[] = array(
                        'IDFP' => $objeto->getIDFP()->getIDFP(),
                        'Importe' => $objeto->getImporte(),
                    );
                }
                foreach ($pagos as $key => $pago)
                    $pagos[$key]['Importe'] = -1 * $pagos[$key]['Importe'];
                $idEntidad = $objeto->getPrimaryKeyValue();
                break;
        }

        // Comprobar que la caja esté abierta, si no, abrirla
        $this->setIDSucursal($idSucursal);
        $this->setIDTpv($_SESSION['tpv']);
        $this->setDia(date('Y-m-d'));
        $idArqueo = $this->estaAbierta();

        if (!$idArqueo)
            $idArqueo = $this->apertura();

        if ($idArqueo) {
            foreach ($pagos as $pago) {
                $fp = new FormasPago($pago['IDFP']);
                if ( ($pago['Importe'] != 0) and ($fp->getAnotarEnCaja()->getIDTipo()) ) {
                    $apunte = new CajaLineas();
                    $apunte->setIDArqueo($idArqueo);
                    $apunte->setFecha(date('Y-m-d H:i:s'));
                    $apunte->setConcepto($concepto);
                    $apunte->setIDFP($pago['IDFP']);
                    $apunte->setOrigen($origen);
                    $apunte->setEntidad($entidad);
                    $apunte->setIDEntidad($idEntidad);
                    $apunte->setImporte($pago['Importe']);
                    $apunte->setIDAgente($_SESSION['USER']['user']['id']);
                    $ok = $apunte->create();
                    $this->_errores = $apunte->getErrores();
                }
                unset($fp);
            }
        } else
            $this->_errores[] = "No se ha realizado el apunte de caja.";

        return $ok;
    }

}

?>