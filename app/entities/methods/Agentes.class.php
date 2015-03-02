<?php

/**
 * Description of Agentes
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 04-nov-2011
 *
 */
class Agentes extends AgentesEntity {

    public function __toString() {
        if ($this->Nombre)
            return $this->getNombre();
        else
            return "";
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {
        $this->setPassword(md5($this->getPassword()));
        $this->setQuien(md5($this->getPassword() . "Pablo"));

        parent::save();
    }

    /**
     * Crea un registro (insert)
     */
    public function create() {
        $this->setPassword(md5($this->getPassword()));
        $this->setQuien(md5($this->getPassword() . "Pablo"));

        return parent::create();
    }

    /**
     * Devuelve un array con todas las empresas
     * a las que tiene acceso el usuario
     * @return array
     */
    public function getEmpresas() {
        if ($this->IDEmpresa < 1) { //Puede acceder a todas
            $empresa = $this->getIDEmpresa();
            $empresas = $empresa->fetchAll('RazonSocial');
        } else { //Puede acceder solo a una
            $empresa = $this->getIDEmpresa();
            $empresas[] = array(
                'Id' => $empresa->getIDEmpresa(),
                'Value' => $empresa->getRazonSocial(),
            );
        }
        return $empresas;
    }

    /**
     * Devuelve un array con todas las sucursales de la empresa indicada
     * a las que tiene acceso el usuario logeado
     * Si no se indica empresa, se toma la actual: $_SESSION['emp']
     *
     * @param integer $idEmpresa
     * @return array
     */
    public function getSucursales($idEmpresa='') {

        if ($idEmpresa == '')
            $idEmpresa = $_SESSION['emp'];

        if ($this->IDSucursal < 1) { //Puede acceder a todas
            $em = new entityManager("empresas");
            $link = $em->getDbLink();

            if (is_resource($link)) {
                $query = "select IDSucursal as Id, Nombre as Value from sucursales where IDEmpresa='" . $idEmpresa . "'";
                $em->query($query);
                $sucursales = $em->fetchResult();
                $em->desConecta();
            }
        } else { //Puede acceder solo a una
            $sucursal = $this->getIDSucursal();
            $sucursales[] = array(
                'Id' => $sucursal->getIDSucursal(),
                'Value' => $sucursal->getNombre(),
            );
        }
        return $sucursales;
    }

}

?>
