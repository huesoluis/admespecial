<?php 
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once 'core/Conectar.php';
session_start();
$conectar=new Conectar();
$conexion=$conectar->conexion();
header('Content-Type: text/html; charset=UTF-8');  

// Define variables and initialize with empty values
$nombre_usuario = $clave = "";
$err=$nombre_usuario_err = $clave1_err =$clave2_err= "";
    // Processing form data when form is submitted
  if($_SERVER["REQUEST_METHOD"] == "POST")
	{
  if(strlen($_POST['clave1'])<=4)
  {
            $clave1_err = 'La contraseña debe ser de al menos 5 caracteres';
  } 
  else
  {
   	$clave1 = trim($_POST["clave1"]);
  }
  if(strlen($_POST['clave2']<=4))
	{
            $clave2_err = 'Clave debe tener al menso 5 caracteres';
  } 
	else
	{
   	$clave2 = trim($_POST["clave2"]);
  }
  // tamaño clave mayor de 4
  if(strlen(trim($_POST["clave1"])!=trim($_POST["clave2"])))
       $err = 'Las contraseñas son distintas';
        // Si no hay errores, actualizar la abse de datos
        if(empty($clave1_err) && empty($clave2_err) && empty($err) )
	{
				//si es un alumno la clave es de 4 digitos
				$centro=$_SESSION['id_centro'];
				$usuario=$centro;
        			$sql1 = "UPDATE centros set primera_conexion='no' where id_centro=?";
        			$sql2 = "UPDATE usuarios set clave=md5('".$_POST['clave1']."') where id_usuario=?";

	    	#if($stmt1 = $conexion->prepare($sql1) && $stmt2 = $conexion->prepare($sql2))
	    			if($stmt1 = $conexion->prepare($sql1))
				{
	        		  // Bind variables to the prepared statement as parameters
     			   	  $stmt1->bind_param("i", $centro);
			        //$stmt2->bind_param("i", $usuario);
				// Attempt to execute the prepared statement
				if($stmt1->execute())
				{
				$res1=1;
				$stmt1->close();
				}
				else {echo "No ha podido actualizarse la contraseña, prueba más tarde o consulta al administrador lhueso@aragon.es";}
				//header("location: login_activa.php");
				}
			    	if($stmt2 = $conexion->prepare($sql2))
				{
			         // Bind variables to the prepared statement as parameters
			        $stmt2->bind_param("i", $usuario);
				// Attempt to execute the prepared statement
				if($stmt2->execute())
				{
				$res2=1;
				$stmt2->close();
				}
				else {echo "No ha podido actualizarse la contraseña, prueba más tarde o consulta al administrador lhueso@aragon.es";}
				}
				if($res1==1 && $res2==1)
					header("Refresh:5;url=login_activa.php");
					echo "CONTRASEÑA ACTUALIZADA CORRECTAMENTE En unos segundos podrás iniciar sesion";
				
        }
        // Close connection
        $conexion->close();
	}
    ?>
    <!DOCTYPE html>

    <html lang="es">

    <head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso inscripciones estudios de Educación Especial</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
            .wrapper{ 
width:450px;
padding: 28px;
    padding-top: 28px;
margin: auto;
padding-top: 120px;		
	}
input[type=text], input[type=password] {
    width: 450px;
    padding: 1px 10px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

@media screen and (max-width : 600px) {
            .wrapper{

width: 100%;
} 
input[type=text], input[type=password] {
    width: 100%;
}
}
        </style>

    </head>

    <body>
        <div class="wrapper">
            <h2>Acceso inscripciones estudios de Educación Especial</h2>

            <h3>DEBES CREAR UNA NUEVA CONTRASEÑA:</h3>
            <form action="" method="post">
                <div class="form-group <?php echo (!empty($nombre_usuario_err)) ? 'has-error' : ''; ?>">
                    <label>Contraseña</label>
                    <input type="password" name="clave1" class="form-control">
                    <span class="help-block"><?php echo $clave1_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($clave_err)) ? 'has-error' : ''; ?>">
                    <label>Repite la contraseña</label>
                    <input type="password" name="clave2" class="form-control">
                    <span class="help-block"><?php echo $clave2_err; ?></span>
                </div>
                <span class="help-block"><?php echo $err; ?></span>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Actualizar contraseña">
                </div>
            </form>
        </div>    

<footer class="page-footer font-small stylish-color-dark pt-4 mt-4">

    <!--Footer Links-->
    <div class="container text-center text-md-left">
        <div class="row">

    <hr>

    <!--Call to action-->
    <div class="text-center py-3">
        <ul class="list-unstyled list-inline mb-0">
            <li class="list-inline-item">
    <!--Copyright-->
    <div class="footer-copyright py-3 text-center">
        Registro e incidencias:
        <a href="mailto:lhueso@aragon.es">lhueso@aragon.es </a>
    </div>
            </li>
        </ul>
    </div>
    <!--/.Call to action-->

    <hr>


</footer>
<!--/.Footer-->
    </body>

        <script src="js/login.js"></script>
    </html>


