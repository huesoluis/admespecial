<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_BASE.'/core/Conectar.php';
require_once DIR_BASE.'/core/EntidadBase.php';
require_once DIR_BASE.'/models/Centro.php';

$conectar=new Conectar();
$conexion=$conectar->conexion();
$centro=new Centro($conexion,$_POST['id_centro'],'ajax');

$nuevoestado='CONTINUA';
$nuevoestado=$_POST['estado'];

$vacantes=$centro->getVacantes('centro','');

/*
if($_POST['estado']=='CONTINUA')
	{
	if(strpos('EBO',$_POST['tipoalumno'])!==FALSE and $vacantes[0]->vacantes==0){ print("error");exit();}	
	if($_POST['tipo_alumno']=='TVA' and $vacantes[1]->vacantes==0){ print("error");exit();}	
	}
*/
$sql="update matricula set estado='".$nuevoestado."' where id_alumno=".$_POST['id_alumno'];
$result=$conexion->query($sql);

$sql_centro='';
/*
if(isset($_POST['tipoalumno']))
{
	$tipoalumno=$_POST['tipoalumno'];
	if($tipoalumno=='ebo') 
	{
		if($_POST['estado']=='CONTINUA')
			$sql_centro="update centros set vacantes_ebo=vacantes_ebo-1 where id_centro in(select id_centro from matricula where id_alumno=".$_POST['id_alumno'];
		if($_POST['estado']=='CAMBIAR A TVA')
			$sql_centro="update centros set vacantes_tva=vacantes_tva-1 where id_centro in(select id_centro from matricula where id_alumno=".$_POST['id_alumno'];
	}
	elseif($tipoalumno=='tva')
		{
		if($_POST['estado']=='NO CONTINUA')
			$sql_centro="update centros set vacantes_tva=vacantes_tva+1 where id_centro in(select id_centro from matricula where id_alumno=".$_POST['id_alumno'];
		if($_POST['estado']=='CAMBIAR A EBO')
			$sql_centro="update centros set vacantes_ebo=vacantes_ebo+1 where id_centro in(select id_centro from matricula where id_alumno=".$_POST['id_alumno'];
		}
if(strlen($sql_centro)!=0) $result_centro=$conexion->query($sql_centro);
}
*/

$vacantes=$centro->getVacantes('centro','');
$conexion->close();
if ($result)
	print($vacantes[0]->vacantes.':'.$vacantes[1]->vacantes);
	else     
	echo "No results".$sql;

?>
