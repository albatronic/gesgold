<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 11.04.2012 12:31:18
 */

/**
 * @orm:Entity(articulos)
 */
class ArticulosEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $IDArticulo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Codigo;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="articulos")
     */
    protected $Descripcion;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     * @var entities\Familias
     */
    protected $IDFamilia = '1';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     * @var entities\TiposVenta
     */
    protected $IDVenta = '1';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $NumeroDias = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $PorcentajeAlta = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $PorcentajeRenovacion = '0.00';
    /**
     * @orm:Column(type="string")
     */
    protected $Caracteristicas;
    /**
     * Unidad de Medida Básica
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMB = '1';
    /**
     * Unidad de Medida para las Compras
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMC = '1';
    /**
     * Factor de conversion de la UMC hacia la UMB
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $CUMC = '1';
    /**
     * Unidad de Medida de Almacén
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMA = '1';
    /**
     * Factor de conversion de la UMA hacia la UMB
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $CUMA = '1';
    /**
     * Unidad de Media para las Ventas
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $UMV = '1';
    /**
     * Factor de conversion de la UMV hacia la UMB
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="articulos")
     */
    protected $CUMV = '1';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'articulos';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDArticulo';
    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'Existencias', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'Contratos', 'ParentColumn' => 'IDArticulo'),
        array('SourceColumn' => 'IDArticulo', 'ParentEntity' => 'MvtosAlmacen', 'ParentColumn' => 'IDArticulo'),
    );
    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'Familias',
        'TiposVenta',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDArticulo($IDArticulo) {
        $this->IDArticulo = $IDArticulo;
    }

    public function getIDArticulo() {
        return $this->IDArticulo;
    }

    public function setCodigo($Codigo) {
        $this->Codigo = trim($Codigo);
    }

    public function getCodigo() {
        return $this->Codigo;
    }

    public function setDescripcion($Descripcion) {
        $this->Descripcion = trim($Descripcion);
    }

    public function getDescripcion() {
        return $this->Descripcion;
    }

    public function setIDFamilia($IDFamilia) {
        $this->IDFamilia = $IDFamilia;
    }

    public function getIDFamilia() {
        if (!($this->IDFamilia instanceof Familias))
            $this->IDFamilia = new Familias($this->IDFamilia);
        return $this->IDFamilia;
    }

    public function setIDVenta($IDVenta) {
        $this->IDVenta = $IDVenta;
    }

    public function getIDVenta() {
        if (!($this->IDVenta instanceof TiposVenta))
            $this->IDVenta = new TiposVenta($this->IDVenta);
        return $this->IDVenta;
    }

    public function setNumeroDias($NumeroDias) {
        $this->NumeroDias = $NumeroDias;
    }

    public function getNumeroDias() {
        return $this->NumeroDias;
    }

    public function setPorcentajeAlta($PorcentajeAlta) {
        $this->PorcentajeAlta = $PorcentajeAlta;
    }

    public function getPorcentajeAlta() {
        return $this->PorcentajeAlta;
    }

    public function setPorcentajeRenovacion($PorcentajeRenovacion) {
        $this->PorcentajeRenovacion = $PorcentajeRenovacion;
    }

    public function getPorcentajeRenovacion() {
        return $this->PorcentajeRenovacion;
    }

    public function setCaracteristicas($Caracteristicas) {
        $this->Caracteristicas = trim($Caracteristicas);
    }

    public function getCaracteristicas() {
        return $this->Caracteristicas;
    }

    public function setUMB($UMB) {
        $this->UMB = $UMB;
    }

    public function getUMB() {
        if (!($this->UMB instanceof UnidadesMedida))
            $this->UMB = new UnidadesMedida($this->UMB);
        return $this->UMB;
    }

    public function setUMC($UMC) {
        $this->UMC = $UMC;
    }

    public function getUMC() {
        if (!($this->UMC instanceof UnidadesMedida))
            $this->UMC = new UnidadesMedida($this->UMC);
        return $this->UMC;
    }

    public function setCUMC($CUMC) {
        $this->CUMC = $CUMC;
    }

    public function getCUMC() {
        return $this->CUMC;
    }

    public function setUMA($UMA) {
        $this->UMA = $UMA;
    }

    public function getUMA() {
        if (!($this->UMA instanceof UnidadesMedida))
            $this->UMA = new UnidadesMedida($this->UMA);
        return $this->UMA;
    }

    public function setCUMA($CUMA) {
        $this->CUMA = $CUMA;
    }

    public function getCUMA() {
        return $this->CUMA;
    }

    public function setUMV($UMV) {
        $this->UMV = $UMV;
    }

    public function getUMV() {
        if (!($this->UMV instanceof UnidadesMedida))
            $this->UMV = new UnidadesMedida($this->UMV);
        return $this->UMV;
    }

    public function setCUMV($CUMV) {
        $this->CUMV = $CUMV;
    }

    public function getCUMV() {
        return $this->CUMV;
    }

}

// END class articulos
?>