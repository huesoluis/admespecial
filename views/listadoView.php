<?php
session_start();
if(!isset($_SESSION['nombre_usuario'])) {header('login_activa.php');}
include('includes/head.php');
?>
<body>
<script href="../js/listados.js"></script>
    <div class="wrapper">
	<?php include('includes/sidebar.php');?>
        <!-- Page Content  -->
        <div id="content">
		<?php 
		include('includes/menusuperior.php');
		?>
		
		<div id='solicitudes'>
		<?php
		print($this->listadoInscritos($allalumnos));
		?>
		</div>
		<div id='l_matricula'>
		</div>
	
		<div id='l_solicitudes'>
		</div>
		<footer class="col-lg-12">
		    <hr/>
		 <?php echo "Departamento Educación ".date("Y"); ?>
		</footer>
		
		    <div class="line"></div>

	</div>
    </div>
<?php include('footerjs.php');?>
</body>

</html>
