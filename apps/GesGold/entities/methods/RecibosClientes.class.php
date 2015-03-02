<?php

/**
 * Description of RecibosClientes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class RecibosClientes extends RecibosClientesEntity {

    public function __toString() {
        return $this->getIDRecibo();
    }

}

?>
