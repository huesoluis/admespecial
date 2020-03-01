<?php
session_start();
include('includes/head.php');
?>
<body>
    <div class="wrapper">
        <div id="content">
	  <input type="hidden" id="estado_convocatoria" name="estado_estado_convocatoria" value="<?php echo $_SESSION['estado_convocatoria']; ?>"></input>
	  <span type="hidden" id="provincia" name="provincia" value="<?php echo $_SESSION['provincia'];?>"></span>
	  <span type="hidden" id="estado" name="estado" value="<?php echo $_SESSION['estado']; ?>"></span>
	  <span type="hidden" id="rol" name="rol" value="<?php echo $_SESSION['rol']; ?>"><b>ROL: </b><?php echo $_SESSION['rol']; ?></b></span> 
		<?php if($_SESSION['rol']=='centro' or $_SESSION['rol']=='admin' or $_SESSION['provincia']!='aragon') include('includes/menusuperior.php');?>
		<?php /*usamos metodo del controlador de centros activo echo $this->showTimeline('centro',$_SESSION['id_centro'],'matricula');*/?>
		<div class="row ">
		<div id="t_matricula" style="width:100%"></div>
		<?php /*usamos metodo del controlador de centros activo*/if($_SESSION['rol']=='centro') echo $this->showTabla('centro',$_SESSION['id_centro'],'matricula');?>
		<?php if($_SESSION['rol']=='admin') echo $this->showTablas($_SESSION['rol'],$_SESSION['id_centro'],'matricula','todas');?>
		<?php if($_SESSION['provincia']!='aragon'){echo "sprovincial"; echo $this->showTablas($_SESSION['rol'],$_SESSION['id_centro'],'matricula',$_SESSION['provincia']);}?>
		<?php 
		if($_SESSION['rol']=='alumno' && $_SESSION['dia_inicio_inscripcion']==1)
		{
			if($_SESSION['fin_sol_alumno']=='1')
				echo '<button class="btn btn-outline-info" id="inicio" type="button"><h2>ULTIMO DIA PARA INSCRIBIRSE!!!</h2></button><br>';
			echo '<br>';
	  		echo '<input type="hidden" id="pin" name="pin" value="'.$_SESSION['clave'].'" ></input> ';
			if($_SESSION['nombre_usuario']=='nousuario')
				echo '<button class="btn btn-outline-info" id="nuevasolicitud" type="button">Nueva solicitud</button>';
			else //usuario alumno autenticado
				echo '<button class="btn btn-outline-info calumno" id="versolicitud" type="button">Ver solicitud</button>';
			echo '<a href="'.URL_BASE.'"><button class="btn btn-outline-info" id="inicio" type="button">INICIO</button></a>';
		}
		if($_SESSION['rol']=='alumno' && $_SESSION['dia_inicio_inscripcion']==0)
			{
				echo '<row><div class="col-12"><p><h1></h1></p></div></row>';
				echo '<row><p><h2></h2></p></row>';	
				echo '<p><h2></h2></p>';	
	echo '<main role="main" class="container">

      <div class="starter-template">
        <h1>INCICIO DE INSCRIPCION</h1>
        <p class="lead">INSCRIPCIONES VIA WEB: DEL 11 al 16 de MARZO (inclusive)</p>
        <p class="lead">INSCRIPCIONES EN LOS CENTROS: DEL 11 al 17 de MARZO (inclusive)</p>
        <p class="lead"><i>En cualquier caso los impresos hay que entregarlos en el centro firmados</i></p>
	<a href="'.URL_BASE.'"><button class="btn btn-outline-info" id="inicio" type="button">VOLVER</button></a>    </div>

    </main><!-- /.container -->';
			}
		?>
		</div>
		<div class="row ">
		<div id="l_matricula" style="width:100%"></div>
		</div>
        </div>
    </div>
    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
		<script>
			$( document ).ready(function() {
			 $( "#nuevasolicitud" ).trigger( "click" );
 			});
		</script>
</body>
</html>
