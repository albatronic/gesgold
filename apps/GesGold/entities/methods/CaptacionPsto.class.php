<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 25.04.2012 12:27:39
 */

/**
 * @orm:Entity(captacion_psto)
 */
class CaptacionPsto extends CaptacionPstoEntity {
	public function __toString() {
		return $this->getId();
	}
}
?>