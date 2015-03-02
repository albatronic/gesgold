<?php
//require "funciones/parametros.class.php";

$AMARILLO = "#FFFF00";
$BLANCO = "#FFFFFF";
$ROJO = "#CC3333";
$AZUL = "#003399";
$VERDE = "#009900";
$GRIS = "#CCCCCC";
$FONDOMENU = "";

$SEPARADOR = "\\t";
$SALTOLINEA = "\\r\\n";
$ENCLOSED = "\"";

$ESTADOS_PSTOS = array("EL" => "Elaborado", "VA" => "Validado", "EN" => "Entregado", "AC" => "Aceptado", "RE" => "Rechazado");
$ESTADOS_SOLICITUD = array("R" => "Registrada", "T" => "En Tr�time", "F" => "Finalizada", "A" => "Anulada");
$PERIODOS_MANTOS = array('52' => "Semanal", "26" => "Quincenal", "12" => "Mensual", "6" => "Bimestral", "4" => "Trimestral", "3" => "Cuatrimestral", "2" => "Semestral", "1" => "Anual");
$TIPOS_PROMOCIONES = array('DE' => '% Descuento', 'NE' => 'Euros Netos', 'MA' => '% Margen');
$ESTADOS_TRASPASOS = array("P" => "Pendiente", "E" => "Enviado", "R" => "Recibido");
$TIPOS_NOTICIAS = array("0" => "Texto", "1" => "Imagen");
$TIPOS_ALMACENES = array("P" => "Propio", "L" => "Logistico");
$TRAMOS_KILOS = array(5, 10, 15, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200);
$VERSIONES = array("ESTANDAR" => "Estandar", "CRISTAL" => "Cristaleria", "TALLAS" => "Tallas y Colores");

function Script() {
    //Devuelve el nombre del script que ha provocado la llamada.
    //devuelve el nombre completo con la extensión pero sin la ruta.
    $script = explode("/", $_SERVER["SCRIPT_NAME"]);
    return $script[count($script) - 1];
}

function Resto($x, $y) {
    if ($y > 0)
        return $x % $y;
}

function Mensajes($iu) {
    //Devuelve el N.mero de mensajes internos que tiene el usuario 'iu' sin leer
    $sql = "select count(IDMensaje) from " . $_SESSION[DBDAT] . $_SESSION[empresa] . ".mensajes where Para='$iu' and Leido='0';";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    return($row[0]);
}

function Popup($m) {
//Genera un html con $m y lo muestra en una venta emergente
//El fichero se genera en la carpeta 'log' y por nombre tiene el id de usuario y la fecha y hora de creaci�n
//Despues de mostrarlo, no se borra.

    $tmp = "EnvioCorreo_" . $_SESSION[iu] . "_" . date('Ymd_His');
    $fp = fopen('documentos' . $_SESSION['empresa'] . '/log/' . $tmp . '.php', 'w');
    if ($fp) {
        fwrite($fp, $m);
        fclose($fp);
        AbreVentana('contenido.php?c=documentos' . $_SESSION['empresa'] . '/log/' . $tmp . '&t=Mensaje', 'Mensaje', 'width=350,menubar=no,resizable=yes,scrollbars=yes');
        //@unlink("tmp/".$tmp.".php");
    }
}

function NS_Obligatorio($idart) {
//DEVUELVE TRUE SI EL ID DEL ARTICULO NECESITA NUMERO DE SERIE

    $sql = "select ConNumeroSerie from articulos where IDArticulo='$idart';";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    return ($row[0] == 'S');
}

function EsInventariable($idart) {
//DEVUELVE DOS VALORES BOOLEANOS
// EL PRIMERO: SI ESTA SUJETO A INVENTARIO (SI SE CONTROLA EL STOCK)
// EL SEGUDNO: SI BLOQUEA STOCK. O SEA, SI NO SE PUEDEN QUEDAR LAS EXISTENCIAS NEGATIVAS.

    $sql = "select Inventario,BloqueoStock from articulos where IDArticulo='$idart';";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    return (array(($row[0] == 'S'), ($row[1] == 'S')));
}

