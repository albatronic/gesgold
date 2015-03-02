<?php

/**
 * Description of ZonasTransporte
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class ZonasTransporte extends ZonasTransporteEntity {

    public function __toString() {
        return $this->getZona();
    }

}

?>
