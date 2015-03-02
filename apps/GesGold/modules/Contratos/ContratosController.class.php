<?php

/**
 * CONTROLLER FOR Contratos
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 12.04.2012 17:43:58

 * Extiende a la clase controller
 */
class ContratosController extends Controller {

    protected $entity = "Contratos";
    protected $parentEntity = "";

    /**
     * Confirma el contrato, que consiste en:
     *   1.- Marcarlo como finalizado
     *   2.- Generar el movimiento de almacen de entrada de producto
     *   3.- Generar el apunte en caja para el pago del mismo
     *
     * @return <type>
     */
    public function ConfirmarAction() {

        if ( ($this->values['permisos']['A']) and ($this->request['METHOD'] == 'POST')) {

            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            if ($datos->validaPago($this->request['pago']))
                $datos->confirma($this->request['pago']);
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Anula la confirmacion, que consiste en:
     *   1.- Marcarlo como no confirmado
     *   2.- Generar el movimiento de almacen de anulacion de producto
     *   3.- Borrar el apunte de caja
     *
     * @return array template, values
     */
    public function AnularAction() {

        if ( ($this->values['permisos']['A']) and ($this->request['METHOD'] == 'POST')) {

            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $datos->anula();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Renueva el contrato de empeño. Consiste en:
     *
     *   1.- Apunte en el histórico del contrato
     *   2.- Apunte en caja entrando el importe de la renovación
     *
     * @return array template, values
     */
    public function RenovarAction() {

        if ( ($this->values['permisos']['A']) and ($this->request['METHOD'] == 'POST')) {

            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $datos->renueva();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Recuperación el contrato de empeño. Consiste en:
     *
     *   1.- Apunte en el histórico del contrato
     *   2.- Apunte en caja entrando el importe de la recuperación
     *   3.- Movimiento de almacén sacando el producto
     *
     * @return array template, values
     */
    public function RecuperarAction() {

        if ( ($this->values['permisos']['A']) and ($this->request['METHOD'] == 'POST')) {

            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $datos->recupera();
            $this->values['errores'] = $datos->getErrores();
            $this->values['alertas'] = $datos->getAlertas();
            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $this->values['datos'] = $datos;
            unset($datos);
            return array('template' => $this->entity . '/edit.html.twig', 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Envia por email el contrato en formato PDF
     * @return <type>
     */
    public function enviarAction() {

        if ($this->request['accion'] == 'Enviar') {

            $para = $this->request['Para'];
            $de = $this->request['De'];
            $deNombre = $this->request['DeNombre'];
            $asunto = $this->request['Asunto'];
            $mensaje = $this->request['Mensaje'];
            $adjuntos = array($this->request['Adjunto'],);

            $envio = new Mail();
            $this->values['resultadoEnvio'] = $envio->send($para, $de, $deNombre, $asunto, $mensaje, $adjuntos);
            unset($envio);
        } else {
            $datos = new Contratos($this->request['Contratos']['IDContrato']);
            $formatos = DocumentoPdf::getFormatos('contratos');
            $formato = $this->request['Formato'];
            if ($formato == '')
                $formato = 0;

            $this->values['archivo'] = $this->generaPdf('contratos', array('0' => $datos->getIDContrato()), $formato);
            $this->values['email'] = array(
                'Para' => $datos->getIDCliente()->getEMail(),
                'De' => $datos->getIDComercial()->getEMail(),
                'DeNombre' => $datos->getIDComercial()->getNombre(),
                'Cc' => '',
                'Asunto' => 'Contrato n. ' . $datos->getNumeroContrato(),
                'Formatos' => $formatos,
                'Formato' => $formato,
                'Mensaje' => 'Le adjunto el contrato ' . $datos->getNumeroContrato() . "\n\nUn saludo.",
                'idContrato' => $datos->getIDContrato(),
            );
        }

        return parent::enviarAction();
    }

    /**
     * Genera un array con la informacion necesaria para imprimir el documento
     * Recibe un array con los ids de albaran
     * No muestra las lineas cuyas unidades son cero.
     *
     * @param array $idsDocumento Array con los ids de albaranes
     * @return array Array con dos elementos: master es un objeto albaran y detail es un array de objetos lineas de albaran
     */
    protected function getDatosDocumento(array $idsDocumento) {

        $master = array();
        $detail = array();

        // Recorro el array de los contratos a imprimir
        foreach ($idsDocumento as $key => $idDocumento) {
            // Instancio la cabecera del contrato
            $master[$key] = new Contratos($idDocumento);
            $detail[$key] = array('0' => $master[$key]);
        }

        return array(
            'master' => $master,
            'detail' => $detail,
        );
    }

    /**
     * Activa el código de tpv utilizado
     * @return <type>
     */
    public function setTpvEditAction() {

        $_SESSION['tpv'] = $this->request['IDTpv'];
        $this->request["METHOD"] = "GET";
        $this->request[2] = $this->request['Contratos']['IDContrato'];

        return parent::editAction();
    }

    /**
     * Activa el código de tpv utilizado
     * @return <type>
     */
    public function setTpvNewAction() {

        $_SESSION['tpv'] = $this->request['IDTpv'];
        $this->request["METHOD"] = "GET";

        return parent::newAction();
    }

}

?>