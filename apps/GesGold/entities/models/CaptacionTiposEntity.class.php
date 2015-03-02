<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 25.04.2012 12:27:12
 */

/**
 * @orm:Entity(captacion_tipos)
 */
class CaptacionTiposEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="captacion_tipos")
     */
    protected $IDCaptacion;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="captacion_tipos")
     */
    protected $Captacion;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'captacion_tipos';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDCaptacion';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDCaptacion', 'ParentEntity' => 'CaptacionPsto', 'ParentColumn' => 'IDCaptacion'),
        array('SourceColumn' => 'IDCaptacion', 'ParentEntity' => 'Contratos', 'ParentColumn' => 'IDCaptacion'),
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
    public function setIDCaptacion($IDCaptacion) {
        $this->IDCaptacion = $IDCaptacion;
    }

    public function getIDCaptacion() {
        return $this->IDCaptacion;
    }

    public function setCaptacion($Captacion) {
        $this->Captacion = trim($Captacion);
    }

    public function getCaptacion() {
        return $this->Captacion;
    }

}

// END class captacion_tipos
?>