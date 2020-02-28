<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_BASE.'/core/ControladorBase.php';
require_once DIR_BASE.'/core/EntidadBase.php';
require_once DIR_BASE.'/controllers/ListadosController.php';
require_once DIR_BASE.'/controllers/CentrosController.php';
require_once DIR_BASE.'/models/Centro.php';

if($_POST['rol']=='admin' || $_POST['rol']=='sp') 
{
	$list=new ListadosController('matricula',1);
	$matriculas=$list->getAlumnosCentro($_POST['id_centro']);
	print($list->showMatriculados($matriculas,$_POST['rol'],$_POST['id_centro']));
}
else
{
$list=new ListadosController('matricula',1);
$conexion=$list->getConexion();
$tcentro=new Centro($conexion,$_POST['id_centro'],'ajax');
$tcentro->setNombre();
$matriculas=$list->getAlumnosCentro($_POST['id_centro']);
	$tablaresumen=$tcentro->getResumen('centro','matricula');
	print($list->showMatriculados($matriculas,$_POST['$rol'],$_POST['id_centro']));
}
?>
