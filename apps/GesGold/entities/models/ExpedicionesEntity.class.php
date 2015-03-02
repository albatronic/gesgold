<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 20.02.2012 01:05:59
 */

/**
 * @orm:Entity(expediciones)
 */
class ExpedicionesEntity extends Entity {

    /**
     * @orm:GeneratedValue
     * @orm:Id
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $IDLinea;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     * @var entities\AlbaranesCab
     */
    protected $IDAlbaran = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     * @var entities\AlbaranesLineas
     */
    protected $IDLineaAlbaran = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     * @var entities\Almacenes
     */
    protected $IDAlmacen = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     * @var entities\Agentes
     */
    protected $IDAlmacenero = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     * @var entities\Agentes
     */
    protected $IDRepartidor = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $Unidades = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $Kilos = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $Cajas = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $Pales = '0.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     * @var entities\Lotes
     */
    protected $IDLote = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     * @var entities\AlmacenesMapas
     */
    protected $IDUbicacion = '0';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $Temperatura = '-20.00';
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="expediciones")
     */
    protected $Expedida = '0';
    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = 'datos#';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'expediciones';
    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDLinea';
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
        'AlbaranesCab',
        'AlbaranesLineas',
        'Almacenes',
        'Agentes',
        'Lotes',
        'AlmacenesMapas',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDLinea($IDLinea) {
        $this->IDLinea = $IDLinea;
    }

    public function getIDLinea() {
        return $this->IDLinea;
    }

    public function setIDAlbaran($IDAlbaran) {
        $this->IDAlbaran = $IDAlbaran;
    }

    public function getIDAlbaran() {
        if (!($this->IDAlbaran instanceof AlbaranesCab))
            $this->IDAlbaran = new AlbaranesCab($this->IDAlbaran);
        return $this->IDAlbaran;
    }

    public function setIDLineaAlbaran($IDLineaAlbaran) {
        $this->IDLineaAlbaran = $IDLineaAlbaran;
    }

    public function getIDLineaAlbaran() {
        if (!($this->IDLineaAlbaran instanceof AlbaranesLineas))
            $this->IDLineaAlbaran = new AlbaranesLineas($this->IDLineaAlbaran);
        return $this->IDLineaAlbaran;
    }

    public function setIDAlmacen($IDAlmacen) {
        $this->IDAlmacen = $IDAlmacen;
    }

    public function getIDAlmacen() {
        if (!($this->IDAlmacen instanceof Almacenes))
            $this->IDAlmacen = new Almacenes($this->IDAlmacen);
        return $this->IDAlmacen;
    }

    public function setIDAlmacenero($IDAlmacenero) {
        $this->IDAlmacenero = $IDAlmacenero;
    }

    public function getIDAlmacenero() {
        if (!($this->IDAlmacenero instanceof Agentes))
            $this->IDAlmacenero = new Agentes($this->IDAlmacenero);
        return $this->IDAlmacenero;
    }

    public function setIDRepartidor($IDRepartidor) {
        $this->IDRepartidor = $IDRepartidor;
    }

    public function getIDRepartidor() {
        if (!($this->IDRepartidor instanceof Agentes))
            $this->IDRepartidor = new Agentes($this->IDRepartidor);
        return $this->IDRepartidor;
    }

    public function setUnidades($Unidades) {
        $this->Unidades = $Unidades;
    }

    public function getUnidades() {
        return $this->Unidades;
    }

    public function setKilos($Kilos) {
        $this->Kilos = $Kilos;
    }

    public function getKilos() {
        return $this->Kilos;
    }

    public function setCajas($Cajas) {
        $this->Cajas = $Cajas;
    }

    public function getCajas() {
        return $this->Cajas;
    }

    public function setPales($Pales) {
        $this->Pales = $Pales;
    }

    public function getPales() {
        return $this->Pales;
    }

    public function setIDLote($IDLote) {
        $this->IDLote = $IDLote;
    }

    public function getIDLote() {
        if (!($this->IDLote instanceof Lotes))
            $this->IDLote = new Lotes($this->IDLote);
        return $this->IDLote;
    }

    public function setIDUbicacion($IDUbicacion) {
        $this->IDUbicacion = $IDUbicacion;
    }

    public function getIDUbicacion() {
        if (!($this->IDUbicacion instanceof AlmacenesMapas))
            $this->IDUbicacion = new AlmacenesMapas($this->IDUbicacion);
        return $this->IDUbicacion;
    }

    public function setTemperatura($Temperatura) {
        $this->Temperatura = $Temperatura;
    }

    public function getTemperatura() {
        return $this->Temperatura;
    }

    public function setExpedida($Expedida) {
        $this->Expedida = $Expedida;
    }

    public function getExpedida() {
        return $this->Expedida;
    }

}

// END class expediciones
?>