<?php
#carga de datos de matrícula del directorio de ficheros de matricula por mes
require('config.php');

$fdatos=DATA_DIR.'centros_aragon.csv';

$helper=new ACCESO($fdatos);

$total=$helper->carga_centros('origen');
print(PHP_EOL."fin carga centros, total: ".$total.PHP_EOL);

?>
