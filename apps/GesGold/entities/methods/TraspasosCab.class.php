<?php

/**
 * Description of TraspasosCab
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class TraspasosCab extends TraspasosCabEntity {

    public function __toString() {
        return $this->getIDTraspaso();
    }

    /**
     * Borra un traspaso y sus líneas
     * Siempre que esté en estado 0 (elaboracion) y no esté traspasado
     */
    public function erase() {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "DELETE FROM traspasos_cab WHERE `IDTraspaso`='{$this->IDTraspaso}' AND IDEstado='0'";
            if ($this->_em->query($query)) {
                //Borrar líneas de traspasos
                $query = "DELETE FROM traspasos_lineas where `IDTraspaso`='{$this->IDTraspaso}'";
                if (!$this->_em->query($query))
                    $this->_errores = $this->_em->getError();
            } else
                $this->_errores = $this->_em->getError();
            $this->_em->desConecta();
        }
        unset($this->_em);
    }

    /**
     * Calcular el peso, volumen, n. de bultos y costo de los productos inventariables
     * Actualiza las propiedades de totales pero no salva los datos.
     * IMPORTANTE: Para que los calculos tomen efecto hay que llamar al método save()
     */
    public function recalcula() {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "select sum(articulos.Peso*traspasos_lineas.Unidades) as Peso,
                sum(articulos.volumen*traspasos_lineas.Unidades) as Volumen,
                sum(Unidades) as Bultos,
                sum(articulos.Pvd*traspasos_lineas.Unidades) as Costo
                from articulos,traspasos_lineas
                where (traspasos_lineas.IDArticulo=articulos.IDArticulo) and (articulos.Inventario='1') and (traspasos_lineas.IDTraspaso='" . $this->getIDTraspaso() . "')";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);

            $this->setPeso($row[0]['Peso']);
            $this->setVolumen($row[0]['Volumen']);
            $this->setBultos($row[0]['Bultos']);
            $this->setImporteCosto($row[0]['Costo']);
        }
    }

    /**
     * Devuelve un array con todos los registros
     * Cada elemento tiene la primarykey y el valor de $column
     */
    public function fetchAll($column="FechaSalida") {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "SELECT IDTraspaso as Id,$column as Value FROM traspasos_cab ORDER BY $column ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }

}

?>
