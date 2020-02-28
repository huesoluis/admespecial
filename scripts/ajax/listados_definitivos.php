<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_CLASES.'LOGGER.php';
require_once DIR_APP.'parametros.php';
require_once DIR_BASE.'core/ControladorBase.php';
require_once DIR_BASE.'core/EntidadBase.php';
require_once DIR_BASE.'controllers/ListadosController.php';
require_once DIR_BASE.'models/Centro.php';
require_once DIR_BASE.'scripts/informes/pdf/fpdf/classpdf.php';

######################################################################################
$log_listados_definitivos=new logWriter('log_listados_definitivos',DIR_LOGS);
$log_listados_definitivos->warning("OBTENIENDO DATOS DEFINITIVOS POST:");
$log_listados_definitivos->warning(print_r($_POST,true));
######################################################################################

//VARIABLES

$dir_pdf=DIR_BASE.'/scripts/datossalida/pdflistados/definitivos/';
$id_centro=$_POST['id_centro'];
$subtipo_listado=$_POST['subtipo'];//dentro de cada tipo, el subtipo de listado
$filtro_datos='<input type="text" class="form-control" id="filtrosol"  placeholder="Introduce datos del alumno"><small id="emailHelp" class="form-text text-muted"></small>';
$list=new ListadosController('alumnos');
$conexion=$list->getConexion();
$tcentro=new Centro($conexion,$_POST['id_centro'],'ajax');
$tcentro->setNombre();

$dvacantes=$tcentro->getVacantes($id_centro);
$vacantes_ebo=$dvacantes[0]->vacantes;
$vacantes_tva=$dvacantes[1]->vacantes;

$cabecera="campos_cabecera_".$subtipo_listado;
$camposdatos="campos_bbdd_".$subtipo_listado;

######################################################################################
$log_listados_definitivos->warning("OBTENIENDO SOLICITUDES GENERALES, CABECERA: ".$cabecera);
$log_listados_definitivos->warning("OBTENIENDO SOLICITUDES GENERALES, CAMPOS DATOS: ".$camposdatos);
$log_listados_definitivos->warning("OBTENIENDO SOLICITUDES GENERALES, CENTRO: ".$id_centro);
######################################################################################

//actualizamos solicitudes para tener en cuenta las que hayan cambiado
//Esto solo puede hacerse en el momento q finalice el plazo de provisionales!!!!!!!!
$solicitud=new Solicitud($conexion);
$solicitudes=$solicitud->genSolDefinitivas($id_centro,$vacantes_ebo,$vacantes_tva); 
//mostramos las solitudes completas sin incluir borrador
$solicitudes=$list->getSolicitudes($id_centro,0,$fase_sorteo=0,$modo='definitivos',$subtipo_listado); 

######################################################################################
$log_listados_definitivos->warning("OBTENIENDO SOLICITUDES GENERALES, DATOS: ");
$log_listados_definitivos->warning(print_r($solicitudes,true));
######################################################################################

if($_POST['pdf']==1)
{
	$datos=array();
	$i=0;
	//extraemos los campos de datos q nos interesan
	foreach($solicitudes as $sol)
	{
		$datos[$i] = new stdClass;
		foreach($$camposdatos as $d)
		{
			$datos[$i]->$d=$sol->$d;
		}
	$i++;
	}
	$pdf = new PDF();
	$cab=$$cabecera;
	$pdf->SetFont('Helvetica','',8);
	$pdf->AddPage();
	$pdf->BasicTable($cab,$datos);
	$pdf->AddPage();
	$pdf->Output(DIR_PROV.$subtipo_listado.'.pdf','F');
}

if($subtipo_listado=='admitidos_def') $subtipo='ADMITIDOS DEFINITIVO';
if($subtipo_listado=='noadmitidos_def') $subtipo='NO ADMITIDOS DEFINITIVO';
if($subtipo_listado=='excluidos_def') $subtipo='EXCLUIDOS DEFINITIVO';

print("<button type='button' class='btn btn-info' onclick='window.open(\"".DIR_PROV_WEB.$subtipo_listado.".pdf\",\"_blank\");'>Descarga listado</button>");
print($list->showFiltrosTipo());
print($filtro_datos);
print("<div style='text-align:center'><h1>LISTADO ".strtoupper($tipo_listado)." ".strtoupper($subtipo)."</h1></div>");
print($list->showListado($solicitudes,$_POST['rol'],$$cabecera,$$camposdatos,$provisional=1));

?>
