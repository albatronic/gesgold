<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 17.12.2011 19:00:12
 */

/**
 * @orm:Entity(almacenes_mapas)
 */
class AlmacenesMapasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $IDUbicacion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="almacenes_mapas")
     * @var entities\Almacenes
     */
    protected $IDAlmacen;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="almacenes_mapas")
     */
    protected $Ubicacion;
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'almacenes_mapas';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDUbicacion';
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
        'Almacenes',
        'Agentes',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDUbicacion($IDUbicacion) {
        $this->IDUbicacion = $IDUbicacion;
    }

    public function getIDUbicacion() {
        return $this->IDUbicacion;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setUbicacion($Ubicacion) {
        $this->Ubicacion = trim($Ubicacion);
    }

    public function getUbicacion() {
        return $this->Ubicacion;
    }

}

// END class almacenes_mapas
?>