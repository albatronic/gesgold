<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(traspasos_cab)
 */
class TraspasosCabEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $IDTraspaso;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $FechaSalida;
    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $FechaEntrada;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $IDAlmacenOrigen;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $IDAlmacenDestino;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $IDAgenteEnvia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $IDAgenteRecibe;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $IDAgencia;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Peso = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Volumen = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Bultos = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $ImporteCosto;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Expedicion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_cab")
     */
    protected $Estado;
    /**
     * @orm:Column(type="string")
     */
    protected $Observaciones;
    /**
     * @orm:Column(type="string")
     */
    protected $Clave;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'traspasos_cab';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDTraspaso';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDTraspaso($IDTraspaso) {
        $this->IDTraspaso = $IDTraspaso;
    }

    public function getIDTraspaso() {
        return $this->IDTraspaso;
    }

    public function setFechaSalida($FechaSalida) {
        $date = new Fecha($FechaSalida);
        $this->FechaSalida = $date->getFecha();
        unset($date);
    }

    public function getFechaSalida() {
        $date = new Fecha($this->FechaSalida);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setFechaEntrada($FechaEntrada) {
        $date = new Fecha($FechaEntrada);
        $this->FechaEntrada = $date->getFecha();
        unset($date);
    }

    public function getFechaEntrada() {
        $date = new Fecha($this->FechaEntrada);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setIDAlmacenOrigen($IDAlmacenOrigen) {
        $this->IDAlmacenOrigen = $IDAlmacenOrigen;
    }

    public function getIDAlmacenOrigen() {
        if (!$this->IDAlmacenOrigen instanceof Almacenes)
            $this->IDAlmacenOrigen = new Almacenes($this->IDAlmacenOrigen);
        return $this->IDAlmacenOrigen;
    }

    public function setIDAlmacenDestino($IDAlmacenDestino) {
        $this->IDAlmacenDestino = $IDAlmacenDestino;
    }

    public function getIDAlmacenDestino() {
        if (!$this->IDAlmacenDestino instanceof Almacenes)
            $this->IDAlmacenDestino = new Almacenes($this->IDAlmacenDestino);
        return $this->IDAlmacenDestino;
    }

    public function setIDAgenteEnvia($IDAgenteEnvia) {
        $this->IDAgenteEnvia = $IDAgenteEnvia;
    }

    public function getIDAgenteEnvia() {
        if (!($this->IDAgenteEnvia instanceof Agentes))
            $this->IDAgenteEnvia = new Agentes($this->IDAgenteEnvia);
        return $this->IDAgenteEnvia;
    }

    public function setIDAgenteRecibe($IDAgenteRecibe) {
        $this->IDAgenteRecibe = $IDAgenteRecibe;
    }

    public function getIDAgenteRecibe() {
        if (!($this->IDAgenteRecibe instanceof Agentes))
            $this->IDAgenteRecibe = new Agentes($this->IDAgenteRecibe);
        return $this->IDAgenteRecibe;
    }

    public function setIDAgencia($IDAgencia) {
        $this->IDAgencia = $IDAgencia;
    }

    public function getIDAgencia() {
        if (!($this->IDAgencia instanceof Agencias))
            $this->IDAgencia = new Agencias($this->IDAgencia);
        return $this->IDAgencia;
    }

    public function setPeso($Peso) {
        $this->Peso = $Peso;
    }

    public function getPeso() {
        return $this->Peso;
    }

    public function setVolumen($Volumen) {
        $this->Volumen = $Volumen;
    }

    public function getVolumen() {
        return $this->Volumen;
    }

    public function setBultos($Bultos) {
        $this->Bultos = $Bultos;
    }

    public function getBultos() {
        return $this->Bultos;
    }

    public function setImporteCosto($ImporteCosto) {
        $this->ImporteCosto = $ImporteCosto;
    }

    public function getImporteCosto() {
        return $this->ImporteCosto;
    }

    public function setExpedicion($Expedicion) {
        $this->Expedicion = trim($Expedicion);
    }

    public function getExpedicion() {
        return $this->Expedicion;
    }

    public function setEstado($Estado) {
        $this->Estado = $Estado;
    }

    public function getEstado() {
        if (!$this->Estado instanceof EstadosTraspasos)
            $this->Estado = new EstadosTraspasos($this->Estado);
        return $this->Estado;
    }

    public function setObservaciones($Observaciones) {
        $this->Observaciones = trim($Observaciones);
    }

    public function getObservaciones() {
        return $this->Observaciones;
    }

    public function setClave($Clave) {
        $this->Clave = trim($Clave);
    }

    public function getClave() {
        return $this->Clave;
    }

}

// END class traspasos_cab
?>