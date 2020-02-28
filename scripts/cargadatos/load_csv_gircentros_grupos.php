<?php
#carga de datos de matrícula del directorio de ficheros de matricula por mes
$basedatos=require_once '../../config/config_database.php';

$fdatos='../datos/datos_entrada/centros_grupos1.csv';
require_once('../clases/ACCESO.php');

$helper=new ACCESO($fdatos,$basedatos);

$total=$helper->carga_centros_grupos();
print(PHP_EOL."fin carga centros, total: ".$total.PHP_EOL);

?>
