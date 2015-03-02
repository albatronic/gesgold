<?php

/**
 * Description of Inventarios
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Inventarios extends InventariosEntity {

    public function __toString() {
        return $this->getId();
    }

}

?>