function Lista($idlista, $columna, $separador) {
//DEVUELVE UN STREAM CON LA 'COLUMNA' DE LA LISTA INDICADA.
//LOS VALORES SE SEPARARAN POR $separador

    $res = mysql_query("select Tabla,Condiciones from listas where IDLista='$idlista';");
    $row = mysql_fetch_array($res);

    $sql = "select $columna from " . $row['Tabla'];
    if (trim($row['Condiciones']) != '')
        $sql.=" where " . $row['Condiciones'];
    $res = mysql_query($sql);
    while ($row1 = mysql_fetch_array($res))
        $lista.=$row1[$columna] . $separador;
    return(substr($lista, 0, strlen($lista) - 1));
}

function ResponsableSucursal($id) {
//DEVUELVE EL ID DE AGENTE RESPONSABLE DE LA SUCURSAL INDICADA

    $e = $_SESSION['DBEMP'];

    $sql = "select IDResponsable from $e.sucursales where IDEmpresa='" . $_SESSION['empresa'] . "' and IDSucursal='$id'";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    return($row[0]);
}

function Permisos($script) {
//DEVUELVE LOS PERMISOS PARA EL PERFIL EN CURSO Y EL SCRIPT INDICADO
//SE DEVUELVEN EN UN ARRAY (LECTURA,CREACION,BORRADO,MODIFICACION)

    $e = $_SESSION['DBEMP'];
    $p = $_SESSION['perfil'];

    //Busco los id de opcion y subopcion (orden) del script
    $sql = "select * from $e.submenu where Script='$script';";
    $res = mysql_query($sql, $_SESSION['CIDEmpresas']);
    $row = mysql_fetch_array($res);

    $sql = "select Permisos from $e.permisos where IDPerfil='$p' and IDOpcion='" . $row['IDOpcion'] . "' and IDSubopcion='" . $row['Orden'] . "';";
    $res = mysql_query($sql, $_SESSION['CIDEmpresas']);
    $row = mysql_fetch_array($res);

    $permisos = array('L' => substr($row['Permisos'], 0, 1), 'C' => substr($row['Permisos'], 1, 1), 'B' => substr($row['Permisos'], 2, 1), 'M' => substr($row['Permisos'], 3, 1), 'LI' => substr($row['Permisos'], 4, 1), 'EX' => substr($row['Permisos'], 5, 1));
    return($permisos);
}

function Tabla_Cabecera($ancho_tabla, $ancho_contenido) {

    if ($ancho_tabla == '')
        $ancho_tabla = '100%';
    if ($ancho_contenido == '')
        $ancho_contenido = '100%';

    echo '
<table width="', $ancho_tabla, '" cellspacing="0" cellpadding="0" background="images/fondo_gris.gif">
  <tr>
    <td background="images/margen_izq.gif" width="20" align="left" valign="top"><img src="images/esq_sup_izq.gif" width="20" height="20"></td>
    <td width="', $ancho_contenido, '" align="left" valign="top">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td background="images/borde_azul.gif"><img src="images/espacio.gif" width="1" height="1" border="0"></td>
      </tr>
      <tr>
        <td>';
}

function Tabla_Pie() {
    echo '
		</td>
      </tr>
    </table>
    </td>
    <td background="images/margen_dch.gif" width="20" align="right" valign="top"><img src="images/esq_sup_dch.gif" width="20" height="20"></td>
  </tr>
  <tr>
    <td rowspan="2" width="20" height="20"><img src="images/esq_inf_izq.gif" width="20" height="20" border="0"></td>
    <td width="1" height="19"><img src="images/espacio.gif" width="1" height="19" border="0"></td>
    <td rowspan="2" width="20" height="20"><img src="images/esq_inf_dch.gif" width="20" height="20" border="0"></td>
  </tr>
  <tr>
    <td background="images/borde_azul.gif"><img src="images/espacio.gif" width="1" height="1" border="0"></td>
  </tr>
</table>';
}

function FotoCliente($id, $tamano) {
    list($foto, $ancho, $alto) = MedidasFoto("documentos" . $_SESSION[empresa] . "/clientes/" . $id);
    if ($ancho > $alto)
        echo "<img border=0 width=", $tamano, " src='$foto'>";
    else
        echo "<img border=0 height=", $tamano, " src='$foto'>";
}

