<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:47
 */

/**
 * @orm:Entity(inventarios)
 */
class InventariosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios")
     */
    protected $Id;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios")
     */
    protected $IDAlmacen = '000';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios")
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="integer")
     */
    protected $IDLote;
    /**
     * @orm:Column(type="datetime")
     * @assert:NotBlank(groups="inventarios")
     */
    protected $Fecha = '0000-00-00 00:00:00';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="inventarios")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios")
     */
    protected $Stock = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="inventarios")
     */
    protected $Cerrado = '0';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'inventarios';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = trim($IDArticulo);
    }

    public function getIDArticulo() {
        if (!($this->IDArticulo instanceof Articulos))
            $this->IDArticulo = new Articulos($this->IDArticulo);
        return $this->IDArticulo;
    }

    public function setIDLote($IDLote) {
        $this->IDLote = $IDLote;
    }

    public function getIDLote() {
        if (!($this->IDLote instanceof Lotes))
            $this->IDLote = new Lotes($this->IDLote);
        return $this->IDLote;
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

    public function setStock($Stock) {
        $this->Stock = $Stock;
    }

    public function getStock() {
        return $this->Stock;
    }

    public function setCerrado($Cerrado) {
        $this->Cerrado = $Cerrado;
    }

    public function getCerrado() {
        if (!($this->Cerrado instanceof ValoresSN))
            $this->Cerrado = new ValoresSN($this->Cerrado);
        return $this->Cerrado;
    }

}

// END class inventarios
?>