<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 25.04.2012 12:26:18
 */

/**
 * @orm:Entity(contratos)
 */
class ContratosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $IDContrato;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\Contadores
     */
    protected $IDContador = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="contratos")
     */
    protected $NumeroContrato;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\Sucursales
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\Almacenes
     */
    protected $IDAlmacen;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="contratos")
     */
    protected $Fecha;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\Clientes
     */
    protected $IDCliente;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\Agentes
     */
    protected $IDAgente;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\Agentes
     */
    protected $IDComercial;
    /**
     * @orm:Column(type="tinyint")
     * @assert:NotBlank(groups="contratos")
     * @var entities\EstadosContratos
     */
    protected $IDEstado = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\FormasPago
     */
    protected $IDFP = '1';
    /**
     * @orm:Column(type="string")
     */
    protected $DescripcionPiezas;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\Articulos
     */
    protected $IDArticulo = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="contratos")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $PesoBruto = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $Tara = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $PesoNeto = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $PrecioGramo = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $PorcentajeAlta = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $ImportePago = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $ImporteRenovacion = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     */
    protected $ImporteRecuperacion = '0.00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="contratos")
     */
    protected $Clave;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="contratos")
     */
    protected $FechaVcto = '0000-00-00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos")
     * @var entities\CaptacionTipos
     */
    protected $IDCaptacion;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'contratos';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDContrato';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDContrato', 'ParentEntity' => 'ContratosHistorico', 'ParentColumn' => 'IDContrato'),
    );

    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'Contadores',
        'Sucursales',
        'Almacenes',
        'Clientes',
        'Agentes',
        'EstadosContratos',
        'FormasPago',
        'Articulos',
        'CaptacionTipos',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDContrato($IDContrato) {
        $this->IDContrato = $IDContrato;
    }

    public function getIDContrato() {
        return $this->IDContrato;
    }

    public function setIDContador($IDContador) {
        $this->IDContador = $IDContador;
    }

    public function getIDContador() {
        if (!($this->IDContador instanceof Contadores))
            $this->IDContador = new Contadores($this->IDContador);
        return $this->IDContador;
    }

    public function setNumeroContrato($NumeroContrato) {
        $this->NumeroContrato = trim($NumeroContrato);
    }

    public function getNumeroContrato() {
        return $this->NumeroContrato;
    }

    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        if (!($this->IDSucursal instanceof Sucursales))
            $this->IDSucursal = new Sucursales($this->IDSucursal);
        return $this->IDSucursal;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setFecha($Fecha) {
        $date = new Fecha($Fecha);
        $this->Fecha = $date->getFecha();
        unset($date);
    }

    public function getFecha() {
        $date = new Fecha($this->Fecha);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setIDCliente($IDCliente) {
        $this->IDCliente = $IDCliente;
    }

    public function getIDCliente() {
        if (!($this->IDCliente instanceof Clientes))
            $this->IDCliente = new Clientes($this->IDCliente);
        return $this->IDCliente;
    }

    public function setIDAgente($IDAgente) {
        $this->IDAgente = $IDAgente;
    }

    public function getIDAgente() {
        if (!($this->IDAgente instanceof Agentes))
            $this->IDAgente = new Agentes($this->IDAgente);
        return $this->IDAgente;
    }

    public function setIDComercial($IDComercial) {
        $this->IDComercial = $IDComercial;
    }

    public function getIDComercial() {
        if (!($this->IDComercial instanceof Agentes))
            $this->IDComercial = new Agentes($this->IDComercial);
        return $this->IDComercial;
    }

    public function setIDEstado($IDEstado) {
        $this->IDEstado = $IDEstado;
    }

    public function getIDEstado() {
        if (!($this->IDEstado instanceof EstadosContratos))
            $this->IDEstado = new EstadosContratos($this->IDEstado);
        return $this->IDEstado;
    }

    public function setIDFP($IDFP) {
        $this->IDFP = $IDFP;
    }

    public function getIDFP() {
        if (!($this->IDFP instanceof FormasPago))
            $this->IDFP = new FormasPago($this->IDFP);
        return $this->IDFP;
    }

    public function setDescripcionPiezas($DescripcionPiezas) {
        $this->DescripcionPiezas = trim($DescripcionPiezas);
    }

    public function getDescripcionPiezas() {
        return $this->DescripcionPiezas;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setPesoBruto($PesoBruto) {
        $this->PesoBruto = $PesoBruto;
    }

    public function getPesoBruto() {
        return $this->PesoBruto;
    }

    public function setTara($Tara) {
        $this->Tara = $Tara;
    }

    public function getTara() {
        return $this->Tara;
    }

    public function setPesoNeto($PesoNeto) {
        $this->PesoNeto = $PesoNeto;
    }

    public function getPesoNeto() {
        return $this->PesoNeto;
    }

    public function setPrecioGramo($PrecioGramo) {
        $this->PrecioGramo = $PrecioGramo;
    }

    public function getPrecioGramo() {
        return $this->PrecioGramo;
    }

    public function setPorcentajeAlta($PorcentajeAlta) {
        $this->PorcentajeAlta = $PorcentajeAlta;
    }

    public function getPorcentajeAlta() {
        return $this->PorcentajeAlta;
    }

    public function setImportePago($ImportePago) {
        $this->ImportePago = $ImportePago;
    }

    public function getImportePago() {
        return $this->ImportePago;
    }

    public function setImporteRenovacion($ImporteRenovacion) {
        $this->ImporteRenovacion = $ImporteRenovacion;
    }

    public function getImporteRenovacion() {
        return $this->ImporteRenovacion;
    }

    public function setImporteRecuperacion($ImporteRecuperacion) {
        $this->ImporteRecuperacion = $ImporteRecuperacion;
    }

    public function getImporteRecuperacion() {
        return $this->ImporteRecuperacion;
    }

    public function setClave($Clave) {
        $this->Clave = trim($Clave);
    }

    public function getClave() {
        return $this->Clave;
    }

    public function setFechaVcto($FechaVcto) {
        $date = new Fecha($FechaVcto);
        $this->FechaVcto = $date->getFecha();
        unset($date);
    }

    public function getFechaVcto() {
        $date = new Fecha($this->FechaVcto);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setIDCaptacion($IDCaptacion) {
        $this->IDCaptacion = $IDCaptacion;
    }

    public function getIDCaptacion() {
        if (!($this->IDCaptacion instanceof CaptacionTipos))
            $this->IDCaptacion = new CaptacionTipos($this->IDCaptacion);
        return $this->IDCaptacion;
    }

}

// END class contratos
?>