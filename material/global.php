<?php
session_start();
if(!$_SESSION)  header("location: ../login_activa.php");
 ?>
<html lang='es'>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Graficos datos globales inserción laboral FCT</title>
	<link rel="shortcut icon" type="image/png"  href="../fpicon.ico" />
	<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script type="text/javascript" src="https://code.highcharts.com/modules/data.js"></script>
	<script src="../js/jquery.easy-autocomplete.min.js"></script>
	<script src="../js/g_global.js"></script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
	<link rel="stylesheet" href="../css/graficos.css">
	<link rel="stylesheet" href="../css/easy-autocomplete.min.css">
	<link href="../css/easy-autocomplete.themes.min.css" rel="stylesheet">
	<script>
	</script>
</head>
<body>

<p hidden id='idcentro' value='<?php echo $_SESSION['nombrecentro'];?>'></p>

<?php include "../php/configuracion.php"; include "../includes/navbar.php"; ?>
<div id="wrapper" >
<!-- Sidebar -->
<div class="container-fluid">
<div class="row">
<div class="col-sm-2">
 <!-- Sidebar -->
    <nav id="">
        <ul class="list-unstyled components">
<?php 
if($_SESSION['idgrupo']=='administrador')
{   
?>
		                    <li>
					<input id="buscar_centros"  placeholder="Introduce el centro"  /> 
				    </li>
<?php
}
?>
					<hr>
		                    <li> <a href="#empleo">GLOBAL EMPLEO</a> </li>
					<hr>
		                    <li> <a href="#relacionado">GLOBAL EMPLEO VINCULADO</a> </li>
					<hr>
		                    <li> <a href="#tfamilia">EMPLEO BRUTO POR FAMILIA</a> </li>
					<hr>
		                    <li> <a href="#trfamilia">EMPLEO VINCULADO POR FAMILIA</a> </li>
					<hr>

<?php 
if($_SESSION['idgrupo']=='administrador')
{   
?>
		                    <li> <a href="#tcentro">EMPLEO BRUTO POR CENTRO</a> </li>
					<hr>
		                    <li> <a href="#trcentro">EMPLEO RELACIONADO POR CENTRO</a> </li>
					<hr>
<?php

$rowdatafile='../datos_junio19/globales/datos_alumnos_respuestas.csv';
}
else
$rowdatafile='../datos_junio19/centros/'.$_SESSION["nombrecentro"].'/datos_alumnos_respuestas.csv';
?>
        </ul>
    </nav>
</div>
<div class="col-sm-8 text-center">
	<h1 style='text-align:center'>DATOS GLOBALES INSERCIÓN FCT <p class='subtext'><?php echo $_SESSION['nombrecentro'];?></p></h1>
	<br>

	<a id='datacentro' title='Para ver en Excel usar pestaña DATOS, opcion TEXTO EN COLUMNAS' href='<?php echo $rowdatafile; ?>' >Descargar datos brutos en csv</a>
	<br>
	<br>
	<div class='row'>	
		<div class="dglobal col-sm-4 col-sm-offset-2 a2">TOTAL ALUMNOS TITULADOS<p><i>Incluyendo titulados FCT y EXENTOS</i></p><div class="dg"></div><div class="dgp"></div></div>
		<div class="dglobal col-sm-4 a3 last">PORCENTAJE DE RESPUESTA<div class="dg"></div><div class="dgp"></div></div>
	</div>
	<br>
	<br>
	<div class='row'>	
		<h2 class='subtitulo'>% Sobre el total de alumnos que han finalizado FCT</h2>
		<div class="dglobal col-sm-2 col-sm-offset-3 a4">ALUMNOS QUE ESTUDIAN<div class="dg"></div><div class="dgp"></div></div>
		<div class="dglobal col-sm-2 a4">ALUMNOS QUE TRABAJAN<div class="dg"></div><div class="dgp"></div></div>
		<div class="dglobal col-sm-2 a4 last">ALUMNOS QUE ESTAN EN DESEMPLEO<div class="dg"></div><div class="dgp"></div></div>
	</div>
	<br>
	<br>
	<div class='row'>	
		<div class="dglobal col-sm-4 col-sm-offset-2 a4 dins1 ">INSERCION LABORAL<p><i>Respecto al total de alumnos que no estudian</i></p><div class="dg"></div><div class="dgp indicador"></div></div>
		<div class="dglobal col-sm-4 a4 last dins2">INSERCION LABORAL VINCULADA<p> (En empresas del sector)</p><p><i>Respecto al total de alumnos que trabajan</i></p><div class="dg"></div><div class="dgp indicador"></div></div>
	</div>
	<div class='row' id='empleo'>
		<div class='col-sm-12' id="trabaja_desempleo"></div>
	</div>
	<div class='row' id='relacionado'>
		<div id="trabaja_relacionado"></div>
	</div>
	<div class='row' id='tfamilia'>
		<div id="trabaja_por_familias"></div>
	</div>
	<div class='row' id='trfamilia'>
		<div id="trabaja_relacionado_por_familias"></div>
	</div>
	<div class='row' id='tcentro'>
		<div id="trabaja_por_centros"></div>
	</div>
	<div class='row' id='trcentro'>
		<div id="trabaja_relacionado_por_centros"></div>
	</div>

</div>
<div class="col-sm-2" id='periodo' >
<form id='tperiodo'>
	<input class='periodo'  type="radio" name="periodo" value="6m" id="6m" checked><span class='periodo'>6 meses</span><br><i class='itext'>Finalizaron FCTs en diciembre 2018</i><br>
	<input type="radio" name="periodo" value="12m" id="12m"><span class='periodo'>12 meses</span><br><i class='itext'> Finalizaron FCTs en junio de 2018</i><br>
</form>
</div>
</div>
</div>
</body>
