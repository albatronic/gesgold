<?php

/**
 * Description of RecibosProveedores
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class RecibosProveedores extends RecibosProveedoresEntity {

    public function __toString() {
        return $this->getIDRecibo();
    }

}

?>
