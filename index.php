<?php
session_start();
if(!$_SESSION) 
header("location: login_activa.php");
/*
if(!isset($_SESSION['nombre_usuario']) || empty($_SESSION['nombre_usuario']))
{
header("location: login_activa.php");
}
*/
//Configuración global
require_once 'config/config_global.php';

//Base para los controladores
require_once 'core/ControladorBase.php';

//Funciones para el controlador frontal
require_once 'core/ControladorFrontal.func.php';

//Cargamos controladores y acciones
if(isset($_GET["controller"])){
    $controllerObj=cargarControlador($_GET["controller"]);
    lanzarAccion($controllerObj);
}else{
    $controllerObj=cargarControlador(CONTROLADOR_DEFECTO);
    lanzarAccion($controllerObj);
}
?>
