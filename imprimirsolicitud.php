<?php
session_start();

if(!isset($_SESSION['nombre_usuario']) || empty($_SESSION['nombre_usuario']))
{
header("location: login_activa.php");
}
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_CLASES.'LOGGER.php';
require_once DIR_APP.'parametros.php';
require_once DIR_BASE.'core/ControladorBase.php';
require_once DIR_BASE.'core/EntidadBase.php';
require_once DIR_BASE.'controllers/ListadosController.php';
require_once DIR_BASE.'controllers/SolicitudController.php';
require_once DIR_BASE.'models/Centro.php';
require_once DIR_BASE.'models/Solicitud.php';


$sc=new SolicitudController('alumno');
$conexion=$sc->getConexion();
include('includes/head.php');
?>
<body>
    <div class="wrapper">
	    <div id="content">
			<a href="<? echo URL_BASE ?>"><button class="btn btn-outline-info" id="inicio" type="button">INICIO</button></a>
			<button class="btn btn-primary" id="imprimir">IMPRIMIR CTRLP</button>
	  	<span type="hidden" id="estado" name="estado"><?php echo $_SESSION['estado']; ?></span>
	  	<span type="hidden" id="rol" name="rol"><?php echo $_SESSION['rol']; ?></span> 
				<?php include 'includes/cabecera_impresion.php';?>
			<div class="row ">
				<div id="headimp" style="width:100%">
					<?php echo $sc->imprimirSolicitud($_GET['id']);?>			
				</div>
			</div>
				<?php include 'includes/pie_impresion.php';?>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
<script>
$('#imprimir').click(function(){

$('#imprimir').hide();
window.print();
$('#imprimir').show();
});
</script>
</body>
