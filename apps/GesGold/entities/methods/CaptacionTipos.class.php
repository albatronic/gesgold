<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 25.04.2012 12:27:12
 */

/**
 * @orm:Entity(captacion_tipos)
 */
class CaptacionTipos extends CaptacionTiposEntity {
	public function __toString() {
		return $this->getCaptacion();
	}
}
?>