<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.06.2011 18:39:48
 */

/**
 * @orm:Entity(traspasos_lineas)
 */
class TraspasosLineasEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $IDTraspaso = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $IDArticulo = '';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Unidades = '0.00';
    /**
     * @orm:Column(type="integer")
     */
    protected $IDLote;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $Costo;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="traspasos_lineas")
     */
    protected $ImporteCosto;
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'traspasos_lineas';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDLinea';

    /**
     * GETTERS Y SETTERS
     */
    public function setIDLinea($IDLinea) {
        $this->IDLinea = $IDLinea;
    }

    public function getIDLinea() {
        return $this->IDLinea;
    }

    public function setIDTraspaso($IDTraspaso) {
        $this->IDTraspaso = $IDTraspaso;
    }

    public function getIDTraspaso() {
        return $this->IDTraspaso;
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

    public function setUnidades($Unidades) {
        $this->Unidades = $Unidades;
    }

    public function getUnidades() {
        return $this->Unidades;
    }

    public function setIDLote($IDLote) {
        $this->IDLote = $IDLote;
    }

    public function getIDLote() {
        if (!($this->IDLote instanceof Lotes))
            $this->IDLote = new Lotes($this->IDLote);
        return $this->IDLote;
    }

    public function setCosto($Costo) {
        $this->Costo = $Costo;
    }

    public function getCosto() {
        return $this->Costo;
    }

    public function setImporteCosto($ImporteCosto) {
        $this->ImporteCosto = $ImporteCosto;
    }

    public function getImporteCosto() {
        return $this->ImporteCosto;
    }

}

// END class traspasos_lineas
?>