function MedidasFoto($id) {
//Devuelve el nombre completo de la foto y sus medidas

    $imagengif = $id . ".gif";
    $imagenjpg = $id . ".jpg";
    $imagenbmp = $id . ".bmp";

    if (file_exists($imagengif))
        $foto = $imagengif;
    elseif (file_exists($imagenjpg))
        $foto = $imagenjpg;
    elseif (file_exists($imagenbmp))
        $foto = $imagenbmp; else
        $foto="documentos" . $_SESSION[empresa] . "/clientes/sinfoto.gif";
    $size = GetImageSize($foto);
    return array($foto, $size[0], $size[1]);
}

function SacaFotoFabricante($idfabricante) {

    $imagengif = "documentos" . $_SESSION[empresa] . "/logosfabri/" . $idfabricante . ".gif";
    $imagenjpg = "documentos" . $_SESSION[empresa] . "/logosfabri/" . $idfabricante . ".jpg";
    $imagenpng = "documentos" . $_SESSION[empresa] . "/logosfabri/" . $idfabricante . ".png";

    if (file_exists($imagengif))
        $foto = $imagengif;
    elseif (file_exists($imagenjpg))
        $foto = $imagenjpg;
    elseif (file_exists($imagenpng))
        $foto = $imagenpng; else
        $foto="";
    if ($foto != "")
        echo "<img src='$foto' height='30'>"; else
        echo $idfabricante;
}

function Contador($accion, $empresa, $sucursal, $tipo, $coniva, $valor) {
//accion:   C=>Leer el contador; G=>Grabar el nuevo valor
//tipo:     E=>Facturas Emitidas; R=>Facturas Recibidas; RE=>Emitidas Rectificativas

    switch ($tipo) {
        case 'E':
            $campo = "ContadorEmitidas";
            $para = "LCFE";
            break;
        case 'RE':
            $campo = "ContadorEmitidasAbono";
            $para = "LCFRE";
            break;
        case 'R':
            $campo = "ContadorRecibidas";
            $para = "LCFR";
            break;
    }
    if ($tipo != 'RE') {
        if ($coniva) {
            $campo = $campo . "A";
            $para = $para . "A";
        } else {
            $campo = $campo . "B";
            $para = $para . "B";
        }
    }

    $letra = DameParametro($para, '');

    switch ($accion) {
        case 'C': //Lee y devuelve un contador
            //CONTADOR INDEPENDIENTE POR SUCURSAL ---> $sql="select $campo from ".$_SESSION['DBEMP'].".sucursales where IDEmpresa=$empresa and IDSucursal=$sucursal";
            $sql = "select $campo from " . $_SESSION['DBEMP'] . ".sucursales where IDEmpresa='$empresa' order by IDSucursal ASC;";
            $res = mysql_query($sql);
            $contador = mysql_fetch_array($res);
            return array($letra, $contador[0]);
            break;

        case 'G': //Actualiza un contador
            //CONTADOR INDEPENDIENTE POR SUCURSAL ---> $sql="update ".$_SESSION['DBEMP'].".sucursales set $campo='$valor' where IDEmpresa=$empresa and IDSucursal=$sucursal limit 1;";
            $sql = "update " . $_SESSION['DBEMP'] . ".sucursales set $campo='$valor' where IDEmpresa='$empresa';";
            $res = mysql_query($sql);
            break;
    }
}

function Subrrayado($ncolumnas) {
    echo "<tr><td colspan='$ncolumnas' background='images/subrrayado.gif'></td></tr>";
}

function DameSaldoCliente($idcli) {
//DEVUELVE EL IMPORTE DE LO PENDIENTE DE COBRO DE CLIENTE.
    $sql = "select sum(Importe) from recibos_clientes where IDCliente='$idcli' and Estado='P';";
    $res = mysql_query($sql);
    $ptecobro = mysql_fetch_array($res);
    return($ptecobro[0]);
}

function DameDirecEntrega($idcli) {
//DEVUELVE EL ID DE LA PRIMERA DIRECCION DE ENTREGA DEL CLIENTE. SI NO TIENE DEVUELVE 0
    $res = mysql_query("select IDDirec from clientes_dentrega where IDCliente='$idcli' order by IDDirec ASC;");
    $row = mysql_fetch_array($res);
    if ($row['IDDirec'] != '')
        return ($row['IDDirec']); else
        return(0);
}

function Atras($mensaje) {
    echo "<script language='JavaScript' type='text/JavaScript'>
			alert('$mensaje')
			history.go(-1)
		</script>";
}

function Mensaje($mensaje) {
    echo "<script language='JavaScript' type='text/JavaScript'>
			alert('$mensaje');
		</script>";
}

