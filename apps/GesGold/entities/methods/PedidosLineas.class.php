<?php

/**
 * Description of PedidosLineas
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class PedidosLineas extends PedidosLineasEntity {

    public function __toString() {
        return $this->getIDLinea();
    }

    /**
     * Guarda la informacion (update)
     */
    public function save() {

        parent::save();

        //RECALCULAR EL PEDIDO SI ESTA EN EL ESTADO INICIAL
        if ($this->getIDPedido()->getIDEstado()->getIDTipo() == 0)
            $this->getIDPedido()->save();
    }

    /**
     * Crea un registro (insert)
     */
    public function create() {

        $lastId = parent::create();
        if ($lastId != NULL) {
            //RECALCULAR EL PEDIDO
            $this->getIDPedido()->save();
        }
        return $lastId;
    }

    /**
     * Si está en estado de PTE. CONFIRMAR (0)
     * Borra una linea de pedido, después recalculo y guardo el pedido
     */
    public function erase() {

        if ($this->IDEstado == 0) {

            parent::erase();

            // Recalculo el pedido
            $this->getIDPedido()->save();
        }
    }

    /**
     * Validaciones antes de actualizar o crear
     */
    public function valida() {
        unset($this->_errores);

        //Para tener disponibles los datos de la
        //cabecera del pedido
        $pedido = new PedidosCab($this->IDPedido);

        // Compruebo la existencia del artículo y que pertenezca a
        // la sucursal de la cabecera del pedido.
        if ($this->IDArticulo == '') {
            $this->_errores[] = "Debe indicar un código de artículo";
        } else {
            $articulo = new Articulos($this->IDArticulo);

            if ($articulo->getStatus() == 0) {
                $this->_errores[] = "El artículo indicado no existe";
                unset($articulo);
            }
        }

        // Si existe el articulo ...
        if (count($this->_errores) == 0) {

            $this->checkPackingCompras($articulo);

            // Si la descripcion está vacia, pongo la del artículo
            // Si trae algo, la respeto.
            if ($this->Descripcion == '')
                $this->setDescripcion($articulo->getDescripcion());

            // Si no se ha indicado precio, pongo Pvd de la ficha del articulo
            if ($this->Precio == '')
                $this->setPrecio($articulo->getPrecioCosto());

            // Totalizar la linea de pedido
            $this->setImporte($this->Unidades * $this->Precio * (1 - $this->Descuento / 100));

            //Comparar el precio respecto al PMC.
            //Si el importe neto (despues de dctos) unitario supera el P.M.C. se emite alerta
            if (($this->getImporte() / $this->getUnidades()) > $articulo->getPmc())
                $this->_alertas[] = "El precio supera al Precio Medio de Compra: " . $articulo->getPmc();

            // Poner el mismo almacen de la cabecera del pedido y el agente en curso
            $this->setIDAlmacen($pedido->getIDAlmacen()->getIDAlmacen());
            $this->setIDAgente($_SESSION['USER']['user']['id']);

            // Si el proveedor no está sujeto a Iva, pongo a 0 el iva y el recargo
            if ($pedido->getIDProveedor()->getIva()->getIDTipo() == '0') {
                $this->setIva('0');
                $this->setRecargo('0');
            } else {
                // Si no se ha indicando iva, pongo el iva asociado al artículo,
                if ($this->Iva == '')
                    $this->setIva($articulo->getIDIva()->getIva());
            }

            // Si el artículo es inventariable:
            // Comprobar existencias sin tener en cuenta lote ni ubicación
            // de almacen. Solo se buscan existencias en el almacen indicado
            // en la línea del pedido
            if ($articulo->getInventario()->getIDTipo() == '1') {
                $existencias = new Existencias();
                $stock = $existencias->getStock($this->IDArticulo, $this->IDAlmacen);

                // Se genera una alerta si hay stock disponible o pendiente de entrada
                if (($stock['PE'] > 0) or ($stock['DI'] > 0)) {
                    $this->_alertas[] = "Hay stock";
                    $this->_alertas[] = "---------";
                }
                $this->_alertas[] = "Disponible: " . $stock['DI'];
                $this->_alertas[] = "Real: " . $stock['RE'];
                $this->_alertas[] = "Reservado: " . $stock['RV'];
                $this->_alertas[] = "Pte. Entrada: " . $stock['PE'];
            }
        }

        unset($articulo);
        unset($pedido);
        unset($existencias);

        return ( count($this->_errores) == 0 );
    }

    /**
     * Valida antes del borrado
     * Devuelve TRUE o FALSE
     * Si hay errores carga el array $this->_errores
     * @return boolean
     */
    public function validaBorrado() {
        unset($this->_errores);
        if ($this->IDEstado != 0) {
            $this->_errores[] = "No se puede borrar la línea. Está confirmada o recepcionada";
        }
        return (count($this->_errores) == 0);
    }

    /**
     * Recepciona la línea de pedido.
     * Pasa las existencias del estado "Entrando" (1) al estado "Recepionada" (2)
     * y marca la línea como Recepcionada.
     * Las unidades recibidas pueden ser diferentes a las recibidas.
     *
     * @return boolean
     */
    public function recepciona() {

        $ok = true;

        $exi = new Existencias();

        //Quitar el 'Entrando' de las unidades pedidas sin indicar lote
        $valores = array(
            'UM' => 'UMC',
            'Reales' => 0,
            'Pales' => 0,
            'Cajas' => 0,
            'Reservadas' => 0,
            'Entrando' => -1 * $this->getUnidades(),
        );
        $exi->actualiza($this->IDAlmacen, $this->IDArticulo, 0, 0, $this->getIDPedido()->getDeposito()->getIDTipo(), $valores);
        $ok = (count($exi->getErrores()) == 0);
        unset($exi);

        if ($ok) {
            // Recorrer las lineas de recepcion actualizando las existencias y
            // marcándolas como recepcionadas
            $unidadesNetas = 0;
            $recepcion = new Recepciones();
            $rows = $recepcion->cargaCondicion("*", "IDLineaPedido='{$this->IDLinea}' and Recepcionada='0'");
            foreach ($rows as $row) {
                $valores = array(
                    'UM' => 'UMC',
                    'Reales' => $row['UnidadesNetas'],
                    'Pales' => $row['Pales'],
                    'Cajas' => $row['Cajas'],
                    'Reservadas' => 0,
                    'Entrando' => 0,
                );
                $mvtoAlmacen = new MvtosAlmacen();
                $ok = $mvtoAlmacen->genera(
                                'PedidosCab',
                                'E', // Entrada por compras
                                $this->getIDPedido()->getIDPedido(), // El id del pedido
                                $row['IDAlmacen'], // El id del almacen
                                $this->IDArticulo, // El id del articulo
                                $row['IDLote'], // El id del lote
                                $row['IDUbicacion'], // El id de la ubicacion
                                $this->getIDPedido()->getDeposito()->getIDTipo(), // Flag de deposito
                                $valores);                              // Valores con los que actualizar

                $unidadesNetas += $row['UnidadesNetas'];

                if ($ok) {
                    // Marcar la linea de recepcion como recepcionada.
                    $recepcion = new Recepciones($row['IDLinea']);
                    $recepcion->setRecepcionada(1);
                    $recepcion->save();
                } else break;
            }
            unset($mvtoAlmacen);
            unset($recepcion);
        }

        if ($ok) {
            // Marcar la línea de pedido como recepcionada y
            // poner las unidades netas recibidas y las unidades pendiente de facturar
            $this->setIDEstado(2);
            $this->setUnidadesRecibidas($unidadesNetas);
            $this->setUnidadesPtesFacturar($unidadesNetas);
            $this->save();
        }

        return $ok;
    }

    /**
     * Comprueba la unidad minima de compra
     * @param Articulos $articulo El objeto articulo
     */
    public function checkPackingCompras(Articulos $articulo) {
        // Comprobar la unidad mínina de compra (PackingCompras)
        $packing = $articulo->getPackingCompras();
        if ($this->Unidades < $packing) {
            $this->setUnidades($packing);
            $this->_alertas[] = "Unidad Mínima de Compra " . $packing;
        } elseif ($this->Unidades > $packing) {
            // Compruebo multiplo, redondeo al múltiplo inmediatamente superior
            $v = explode(".", $this->Unidades / $packing);
            $resultado = $v[0];
            if ($v[1])
                $resultado++;
            $this->setUnidades($resultado * $packing);
            $this->_alertas[] = "Unidad Mínima de Compra " . $packing;
        }
    }

    /**
     * Devuelve un array de objetos Recepciones de la linea de pedido
     * @return array Array de objetos Recepciones 
     */
    public function getDetalleRecepcion() {

        $lineas = array();

        $recepcion = new Recepciones();
        $rows = $recepcion->cargaCondicion("IDLinea", "IDLineaPedido='{$this->IDLinea}'", "IDLinea ASC");
        unset($recepcion);

        foreach ($rows as $row) {
            $lineas[] = new Recepciones($row['IDLinea']);
        }

        return $lineas;
    }

}

?>
