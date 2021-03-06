<?php
/**
* CONTROLLER FOR CaptacionPsto
* @author Sergio Perez <sergio.perez@albatronic.com>
* @copyright INFORMATICA ALBATRONIC SL 
* @since 25.04.2012 12:27:39

* Extiende a la clase controller
*/

class CaptacionPstoController extends Controller {

	protected $entity = "CaptacionPsto";
	protected $parentEntity = "CaptacionTipos";


    public function listAction($idCaptacion='') {

        if ($idCaptacion == '')
            $idCaptacion = $this->request[2];

        $objetoNuevo = new $this->entity();
        $objetoNuevo->setIDCaptacion($idCaptacion);
        $lineas[] = $objetoNuevo;
        unset($objetoNuevo);

        $lis = new $this->entity();
        $rows = $lis->cargaCondicion('Id', "IDCaptacion = '" . $idCaptacion . "'", 'Ano DESC');
        foreach ($rows as $row) {
            $lineas[] = new $this->entity($row['Id']);
        }

        $template = $this->entity . '/form.html.twig';

        $this->values['linkBy']['value'] = $idCaptacion;
        $this->values['listado']['data'] = $lineas;

        unset($lis);
        unset($lineas);

        return array('template' => $template, 'values' => $this->values);
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
                    return $this->listAction($this->values['linkBy']['value']);
                    break;
            }
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Edita, actualiza o borrar un registro
     *
     * Viene siempre por POST
     * Actualiza o Borrar según el valor de $this->request['accion']
     *
     * @return array con el template y valores a renderizar
     */
    public function editAction() {

        //COGER DEL REQUEST EL LINK A LA ENTIDAD PADRE
        if ($this->values['linkBy']['id'] != '') {
            $this->values['linkBy']['value'] = $this->request[$this->entity][$this->values['linkBy']['id']];
        }

        switch ($this->request['accion']) {
            case 'G': //GUARDAR DATOS
                if ($this->values['permisos']['A']) {
                    $datos = new $this->entity();
                    $datos->bind($this->request[$this->entity]);
                    if ($datos->valida($this->form->getRules())) {
                        $datos->save();
                        $this->values['errores'] = $datos->getErrores();
                        $this->values['alertas'] = $datos->getAlertas();

                        //Recargo el objeto para refrescar las propiedas que
                        //hayan podido ser motivo de algun calculo durante el proceso
                        //de guardado.
                        $datos = new $this->entity($this->request[$this->entity][$datos->getPrimaryKeyName()]);
                    }

                    $this->values['datos'] = $datos;

                    return $this->listAction($this->values['linkBy']['value']);
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;

            case 'B': //BORRAR DATOS
                if ($this->values['permisos']['B']) {
                    $datos = new $this->entity();
                    $datos->bind($this->request[$this->entity]);

                    if ($datos->erase()) {
                        $datos = new $this->entity();
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = array();
                    } else {
                        $this->values['datos'] = $datos;
                        $this->values['errores'] = $datos->getErrores();
                    }
                    return $this->listAction($this->values['linkBy']['value']);
                } else {
                    return array('template' => '_global/forbiden.html.twig');
                }
                break;
        }
    }
}
?>