<?php
/**
 * Define los tipos de operaciones de los contratos
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 08-08-2012
 *
 */

class OperacionesContratos extends Tipos {

    protected $tipos = array(
        array('Id' => '0', 'Value' => 'Compra'),
        array('Id' => '1', 'Value' => 'Venta'),
        array('Id' => '2', 'Value' => 'Empeño'),
        array('Id' => '3', 'Value' => 'Renovación'),
        array('Id' => '4', 'Value' => 'Recuperación'),
        array('Id' => '5', 'Value' => 'Pérdida'),
        array('Id' => '6', 'Value' => 'Intervenido'),
        array('Id' => '7', 'Value' => 'Despiece'),
        array('Id' => '8', 'Value' => 'Anulado'),
    );
}
?>
