<?php

/**
 * Description of IndexController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 28-may-2011
 *
 */
class IndexController extends Controller {

    protected $entity = "Index";
    protected $parentEntity = "";

    /**
     * Muestra un template con los accesos favoritos del usuario
     * @return array
     */
    public function IndexAction() {
        // QUITAR LOS COMENTARIOS PARA MOSTAR LOS FAVORITOS
        //$this->values = array('favoritos' => $this->getTopFavoritos());

        $this->values['sucursal'] = new Sucursales($_SESSION['suc']);
        return array('template' => 'Index/index.html.twig', 'values' => $this->values);
    }

    /**
     * Acciones a realizar cuando se selecciona otra empresa
     * Se cambia el valor de la variable de session 'emp'
     * y se recargan las sucursales de la nueva empresa.
     * @return
     */
    public function EmpresaAction() {
        //Activo la empresa nueva
        $_SESSION['emp'] = $this->request['Empresa'];

        //Buscar las sucursales de la nueva empresa seleccionada
        $user = new Agentes($_SESSION['USER']['user']['id']);
        $_SESSION['USER']['sucursales'] = $user->getSucursales($_SESSION['emp']);

        //Activo la sucursal nueva
        $_SESSION['suc'] = $_SESSION['USER']['sucursales'][0]['Id'];

        //Activo la version
        $empresa = new Empresas($_SESSION['emp']);
        $_SESSION['ver'] = $empresa->getIDVersion()->getIDTipo();
        unset($empresa);

        //Desactivo el tpv para forzar a la elección de un nuevo
        unset($_SESSION['tpv']);

        $this->values['sucursal'] = new Sucursales($_SESSION['suc']);
        return array('template' => 'Index/index.html.twig', 'values' => $this->values);
    }

    /**
     * Acciones a realizar cuando se selecciona otra sucursal
     * Se cambia el valor de la variable de session 'suc'
     * @return
     */
    public function SucursalAction() {

        $_SESSION['suc'] = $this->request['Sucursal'];
        
        //Desactivo el tpv para forzar a la elección de un nuevo
        unset($_SESSION['tpv']);

        $this->values['sucursal'] = new Sucursales($_SESSION['suc']);
        return array('template' => 'Index/index.html.twig', 'values' => $this->values);
    }

    /**
     * Activa el código de tpv utilizado
     * @return <type>
     */
    public function setTpvAction() {

        $_SESSION['tpv'] = $this->request['IDTpv'];
        return $this->IndexAction();
    }

}

?>
