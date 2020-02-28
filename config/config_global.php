<?php
define("CONTROLADOR_DEFECTO", "centros");
define("ACCION_DEFECTO", "index");

define("DIR_BASE",$_SERVER['CONTEXT_DOCUMENT_ROOT']."/");
define("DIR_CORE",DIR_BASE."/core");
define("IPREMOTA","172.27.0.56");
//parametros propios de la aplicacion
define("DIR_APP",DIR_BASE."/app/");
define("DIR_CONF",DIR_BASE."config/");
define("DIR_CLASES",DIR_BASE."scripts/clases/");
define("DIR_LOGS",DIR_BASE.'/scripts/datos/logs/');

define("DATA_SCRIPTS_DIR",'/scripts/datos_entrada/');

define("URL_BASE",'http://admespecial.aragon.es/');

define("DIA_INICIO",'2020/02/28');
define("DIA_INICIO_INSCRIPCION",'2020/03/11');
define("DIA_FIN_INSCRIPCION",'2020/03/17');
define("DIA_MAX_SOL_ALUMNO",'2020/03/16');

define("DIA_SORTEO",'2020/03/19');

define("DIA_BAREMACION",'2020/03/19');
define("DIA_PUBLICACION_BAREMACION",'2020/03/24');

define("DIA_PROVISIONALES",'2020/03/31');
define("DIA_DEFINITIVOS",'2020/04/16');

define("DIA_SORTEO_SP",'2020/04/22');

?>
