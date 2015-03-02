<?php

/**
 * Description of Clientes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Clientes extends ClientesEntity {

    public function __toString() {
        return $this->getRazonSocial();
    }

    /**
     * Carga de datos en las variables de la clase
     */
    protected function load() {
        $this->IDSucursal = $_SESSION['suc'];
        parent::load();
    }

    /**
     * Crea un registro (insert)
     */
    public function create() {

        $lastId = parent::create();

        if ($lastId != NULL) {
            $this->setIDCliente($lastId);

            //ACTUALIZAR LA CUENTA CONTABLE EN BASE AL PREFIJO PARA LA SUCURSAL
            //Y AL NUMERO DE DIGITOS PARA LA EMPRESA
            $cContable = '';

            $this->conecta();

            // localiza la cta. contable mayor para la sucursal
            if (is_resource($this->_dbLink)) {
                $query = "SELECT `CContable` FROM `{$this->_dataBaseName}`.`{$this->_tableName}` WHERE IDSucursal='{$this->IDSucursal}' ORDER BY `CContable` DESC Limit 1;";
                $this->_em->query($query);
                $row = $this->_em->fetchResult();
                $this->_em->desConecta();
                unset($this->_em);
                $cContable = (int) $row[0]['CContable'];
            }

            $idSucursal = $this->IDSucursal;

            if ($cContable > 0) {
                $cContable += 1;
                $this->setCContable($cContable);
            } else {
                $prefijo = $this->getIDSucursal()->getCtaContableClientes();
                $empresa = new Empresas($_SESSION['emp']);
                $digitos = $empresa->getDigitosCuentaContable();
                unset($empresa);
                $this->setCContable(str_pad($prefijo, $digitos - 1, '0') . '1');
            }

            //PONER EL MISMO NOMBRE COMERCIAL SI ES QUE ESTE ESTÁ VACIO
            if ($this->getNombreComercial() == '')
                $this->setNombreComercial($this->getRazonSocial());
            $this->setIDSucursal($idSucursal);
            $this->save();

            //CREAR LA DIRECCION DE ENTREGA POR DEFECTO
            $this->createDireccionEntrega($this->getIDCliente());
        }

        return $lastId;
    }

    /**
     * Obliga a una fecha de nacimiento
     */
    public function validaLogico() {

        if ($this->FechaNacimiento == '0000-00-00')
            $this->_errores[] = "Debe indicar una fecha de nacimiento";
    }

    /**
     * Crea la direccion de entrega por defecto del cliente $IDCliente
     * @param ClientesDentrega $IDCliente
     */
    public function createDireccionEntrega($IDCliente) {
        $de = new ClientesDentrega();
        $de->setIDCliente($this->IDCliente);
        $de->setNombre($this->RazonSocial);
        $de->setDireccion($this->Direccion);
        $de->setIDPais($this->IDPais);
        $de->setPoblacion($this->Poblacion);
        $de->setIDProvincia($this->IDProvincia);
        $de->setCodPostal($this->CodigoPostal);
        $de->setTelefono($this->Telefono);
        $de->setFax($this->Fax);
        $de->setEMail($this->EMail);
        $de->setIDComercial($this->IDComercial);
        $de->setIDZona($this->IDZona);
        $de->create();
    }

    /**
     * Calcula el riesgo de un cliente
     * Devuelve el siguiente array:
     *
     * array(
     *       'AL' => array(),  //Albaranes pendientes de facturar (numero e importe)
     *       'RE' => array(),  //Recibos pendientes de cobro (numero e importe)
     *       'RI' => decimal,  //El límite de riesgo asignado por la aseguradora
     * );
     *
     * @return array $pteCobro Array con el riesgo del cliente
     */
    public function getRiesgo() {
        $pteCobro = array(
            'AL' => $this->getPteFacturar(), //Albaranes pendientes de facturar (numero e importe)
            'RE' => $this->getPteCobro(), //Recibos pendientes de cobro (numero e importe)
            'RI' => $this->getLimiteRiesgo(), //El límite de riesgo asignado por la aseguradora
        );

        return $pteCobro;
    }

    /**
     * Calcula el número de albaranes y el importe total con impuestos
     * de los albaranes del cliente indicado que están SERVIDOS (IDEstado=2) pero
     * NO facturados (IDFactura = '0')
     *
     * Devuelve el array (
     *      'Albaranes' =>  Numero de albaranes,
     *      'Importe'   =>  Suma del importe de todos los albaranes con impuestos
     * )
     *
     * @return Array $pteFacturar Array con lo pendiente de facturar
     */
    public function getPteFacturar() {
        $pteFacturar = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select count(IDAlbaran) as Albaranes, sum(Total) as Importe from {$this->_dataBaseName}.albaranes_cab where IDCliente='{$this->IDCliente}' and IDEstado='2' and IDFactura='0'";
            if ($this->_em->query($query)) {
                $rows = $this->_em->fetchResult();
                $pteFacturar = $rows[0];
            } else {
                $this->_errores[] = $this->_em->getError();
            }
            $this->_em->desConecta();
        } else {
            $this->_errores[] = $this->_em->getError();
        }

        return $pteFacturar;
    }

    /**
     * Calcula el número de recibos y el importe total de los
     * recibos del cliente indicado que no están PENDIENTES DE COBRO (IDEstado<>6)
     *
     * Devuelve el array (
     *      'Recibos'   =>  Numero de recibos,
     *      'Importe'   =>  Suma del importe de todos los recibos
     * )
     *
     * @return Array $pteCobro Array con lo pendiente de cobro
     */
    public function getPteCobro() {
        $pteCobro = array();

        $this->conecta();
        if (is_resource($this->_dbLink)) {
            $query = "select count(IDRecibo) as Recibos, sum(Importe) as Importe from {$this->_dataBaseName}.recibos_clientes where IDCliente='{$this->IDCliente}' and IDEstado<>'6'";
            if ($this->_em->query($query)) {
                $rows = $this->_em->fetchResult();
                $pteCobro = $rows[0];
            } else {
                $this->_errores[] = $this->_em->getError();
            }
            $this->_em->desConecta();
        } else {
            $this->_errores[] = $this->_em->getError();
        }

        return $pteCobro;
    }

    /**
     * Devuelve un array con todos los clientes de la sucursal (opcional) y agente (opcional) indicado
     * Si no se indica sucursal, se toma la sucursal en curso.
     * Si el agente indicado es comercial, solo se muestran sus clientes, en caso contrato
     * se muestran todos los clientes de la sucursal indicada.
     *
     * Cada elemento tiene la primarykey y el valor de $column
     */
    public function fetchAll($valorFiltro='') {

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $filtro = "WHERE (Vigente='1') AND ( (Cif LIKE '%{$valorFiltro}%') OR (RazonSocial LIKE '%{$valorFiltro}%') OR (NombreComercial LIKE '%{$valorFiltro}%'))";

            $query = "SELECT IDCliente as Id,CONCAT(RazonSocial,' [',Cif,'] ',CodigoRiesgo) as Value FROM `{$this->_dataBaseName}`.`clientes` {$filtro} ORDER BY RazonSocial ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '', Value => ':: Cliente');
        return $rows;
    }

    /**
     * Devuelve un array filtrando los clientes en base a:
     *   Un valor de filtro que se busca en la razon social y el nombre comercial (%valorFiltro%)
     *   Una sucursal, si no se indica se toma la sucursal en curso
     *   Si el usuario en curso es del rol comercial, solo se muestran los adscritos a el
     *
     * @param integer $idSucursal El id de la sucursal
     * @param string $valorFiltro
     * @return array
     */
    public function fetchClientesSucursal($idSucursal='', $valorFiltro='') {

        if ($idSucursal == '')
            $idSucursal = $_SESSION['suc'];

        $this->conecta();

        $rows = array();

        if (is_resource($this->_dbLink)) {
            $usuario = new Agentes($_SESSION['USER']['user']['id']);

            $filtro = "(IDSucursal='{$idSucursal}') and (Vigente='1') and ( (RazonSocial LIKE '%{$valorFiltro}%') or (NombreComercial LIKE '%{$valorFiltro}%') )";
            if ($usuario->getEsComercial())
                $filtro .= " and (IDComercial='" . $_SESSION['USER']['user']['id'] . "')";
            $query = "SELECT IDCliente as Id, CONCAT(RazonSocial,' - ',NombreComercial) as Value FROM `{$this->_dataBaseName}`.`clientes` where ( {$filtro} ) ORDER BY RazonSocial";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
            unset($usuario);
        }
        return $rows;
    }

    /**
     * Devuelve las promociones vigentes para el cliente
     * y la fecha indicada.
     *
     * El formato de fecha pasado debe ser YYYY-mm-dd
     *
     * @param date $fecha La fecha
     * @return array Array con objetos promociones
     */
    public function getPromosVigentes($fecha) {

        $promos = array();

        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            $query = "SELECT t1.IDPromocion
                FROM {$this->_dataBaseName}.promociones as t1, {$this->_dataBaseName}.promociones_clientes as t2
                WHERE t1.FinPromocion>='{$fecha}'
                AND t1.IDPromocion=t2.IDPromocion
                AND ( (t2.IDCliente='{$this->IDCliente}') OR (t2.IDGrupo='{$this->getIDGrupo()->getIDGrupo()}') )
                ORDER BY FinPromocion ASC";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();

            foreach ($rows as $row) {
                $promos[] = new Promociones($row['IDPromocion']);
            }
        }
        unset($em);

        return $promos;
    }

    /**
     * Devuelve la cuenta corriente con 20 dígitos
     *
     * @return string La cuenta corriente
     */
    public function getCtaCorriente() {
        return $this->IDBanco . $this->IDOficina . $this->Digito . $this->Cuenta;
    }

}

?>
