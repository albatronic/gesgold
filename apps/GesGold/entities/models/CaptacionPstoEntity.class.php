<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 25.04.2012 12:27:39
 */

/**
 * @orm:Entity(captacion_psto)
 */
class CaptacionPstoEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="captacion_psto")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="captacion_psto")
     * @var entities\CaptacionTipos
     */
    protected $IDCaptacion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="captacion_psto")
     */
    protected $Ano;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="captacion_psto")
     */
    protected $Presupuesto;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'captacion_psto';
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
        'CaptacionTipos',
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

    public function setIDCaptacion($IDCaptacion) {
        $this->IDCaptacion = $IDCaptacion;
    }

    public function getIDCaptacion() {
        if (!($this->IDCaptacion instanceof CaptacionTipos))
            $this->IDCaptacion = new CaptacionTipos($this->IDCaptacion);
        return $this->IDCaptacion;
    }

    public function setAno($Ano) {
        $this->Ano = $Ano;
    }

    public function getAno() {
        return $this->Ano;
    }

    public function setPresupuesto($Presupuesto) {
        $this->Presupuesto = $Presupuesto;
    }

    public function getPresupuesto() {
        return $this->Presupuesto;
    }

}

// END class captacion_psto
?>