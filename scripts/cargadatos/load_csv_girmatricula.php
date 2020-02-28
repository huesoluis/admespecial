<?php
#carga de datos de matrícula del directorio de ficheros de matricula por mes
$basedatos=require_once '../../config/config_database.php';
require_once 'config_scripts.php';
require_once '../clases/ACCESO.php';

$fdatos=DATA_SCRIPTS_DIR.'matricula6.csv';

$helper=new ACCESO($fdatos,$basedatos);

$res=$helper->carga_matricula();
print(PHP_EOL."fin carga matricula, total: ".$res[0]);
print(PHP_EOL."filas omitidas: ".$res[1]);
print(PHP_EOL."TOTAL:  ".$res[2]);

?>