function AbreVentana($url, $nombre, $parametros) {
    echo "<script language='JavaScript' type='text/JavaScript'>
			window.open('" . $url . "','" . $nombre . "','" . $parametros . "')
		</script>";
}

function RecargaVentana($url) {
    echo "<script language='JavaScript' type='text/JavaScript'>
			document.location.href='" . $url . "'
		</script>";
}

function CierraVentana() {
    echo "<script language='JavaScript' type='text/JavaScript'>
			window.close();
		</script>";
}

function Imprimir() {
    echo "<script language='JavaScript' type='text/JavaScript'>
			window.print();
		</script>";
}

function BotonDocumento($tipo, $accion, $texto, $desdevalor, $hastavalor) {
    if ($texto == '') {
?>
        <a href="javascript:;" onClick="window.open('documento.php?t=<?php echo $tipo, "&accion=", $accion, "&d=", $desdevalor, "&h=", $hastavalor; ?>','Documento','width=290,height=170,menubar=no,resizable=yes,scrollbars=no')"><img src="images/imprimir.png" alt="<?php echo $_SESSION[TEXTOS]['Vista Preliminar']; ?>"></a>
<?php } else {
?>
        <a href="javascript:;" onClick="window.open('documento.php?t=<?php echo $tipo, "&accion=", $accion, "&d=", $desdevalor, "&h=", $hastavalor; ?>','Documento','width=290,height=170,menubar=no,resizable=yes,scrollbars=no')"><?php echo $texto; ?></a>
<?php
    }
}

function BotonSMS($texto, $de, $para, $mensaje, $origen, $idorigen) {
    //VER SI EST� HABILITADA LA OPCION
    $ok = DameParametro('SMSON', 0);

    if ($ok == '1') {
        if ($texto == '') {
?>
            <a href="javascript:;" onClick="window.open('sms.php?para=<?php echo $para, "&mensaje=", $mensaje, "&de=", $de, "&origen=", $origen, "&idorigen=", $idorigen; ?>','Documento','width=390,height=290,menubar=no,resizable=yes,scrollbars=no')"><img src="images/sms.jpg" width=25 alt="<?php echo $_SESSION[TEXTOS]['Enviar SMS']; ?>"></a>
<?php } else {
 ?>
            <a href="javascript:;" onClick="window.open('sms.php?para=<?php echo $para, "&mensaje=", $mensaje, "&de=", $de, "&origen=", $origen, "&idorigen=", $idorigen; ?>','Documento','width=390,height=290,menubar=no,resizable=yes,scrollbars=no')"><?php echo $texto; ?></a>
<?php
        }
    }
}

function Titulo($ncol, $titulo, $clase) {
    switch ($clase) {
        case 'T':
            $class = 'titulos';
            $iconos = '<img src="images/interrogacion.png" width="13" height="13" onclick="window.close();">';
            break;
        case 'S':
            $class = 'subtitulos';
            $iconos = "";
            break;
    }
    if ($ncol == '')
        $ncol = 1;
?>
    <tr>
        <td colspan="<?php echo $ncol; ?>" height="23">
            <table width='100%' cellpadding="0" cellspacing="0">
                <tr>
                    <td align="left"  height="23" class="<?php echo $class; ?>" bgcolor="#ADC1D6">&nbsp;::&nbsp;<?php echo $titulo; ?></td>
                    <td width="13" align="right" bgcolor="#ADC1D6"><?php echo $iconos; ?></td>
                    <td width="19" align="right"><img src="images/cabsupder.gif" width="19" height="23" alt=""></td>
                </tr>
            </table>
        </td>
    </tr>
<?php
}

function Paginar($sql, $pagina, $tampagina, $IDConexion='') {
// EN BASE A LA SENTENCIA '$sql' Y AL TAMAÑO DE LA PÁGINA '$tampagina', DEVUELVE:
//		 EL N. DE REGISTRO POR EL QUE COMIENZA LA P�GINA $pagina
//		 EL N. TOTAL DE REGISTROS DE $sql
//		 EL N. TOTAL DE P�GINAS DE $sql

    if ($IDConexion == '')
        $res = mysql_query($sql); else
        $res=mysql_query($sql, $IDConexion);
    $total_registros = mysql_num_rows($res);
    $total_paginas = floor($total_registros / $tampagina);
    if (($total_registros % $tampagina) > 0)
        $total_paginas = $total_paginas + 1;
    $desde = ($pagina - 1) * $tampagina;
    return array($desde, $total_registros, $total_paginas);
}

