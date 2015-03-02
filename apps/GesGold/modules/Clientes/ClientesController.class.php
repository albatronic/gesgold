<?php

/**
 * CONTROLLER FOR Clientes
 * 
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:14

 * Extiende a la clase controller
 */
class ClientesController extends Controller {

    protected $entity = "Clientes";
    protected $parentEntity = "";

    /**
     * Generar el listado de clientes apoyándose en el método padre
     *
     * Muestra los clientes de todas las sucursales, excepto si el usuario
     * es comercial en cuyo caso muestra solo los suyos
     *
     * @return array
     */
    public function listAction() {

        $filtro = "";

        $tabla = $this->form->getDataBaseName() . "." . $this->form->getTable();
        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial())
            $filtro = $tabla . ".IDComercial='" . $usuario->getIDAgente() . "'";
        unset($usuario);

        return parent::listAction($filtro);
    }

    /**
     * Generar el listado de clientes apoyándose en el método padre
     *
     * Muestra los clientes de todas las sucursales, excepto si el usuario
     * es comercial en cuyo caso muestra solo los suyos
     *
     * @return array
     */
    public function listadoAction() {

        $filtro = "";

        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial())
            $filtro = "(IDComercial='" . $usuario->getIDAgente() . "')";
        unset($usuario);

        return parent::listadoAction($filtro);
    }

    /**
     * Establece los parametros de exportacion y se los
     * entrega al método padre del controller principal que es
     * el que reliza el proceso de exportación en base a estos
     * parámetros.
     * 
     * @return array
     */
    public function exportarAction() {

        $filtro = "";

        $usuario = new Agentes($_SESSION['USER']['user']['id']);
        if ($usuario->getEsComercial())
            $filtro = "(IDComercial='" . $usuario->getIDAgente() . "')";
        unset($usuario);
        ;

        $this->values['export'] = array(
            'title' => 'Clientes de la sucursal ' . $_SESSION['suc'],
        );
        return parent::exportarAction($filtro);
    }

    /**
     * Método para cargar clientes desde un fichero externo
     * Se usa solo para iniciar la produccion
     * En el cliclo de vida de la aplicacion no se usa
     * 
     * @return void
     */
    public function CargaAction() {
        set_time_limit(0);
        $handle = @fopen("tmp/clientes.txt", "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $cliente = explode("\t", $buffer);
                $c = new Clientes($cliente[0]);
                $c->setRazonSocial($cliente[1]);
                $c->setCif($cliente[2]);
                $c->setTelefono($cliente[3]);
                $c->setDireccion($cliente[4]);
                $c->setPoblacion($cliente[5]);
                $c->setFechaNacimiento($cliente[6]);
                $c->setCodigoPostal($cliente[9]);
                $c->setFax($cliente[11]);
                $c->setIDComercial(2);
                $c->create();
            }

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }

}

?>