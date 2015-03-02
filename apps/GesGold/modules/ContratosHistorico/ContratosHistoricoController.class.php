<?php

/**
 * CONTROLLER FOR ContratosHistorico
 * @author: Sergio Perez <sergio.perez@albatronic.com>
 * @copyright: INFORMATICA ALBATRONIC SL
 * @date 08.08.2012 18:49:32

 * Extiende a la clase controller
 */
class ContratosHistoricoController extends Controller {

    protected $entity = "ContratosHistorico";
    protected $parentEntity = "Contratos";

    public function __construct($request) {
        // Cargar lo que viene en el request
        $this->request = $request;

        // Cargar la configuracion del modulo (modules/moduloName/config.yaml)
        $this->form = new Form($this->entity);

        $this->permisos = new ControlAcceso();
        $this->values['permisos'] = $this->permisos->getPermisos();
        $this->values['request'] = $this->request;
        $this->values['linkBy'] = array(
            'id' => 'IDContrato',
            'value' => '',
        );
    }

    /**
     * Crea un registro nuevo
     *
     * Siempre viene por POST
     * Si viene por POST crea un registro
     *
     * @return array con el template y valores a renderizar
     */
    public function newAction() {
        if ($this->values['permisos']['I']) {
            switch ($this->request["METHOD"]) {

                case 'POST': //CREAR NUEVO REGISTRO
                    //COGER EL LINK A LA ENTIDAD PADRE
                    if ($this->values['linkBy']['id'] != '') {
                        $this->values['linkBy']['value'] = $this->request[$this->entity][$this->values['linkBy']['id']];
                    }

                    $datos = new $this->entity();
                    $datos->bind($this->request[$this->entity]);

                    if ($datos->valida($this->form->getRules())) {
                        $datos->create();
                        $this->values['alertas'] = $datos->getAlertas();
                        $this->values['errores'] = $datos->getErrores();

                        //Recargo el objeto para refrescar las propiedas que
                        //hayan podido ser objeto de algun calculo durante el proceso
                        //de guardado.
                        $datos = new $this->entity($datos->getPrimaryKeyValue());
                        $this->values['datos'] = $datos;
                    } else {
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = $datos->getErrores();
                    }
                    unset($datos);
                    return $this->listAction($this->values['linkBy']['value']);
                    break;
            }
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    public function listAction($idContrato = '') {

        if ($this->values['permisos']['C']) {
            if ($idContrato == '')
                $idContrato = $this->request[2];

            $objetoNuevo = new $this->entity();
            $objetoNuevo->setIDContrato($idContrato);
            $objetoNuevo->setFecha(date('dmY'));
            $lineas[] = $objetoNuevo;
            unset($objetoNuevo);

            $lis = new $this->entity();
            $rows = $lis->cargaCondicion('Id', "IDContrato = '" . $idContrato . "'", 'Fecha,Id ASC');
            foreach ($rows as $row) {
                $lineas[] = new $this->entity($row['Id']);
            }

            $template = $this->entity . '/form.html.twig';

            $this->values['linkBy']['value'] = $idContrato;
            $this->values['listado']['data'] = $lineas;

            unset($lis);
            unset($lineas);

            return array('template' => $template, 'values' => $this->values);
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>