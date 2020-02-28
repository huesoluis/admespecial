<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_BASE.'/core/Conectar.php';
require_once DIR_BASE.'/core/ControladorBase.php';
require_once DIR_BASE.'/core/EntidadBase.php';
require_once DIR_BASE.'/controllers/SolicitudController.php';
require_once DIR_BASE.'/models/Solicitud.php';
require_once DIR_BASE.'/controllers/ListadosController.php';
require_once DIR_BASE.'/controllers/CentrosController.php';
require_once DIR_BASE.'/scripts/ajax/form_alumnojs.php';
require_once DIR_BASE.'/models/Centro.php';
require_once DIR_BASE.'/scripts/informes/pdf/fpdf/classpdf.php';

require_once DIR_CLASES.'LOGGER.php';
require_once DIR_APP.'parametros.php';

#####VARIABLES#############################################################################

$id_centro=$_POST['id_centro'];
$tipo=$_POST['tipolistado'];
$rol=$_POST['rol'];
$subtipo_pdf=$_POST['tipolistado'];//dentro de cada tipo, el subtipo de listado
$subtipo_listado='vacantes';

$cabecera="campos_cabecera_".$subtipo_pdf;
$camposdatos="campos_bbdd_".$subtipo_pdf;

$dir_pdf=DIR_BASE.'/scripts/datossalida/pdflistados/';
$dir_pdf_web='scripts/datossalida/pdflistados/';

$tiposol=0;
$fase_sorteo=0;
$modo='pdf';

$list=new ListadosController('alumnos');
$centros_cont=new CentrosController(0);

##################################################################################
$log_genpdfs=new logWriter('log_genpdfs',DIR_LOGS);
$log_genpdfs->warning("DATOS POST PARA PDFS");
$log_genpdfs->warning(print_r($_POST,true));
##################################################################################

//si es para datos de matricula
if($tipo=='pdf_mat')
{
	//mostramos las solitudes completas sin incluir borrador
	$lvacantes=$list->getResumenMatriculaCentro($rol='centro',$id_centro,$tiposol,$modo); 
}

###################################################################################
$log_genpdfs->warning("PREOBTENIENDO RESUMEN MATRICULA PDF");
$log_genpdfs->warning(print_r($lvacantes,true));

$log_genpdfs->warning("CAMPOs VACANTES PARA PDFS");
$log_genpdfs->warning(print_r($$camposdatos,true));
###################################################################################

$datos=array();
$i=0;
$pdf = new PDF();
$cab=$$cabecera;
$pdf->SetFont('Helvetica','',8);
$pdf->Ln(20);
$pdf->Ln(20);
$pdf->setTitle('PROCESO DE ESCOLARIZACION DE ALUMNOS EN CENTROS SOSTENIDOS CON FONDOS PUBLICOS');
//pagina en Landscape
$pdf->AddPage('L','',0,'LISTADO COMPLETO VACANTES');
$pdf->BasicTable($cab,(array)$lvacantes,1,$tam=29);
$pdf->Ln(20);
 // Arial italic 8
$pdf->SetFont('Arial','I',8);
  // Page number
$pdf->Cell(0,10,'                         SELLO DEL CENTRO',0,1);
$pdf->Cell(0,10,'EL/LA DIRECTORA                        ',0,1,'R');
$pdf->Cell(0,10,'En Zaragoza______________________a____de____2020',0,0,'C');
$pdf->Ln(20);
$pdf->Cell(0,10,'Fdo                        ',0,1,'R');

$pdf->Output($dir_pdf.$subtipo_listado.'/'.$id_centro.'.pdf','F');

$pdffile=$dir_pdf_web.$subtipo_listado.'/'.$id_centro.'.pdf';
print($pdffile);
?>
