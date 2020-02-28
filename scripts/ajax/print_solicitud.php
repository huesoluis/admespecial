<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_BASE.'/core/Conectar.php';
require_once DIR_BASE.'/core/ControladorBase.php';
require_once DIR_BASE.'/core/EntidadBase.php';
require_once DIR_BASE.'/controllers/SolicitudController.php';
require_once DIR_BASE.'/models/Solicitud.php';
require_once DIR_BASE.'/scripts/ajax/form_alumnojs.php';

require_once DIR_CLASES.'LOGGER.php';
require_once DIR_APP.'parametros.php';

$conectar=new Conectar();
$conexion=$conectar->conexion();
$log_editar=new logWriter('log_solpdf',DIR_LOGS);

$solicitud=new Solicitud($conexion);

$fsol=$solicitud->crearpdf($_POST['id_alumno']);

if($fsol==1) print('../datossalida/pdfsolicitudes/sol'.$_POST['id_alumno'].'.pdf');
else print("error generando pdf");
?>
