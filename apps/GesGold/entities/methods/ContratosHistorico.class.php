<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 08.08.2012 18:49:32
 */

/**
 * @orm:Entity(contratos_historico)
 */
class ContratosHistorico extends ContratosHistoricoEntity {
	public function __toString() {
		return $this->getId();
	}
}
?>