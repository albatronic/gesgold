<?php

/**
 * Description of Almacenes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Almacenes extends AlmacenesEntity {

    public function __toString() {
        return $this->getNombre();
    }

    /**
     * Devuelve un array con todas las ubicaciones del almacén
     * El array es del tipo array('Id' => , 'Value' => )
     * @return array Las ubicaciones del almacen
     */
    public function getUbicaciones($filtroUbicacion="%") {
        $ubicaciones = array();

        $mapa = new AlmacenesMapas();
        $ubicaciones = $mapa->fetchAll($this->IDAlmacen, $filtroUbicacion);
        unset($mapa);

        return $ubicaciones;
    }

    /**
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * OJO!!! ESTE METODO NO SE USA EN PRINCIPIO. HAY QUE REVISAR EL QUERY
     * YA QUE SE USAN TABLAS DE DIFERENTES BASES DE DATOS Y PARACE QUE NO ESTA BIEN
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     *
     * Devuelve un array con las ubicaciones disponibles en el almacén
     * Se entiende por ubicacion libre aquella que no tiene existencias
     * El array es del tipo array('Id' => , 'Value' => )
     * @return array Con las ubicaciones libres
     */
    public function getUbicacionesLibres() {

        $huecos = array();

        // LLamo al procedimiento almacenado 'UbicacionesLibres'
        $em = new EntityManager("datos" . $_SESSION['emp']);
        if ($em->getDbLink()) {
            //$query = "Call UbicacionesLibres('{$this->IDAlmacen}');";
            $query = "SELECT IDUbicacion as Id, Ubicacion as Value
                        FROM {$this->_dataBaseName}.almacenes_mapas
                        WHERE
                            IDAlmacen = '{$this->IDAlmacen}' AND
                            IDUbicacion NOT IN (
                                SELECT t1.IDUbicacion
                                FROM existencias as t1
                                GROUP BY t1.IDAlmacen, t1.IDUbicacion
                                HAVING t1.IDAlmacen =  '{$this->IDAlmacen}'
                                AND SUM( t1.Reales ) > 0
                            )
                        ORDER BY Ubicacion";
            $em->query($query);
            $huecos = $em->fetchResult();
            $em->desConecta();
        }
        unset($em);

        return $huecos;
    }

}

?>
