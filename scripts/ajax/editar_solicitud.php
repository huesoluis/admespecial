<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_BASE.'/core/ControladorBase.php';
require_once DIR_BASE.'/core/EntidadBase.php';
require_once DIR_BASE.'/models/Solicitud.php';
require_once DIR_BASE.'/controllers/SolicitudController.php';
require_once DIR_BASE.'/scripts/ajax/form_alumnojs.php';

require_once DIR_CLASES.'LOGGER.php';
require_once DIR_APP.'parametros.php';

$log_formulario=new logWriter('log_formulario_solicitud',DIR_LOGS);
$log_nuevoalumno=new logWriter('log_nuevoalumno',DIR_LOGS);

//$conexion=$scontroller->getConexion();

$consulta=$_POST['modo'];
if(isset($_POST['id_alumno'])) $id=$_POST['id_alumno'];
else $id=0;
if(isset($_POST['codigo_centro'])) $id_centro=$_POST['codigo_centro'];
else $id_centro=0;
if(isset($_POST['rol'])) $rol=$_POST['rol'];
else $rol='alumno';

$scontroller=new SolicitudController($rol);

$log_nuevoalumno->warning("DATOS ALUMNO RECIBID");
$log_nuevoalumno->warning(print_r($_POST,true));
#si es un ciudadano obtenemos el id usando el pin proporcionado
if($rol=='alumno')
{
	$pin=$_POST['pin'];
	$log_nuevoalumno->warning("PIN ALUMNO RECIBIDO:".$pin);
	$id=$scontroller->getIdAlumnoPin($pin);
	$log_nuevoalumno->warning("PIN ALUMNO RECIBIDO:".$id);

}

if(isset($_POST['codigo_centro'])) $id_centro=$_POST['codigo_centro'];
$log_formulario->warning("ID ALUMNO RECIBIDO:");
$log_formulario->warning($id);
$log_formulario->warning($_POST['codigo_centro']);

//obtenemos formulario con los datos
$sform=$scontroller->showFormSolicitud($id,$id_centro,$rol);
//Si el id es cero obentemos el nuevo id
if($id==0) $id=$scontroller->lastid+1;

$repjs="#loc_dfamiliar".$id;
$script=str_replace('.localidad',$repjs,$script);

$repjs="#nacionalidad".$id;
$script=str_replace('.nacionalidad',$repjs,$script);


if($consulta=='autocompletar')
	print($datos_solicitud);
else
	{
	print($sform);
	print($script);
	}
?>
