<?php

/**
 * Description of Agencias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Agencias extends AgenciasEntity {

    public function __toString() {
        return $this->getAgencia();
    }

}

?>