function Paginacion($pagina, $total_paginas, $total_registros, $url, $alineacion, $color, $listado) {

//RASTREO LA URL PARA ENCONTRAR EL NOMBRE DEL SCRIPT QUE SE VA A EJECUTAR,
//PARA LUEGO BUSCAR LOS PERMISOS DE LISTADO Y DE EXCEL.
    $a = explode('&', $url);
    $i = -1;
    $salir = 0;
    while (($i < count($a)) and (!$salir)) {
        $i++;
        $b = split('=', $a[$i]);
        if (($b[0] == "contenido.php?c") or ($b[0] == "c"))
            $salir = 1;
    }
    $permisos = Permisos($b[1]);

    if ($alineacion == 'left') {
        $estilo = "border-left: 1px dotted grey; border-top: 1px dotted grey; border-right: 1px dotted grey;";
    } else {
        $estilo = "border-left: 1px dotted grey; border-bottom: 1px dotted grey; border-right: 1px dotted grey;";
    }
?>
    <table width='100%' bgcolor='' cellspacing="0" cellpadding="0" style="<?php echo $estilo; ?>">
        <tr>
            <td class="ta11px" align="<?php echo $alineacion; ?>" height="19">
                <table class="ta11px">
                    <tr valign="middle">
                        <td height="19">
                        <?php
                        printf($_SESSION[TEXTOS]['Paginacion'], $pagina, $total_paginas, $total_registros);
                        if ($pagina > 1) {
                            echo " <a href='$url", 1, "'><img src='images/primero1.gif'  title='", $_SESSION[TEXTOS][Primero], "'></a>";
                            echo " <a href='$url", $pagina - 1, "'><img src='images/anterior1.gif'  title='", $_SESSION[TEXTOS][Anterior], "'></a>";
                            if ($pagina == $total_paginas) {
                                echo " <img src='images/siguiente0.gif' >";
                                echo " <img src='images/ultimo0.gif' >";
                            }
                        }
                        if ($pagina < $total_paginas) {
                            if ($pagina == 1) {
                                echo " <img src='images/primero0.gif' >";
                                echo " <img src='images/anterior0.gif' >";
                            }
                            echo " <a href='$url", $pagina + 1, "'><img src='images/siguiente1.gif'  title='", $_SESSION[TEXTOS][Siguiente], "'></a>";
                            echo " <a href='$url", $total_paginas, "'><img src='images/ultimo1.gif'  title='", $_SESSION[TEXTOS][Ultimo], "'></a>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (is_array($listado) and ($total_registros > 0) and ($permisos[LI])) { ?>
                            <table><tr>
                                <form name="LISTADO" action="listado.php" method="POST" target="blank">
                                    <td height="19">
                                        <input name="LISs" value="<?php echo $listado['sql']; ?>" type="hidden">
                                        <input name="LISt" value="<?php echo $listado['titulo']; ?>" type="hidden">
                                        <input name="LISc" value="<?php echo $listado['columnas']; ?>" type="hidden">
                                        <input name="LISf" value="<?php echo $listado['filtro']; ?>" type="hidden">
                                        <input type="image" src="images/imprimir.png" alt="<?php echo $_SESSION[TEXTOS]['Imprimir Listado']; ?>">
                                    </td>
                                </form>
                    </tr></table>
            <?php } ?>
                    </td>
                    <td>
            <?php if (is_array($listado) and ($total_registros > 0) and ($permisos[EX])) {
            ?>
                            <table><tr>
                                <form name="EXCEL" action="excel.php" method="POST" target="blank">
                                    <td height="19">
                                        <input name="LISs" value="<?php echo $listado['sql']; ?>" type="hidden">
                                        <input name="LISt" value="<?php echo $listado['titulo']; ?>" type="hidden">
                                        <input name="LISc" value="<?php echo $listado['columnas']; ?>" type="hidden">
                                        <input name="LISf" value="<?php echo $listado['filtro']; ?>" type="hidden">
                                        <input type="image" src="images/ico_excel.gif" alt="<?php echo $_SESSION[TEXTOS]['Enviar a Excel']; ?>">
                                    </td>
                                </form>
                    </tr></table>
<?php } ?>
                        </td>
                        </tr>
                        </table>
                        </td>
                        </tr>
                        </table>
<?php
                    }

                    function DameDatosEmpresa() {
                        $sql = "select " . $_SESSION['DBEMP'] . ".empresas.*," . $_SESSION['DBEMP'] . ".provincias.NOMBRE as Provincia from " . $_SESSION['DBEMP'] . ".empresas," . $_SESSION['DBEMP'] . ".provincias where (IDEmpresa=" . $_SESSION['empresa'] . ") and (IDProvincia=" . $_SESSION['DBEMP'] . ".provincias.CODIGO)";
                        $res = mysql_query($sql);
                        $row = mysql_fetch_array($res);
                        $d = "<b>" . $row['RazonSocial'] . "</b><br>";
                        $d = $d . "CIF: " . $row['Cif'] . "<br>";
                        $d = $d . $row['Direccion'] . "<br>";
                        $d = $d . $row['CodigoPostal'] . " " . $row['Poblacion'] . " " . $row['Provincia'] . "<br>";
                        $d = $d . "Tlf.: " . $row['Telefono'] . " Fax: " . $row['Fax'] . "<br>";
                        $d = $d . "<a href='mailto:" . $row['EMail'] . "'>" . $row['EMail'] . "</a><br>";
                        $d = $d . "<a href=http://" . $row['Web'] . ">" . $row['Web'] . "</a><br>";
                        return($d);
                    }

                    function Eslogan() {
                        $logo2 = DameParametro('LOGO2', '');
?>
                        <table width="100%" class="formularios">
                            <tr>
                                <td align="right"><b><?php echo DameParametro('ESLOG', ''); ?></b></td>
                                <td width="115"><?php if ($logo2 != '') {
?><img src="images/<?php echo $logo2; ?>" alt=""><?php } ?></td>
                </tr>
            </table>
<?php
                    }

                    function DameParametro($IDParametro, $defecto='') {
//DEVUELVE EL VALOR DEL PARAMETRO.

                        $par = new parametros($IDParametro);
                        $valor = $par->getValor();
                        unset($par);
                        if ($valor == '')
                            return $defecto; else
                            return $valor;
                    }

                    function Bloquea($tabla) {
                        $r = mysql_query("lock tables " . $tabla . " write;");
                    }

                    function Desbloquea($tabla) {
                        $r = mysql_query("unlock tables " . $tabla . ";");
                    }

                    function CuentaCorriente($formulario, $IDBanco, $IDOficina, $Digito, $Cuenta) {
                        global $campos;
?>
                        <input name="<?php echo $IDBanco; ?>" type="text" size="4" maxlength="4" value="<?php echo $campos['idbanco']; ?>" class="formularios">
                        <img src="images/lupa.png" onclick="MuestraBancos('<?php echo $formulario; ?>','<?php echo $IDBanco; ?>');" alt="">&nbsp;&nbsp;
                        <input name="<?php echo $IDOficina; ?>" type="text" size="4" maxlength="4" value="<?php echo $campos['idoficina']; ?>" class="formularios">
                        <img src="images/lupa.png" alt="" onclick="MuestraOficinas('<?php echo $formulario; ?>','<?php echo $IDBanco; ?>','<?php echo $IDOficina; ?>');">&nbsp;&nbsp;
                        <input name="<?php echo $Digito; ?>" type="text" size="2" maxlength="2" value="<?php echo $campos['digito']; ?>" class="formularios" readonly>&nbsp;&nbsp;
                        <input name="<?php echo $Cuenta; ?>" type="text" size="10" maxlength="10" value="<?php echo $campos['cuenta']; ?>" class="formularios">&nbsp;&nbsp;
<?php
                    }

                    function ValidaCC($b, $o, $d, $c) {
//Valida una cuenta corriente.
//Devuelve el digito de control si es correcta
//En caso contrario devuelve un mensaje de error

                        $e = $_SESSION['DBEMP'];
                        $mensaje = "";

                        //Validar Banco
                        $res = mysql_query("select count(IDBanco) from $e.bancos where IDBanco='$b';");
                        $row = mysql_fetch_array($res);
                        if ($row[0] != 1)
                            return("El banco indicado no existe.");

                        //Validar Oficina
                        $res = mysql_query("select count(IDBanco) from $e.bancos_oficinas where IDBanco='$b' and IDOficina='$o';");
                        $row = mysql_fetch_array($res);
                        if ($row[0] != 1)
                            return("La Oficina bancaria indicada no existe.");

                        if (strlen($c) < 10)
                            return("La cuenta corriente debe tener 10 d�gitos");

                        if ($b . $o . $c == '000000000000000000')
                            $dc = '00'; else
                            $dc=DigitoControl($b . $o, $c);
                        return($dc);
                    }

                    Function DigitoControl($IentOfi, $InumCta) {
                        $APesos = Array(1, 2, 4, 8, 5, 10, 9, 7, 3, 6); // Array de "pesos"
                        $DC1 = 0;
                        $DC2 = 0;
                        $x = 8;
                        while ($x > 0) {
                            $digito = $IentOfi[$x - 1];
                            $DC1 = $DC1 + ($APesos[$x + 2 - 1] * ($digito));
                            $x = $x - 1;
                        }
                        $Resto = $DC1 % 11;
                        $DC1 = 11 - $Resto;
                        if ($DC1 == 10)
                            $DC1 = 1;
                        if ($DC1 == 11)
                            $DC1 = 0;              // D�gito control Entidad-Oficina

                            $x = 10;
                        while ($x > 0) {
                            $digito = $InumCta[$x - 1];
                            $DC2 = $DC2 + ($APesos[$x - 1] * ($digito));
                            $x = $x - 1;
                        }
                        $Resto = $DC2 % 11;
                        $DC2 = 11 - $Resto;
                        if ($DC2 == 10)
                            $DC2 = 1;
                        if ($DC2 == 11)
                            $DC2 = 0;         // D�gito Control C/C

                            $DigControl = ($DC1) . "" . ($DC2);   // los 2 N.meros del D.C.
                        //if (strlen($DigControl)==3) $DigControl=substr($DigControl,1,2);
                        return $DigControl;
                    }

                    function CopiaSeguridad($empresa) {

                        $e = $_SESSION['DBEMP'];
                        $d = $_SESSION['DBDAT'] . $empresa;

                        $ok = 1;

                        $dbname = $d;
                        $backupfile = "documentos" . $empresa . "/backup/" . $dbname . "_" . date('Ymd_His') . ".sql";
                        $command = "mysqldump -h" . $_SESSION['SERVIDORDA'] . " -u" . $_SESSION['USUARIODA'] . " -p" . $_SESSION['PASSWORDDA'] . " $dbname > $backupfile";
                        echo $command;
                        system($command, $ok);
                        return array($ok);
                    }

                    function AbreCajon() {

                        $sql = "select AperturaCajon from " . $_SESSION['DBDAT'] . $_SESSION['empresa'] . ".tpvs where IDSucursal=" . $_SESSION['sucursal'] . " and IDTpv=" . $_SESSION['caja'];
                        $res = mysql_query($sql);
                        $row = mysql_fetch_array($res);
                        $comando = `$row[0]`;
                        echo "<pre>$comando</pre>";
                    }

                    function FormatoPapel($tipo, $formato) {
                        //DEVUELVE TODAS LAS MEDIDAS Y CARACTERISTICAS RELACIONADAS CON
                        //EL TIPO DE DOCUMENTO Y FORMATO INDICADO.
                        $emp = $_SESSION['DBEMP'];

                        $sql = "select t1.*,t2.* from $emp.documentos as t1,$emp.tipos_papel as t2
        where t1.IDTipo='$tipo' and t1.IDFormato='$formato' and t2.IDPapel=t1.IDPapel";
                        $res = mysql_query($sql);
                        return(mysql_fetch_array($res));
                    }

                    function MedidasPapel($id) {

                        $res = mysql_query("select Alto,Ancho,MargenSup,MargenIzq,LineasPorPagina,PapelContinuo from " . $_SESSION['DBEMP'] . ".tipos_papel where IDPapel='$id'");
                        $row = mysql_fetch_array($res);
                        if ($row[0] == '')
                            return array('ALTO' => -1);
                        else
                            return array('ALTO' => $row[0], 'ANCHO' => $row[1], 'MARGENSUP' => $row[2], 'MARGENIZQ' => $row[3], 'LOPAG' => $row[4], 'PAPELCONTIUNO' => $row[5]);
                    }
?>
