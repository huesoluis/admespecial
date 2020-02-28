<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_CLASES.'LOGGER.php';
require_once DIR_APP.'parametros.php';
require_once DIR_BASE.'/core/ControladorBase.php';
require_once DIR_BASE.'/core/EntidadBase.php';
require_once DIR_BASE.'/controllers/ListadosController.php';
require_once DIR_BASE.'/controllers/CentrosController.php';
require_once DIR_BASE.'/models/Centro.php';

$log_mostrar_solicitudes=new logWriter('log_mostrar_solicitudes',DIR_LOGS);
if($_POST['rol']=='admin' || $_POST['provincia']!='aragon') 
{
	$id_centro=$_POST['id_centro'];
	$list=new ListadosController('alumnos');
	$log_mostrar_solicitudes->warning('IDCENTRO: '.$id_centro);
	$solicitudes=$list->getSolicitudes($id_centro,0,$fase_sorteo=0); 
	$log_mostrar_solicitudes->warning('MOSTRAR SOLICITUDES');
	$log_mostrar_solicitudes->warning(print_r($solicitudes,true));
	print($list->showSolicitudes($solicitudes,'centro'));
}
else
{
print("ERROR");
}
?>
