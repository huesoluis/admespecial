<?php
#carga de datos de matrícula del directorio de ficheros de matricula por mes
$basedatos=require_once '../../config/config_database.php';
$fdatos='../datos/pruebas/';
require_once('../clases/ACCESO.php');

$helper=new ACCESO($fdatos,$basedatos);

$total=$helper->generar_solicitudes('1000');
print(PHP_EOL."fin carga solicitudes, total: ".$total.PHP_EOL);

?>
