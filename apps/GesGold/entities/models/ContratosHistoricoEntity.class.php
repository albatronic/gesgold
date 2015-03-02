<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 08.08.2012 18:49:32
 */

/**
 * @orm:Entity(contratos_historico)
 */
class ContratosHistoricoEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos_historico")
     */
    protected $Id;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos_historico")
     */
    protected $IDContrato;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos_historico")
     */
    protected $IDOperacion;

    /**
     * @orm:Column(type="date")
     * @assert:NotBlank(groups="contratos_historico")
     */
    protected $Fecha;

    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="contratos_historico")
     */
    protected $Descripcion;

    /**
     * @orm:Column(type="string")
     * @assert:Blank(groups="contratos_historico")
     */
    protected $Entidad;

    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="contratos_historico")
     */
    protected $IDEntidad = 0;

    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'contratos_historico';

    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';

    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
    );

    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
    }

    public function setIDContrato($IDContrato) {
        $this->IDContrato = $IDContrato;
    }

    public function getIDContrato() {
        if (!($this->IDContrado instanceof Contratos))
            $this->IDContrato = new Contratos($this->IDContrato);
        return $this->IDContrato;
    }

    public function setIDOperacion($IDOperacion) {
        $this->IDOperacion = $IDOperacion;
    }

    public function getIDOperacion() {
        if (!($this->IDOperacion instanceof OperacionesContratos))
            $this->IDOperacion = new OperacionesContratos($this->IDOperacion);
        return $this->IDOperacion;
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

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setEntidad($Entidad) {
        $this->Entidad = trim($Entidad);
    }

    public function getEntidad() {
        return $this->Entidad;
    }

    public function setIDEntidad($IDEntidad) {
        $this->IDEntidad = $IDEntidad;
    }

    public function getIDEntidad() {
        return $this->IDEntidad;
    }

}

// END class contratos_historico
?>