<?php

/**
 * Description of Lotes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Lotes extends LotesEntity {

    public function __toString() {
        return $this->getLote();
    }

    /**
     * Devuelve un array con todos los lotes del articulo indicado (opcional)
     * ordenados por fecha de caducidad ascendente (el más cercano a caducar, primero)
     * Cada elemento tiene la primarykey y el valor de $column
     *
     * @param integer $idArticulo El id de articulo (opcional)
     * @param string $column La columna a devolver. Por defecto "Lote"
     * @return array
     */
    public function fetchAll($idArticulo='', $column='Lote', $filtroDescripcion='%') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            if ($idArticulo != '')
                $filtro = "(IDArticulo='" . $idArticulo . "')";
            else
                $filtro = "(1)";
            $filtro .= " AND Lote LIKE '{$filtroDescripcion}'";
            $query = "SELECT IDLote as Id, $column as Value FROM lotes WHERE $filtro ORDER BY FechaCaducidad ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        return $rows;
    }

    public function valida(array $rules) {
        $validacion = parent::valida($rules);
        if ($validacion) {
            if ($this->FechaCaducidad <= $this->FechaFabricacion) {
                //Calcular la fecha de caducidad en base a la de fabricacion
                //y el número de días de caducidad del artículo
                $articulo = new Articulos($this->IDArticulo);
                $date = new Fecha($this->FechaFabricacion);
                $this->FechaCaducidad = $date->sumaDias($articulo->getCaducidad());
                unset($articulo);
                unset($date);
            }
        }
        return $validacion;
    }

    /**
     * Devuelve un array con todas las ubicaciones del $idAlmacen
     * donde hay existencias del lote
     *
     * @param integer $idLote
     * @return array Las ubicaciones
     */
    public function getUbicaciones($idAlmacen, $filtroUbicacion="%") {
        $ubicaciones = array();

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            //$query = "Call UbicacionesLote('{$idAlmacen}','{$this->IDLote}','{$filtroUbicacion}');";
            $query = "SELECT DISTINCT e.IDUbicacion AS Id, m.Ubicacion AS Value
                        FROM
                            {$this->_dataBaseName}.existencias e,
                            interpra_ppuemp.almacenes_mapas m
                        WHERE
                            e.IDUbicacion = m.IDUbicacion AND
                            e.IDAlmacen= '{$idAlmacen}' AND
                            e.IDLote= '{$this->IDLote}' AND
                            e.Reales > 0 AND
                            m.Ubicacion LIKE '{$filtroUbicacion}'
                        ORDER BY m.Ubicacion";
            $this->_em->query($query);
            $ubicaciones = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }

        return $ubicaciones;
    }

}

?>
