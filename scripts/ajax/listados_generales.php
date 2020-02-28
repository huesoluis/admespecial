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
$log_listados_generales=new logWriter('log_listados_generales',DIR_LOGS);
$log_listados_generales->warning("OBTENIENDO SOLICITUDES");
$log_listados_generales->warning(print_r($_POST,true));
######################################################################################

//VARIABLES

$dir_pdf=DIR_BASE.'/scripts/datossalida/pdflistados/';
$id_centro=$_POST['id_centro'];
$tipo_listado=$_POST['tipo'];//listados del sorteo, provisionales o definitivos
$subtipo_listado=$_POST['subtipo'];//dentro de cada tipo, el subtipo de listado
$filtro_datos='<input type="text" class="form-control" id="filtrosol"  placeholder="Introduce datos del alumno"><small id="emailHelp" class="form-text text-muted"></small>';
$list=new ListadosController('alumnos');
$conexion=$list->getConexion();
$tcentro=new Centro($conexion,$_POST['id_centro'],'ajax');
$tcentro->setNombre();

$cabecera="campos_cabecera_".$subtipo_listado;
$camposdatos="campos_bbdd_".$subtipo_listado;

######################################################################################
$log_listados_generales->warning("OBTENIENDO SOLICITUDES GENERALES, CABECERA: ".$cabecera);
$log_listados_generales->warning("OBTENIENDO SOLICITUDES GENERALES, CAMPOS DATOS: ".$camposdatos);
$log_listados_generales->warning("OBTENIENDO SOLICITUDES GENERALES, CENTRO: ".$id_centro);
######################################################################################

//mostramos las solitudes completas sin incluir borrador
$solicitudes=$list->getSolicitudes($id_centro,0,$fase_sorteo=3); 

######################################################################################
$log_listados_generales->warning("OBTENIENDO SOLICITUDES GENERALES, DATOS: ");
$log_listados_generales->warning(print_r($solicitudes,true));
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
	$log_listados_generales->warning(print_r($datos,true));
	$pdf = new PDF();
	$cab=$$cabecera;
	$pdf->SetFont('Helvetica','',8);
	//$pdf->AddPage('L','',0,$subtipo_listado);
	$pdf->AddPage('L','',0,$subtipo_listado);
	$pdf->BasicTable($cab,$datos);
	$pdf->Ln(20);
	 // Arial italic 8
	$pdf->SetFont('Arial','I',8);
	  // Page number
	$pdf->Cell(0,10,'En Zaragoza______________________a____de____2020_',0,0,'C');
	$pdf->Output(DIR_SOR.$subtipo_listado.'.pdf','F');
}

if($subtipo_listado=='sor_ale') $subtipo='Nº ALEATORIO';
if($subtipo_listado=='sor_bar') $subtipo='SOLICITUDES BAREMADAS';
if($subtipo_listado=='sor_bar') $subtipo='SOLICITUDES DETALLE BAREMO';

print("<button type='button' class='btn btn-info' onclick='window.open(\"".DIR_SOR_WEB.$subtipo_listado.".pdf\",\"_blank\");'>Descarga listado</button>");
print($filtro_datos);
print("<div style='text-align:center'><h1>LISTADO ".strtoupper($tipo_listado)." ".strtoupper($subtipo)."</h1></div>");
print($list->showFiltrosTipo());
print($list->showListado($solicitudes,$_POST['rol'],$$cabecera,$$camposdatos));

?>
