<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT']."/config/config_global.php";
require_once DIR_CLASES.'LOGGER.php';
require_once DIR_APP.'parametros.php';
require_once DIR_BASE.'core/ControladorBase.php';
require_once DIR_BASE.'core/EntidadBase.php';
require_once DIR_BASE.'controllers/ListadosController.php';
require_once DIR_BASE.'models/Centro.php';
require_once DIR_BASE.'scripts/informes/pdf/fpdf/classpdf.php';
require_once DIR_BASE.'models/Solicitud.php';

########################################################################################
$log_listado_solicitudes=new logWriter('log_listado_solicitudes',DIR_LOGS);
########################################################################################

//VARIABLES
$modo='presorteo';
$id_centro=$_POST['id_centro'];
$estado_convocatoria=$_POST['estado_convocatoria'];
$filtro_solicitudes='<input type="text" class="form-control" id="filtrosol"  placeholder="Introduce datos del alumno o centro"><small id="emailHelp" class="form-text text-muted"></small>';
$list=new ListadosController('alumnos');
$conexion=$list->getConexion();
$tcentro=new Centro($conexion,$_POST['id_centro'],'ajax');

$provincia='todas';
if(strpos($_POST['rol'],'sp')!==FALSE) $provincia=substr($_POST['rol'],2);

$tsolicitud=new Solicitud($conexion);
$tcentro->setNombre();
$nombre_centro=$tcentro->getNombre();
$nsolicitudes=$tcentro->getNumSolicitudes($id_centro);

//variable para controlar si se actualiza el sorteo en la tabla de centros
if($_POST['rol']=='centro') $fase_sorteo=$tcentro->getFaseSorteo();//0: no realizado, 1: se han asignado los numeros aleatorios, 2: se ha realizado sorteo
else $fase_sorteo=2;
$hoy = date("Y/m/d");

$form_nuevasolicitud='<div class="input-group-append" id="cab_fnuevasolicitud"><button class="btn btn-outline-info" id="nuevasolicitud" type="button">Nueva solicitud</button></div>';

$log_listado_solicitudes->warning("OBTENIENDO SOLICITUDES CON ROL: ".$_POST['rol']);
//Para el caso de acceso del administrador o servicios provinciales
if($_POST['rol']=='admin' or strpos($_POST['rol'],'sp')!==FALSE)
{
			$centros=$list->getCentrosIds($provincia);	
			foreach($centros as $centro)
			{
			########################################################################################
			$log_listado_solicitudes->warning("OBTENIENDO NOMBRE CENTRO para".$_POST['rol']);
			$log_listado_solicitudes->warning(print_r($centro,true));
			########################################################################################
			
			$tcentro->setId($centro->id_centro);
			$tcentro->setNombre();
			$nombre_centro=$tcentro->getNombre();
			$tablaresumen=$tcentro->getResumen('centro','alumnos');
			
			########################################################################################
			$log_listado_solicitudes->warning("OBTENIENDO SOLICITUDES COMO ADMINISTRADOR: ".$_POST['rol']);
			$log_listado_solicitudes->warning($nombre_centro);
			$log_listado_solicitudes->warning(print_r($tablaresumen,true));
			########################################################################################
			
			print($list->showTablaResumenSolicitudes($tablaresumen,$nombre_centro,$centro->id_centro));
			print('<br>');
			}
}
else
{
	$form_sorteo_parcial='<div class="input-group mb-3">
		<div class="input-group-append">
		</div>
		<div class="input-group-append">
			<button class="btn btn-success" type="submit" id="boton_realizar_sorteo">Realizar sorteo</button>
		</div>
		<input type="text" id="num_sorteo" name="num_sorteo" value="" placeholder="NUMERO OBTENIDO" disabled>
		<input type="hidden" id="num_solicitudes" name="num_solicitudes" value="'.$nsolicitudes.'" placeholder="NUMERO OBTENIDO" disabled>
	</div>';
	$form_sorteo_completo='<div class="input-group mb-3">
		<div class="input-group-append">
			<button class="btn btn-success" type="submit" id="boton_asignar_numero">Asignar numero</button>
		</div>
		<div class="input-group-append">
			<button class="btn btn-success" type="submit" id="boton_realizar_sorteo">Realizar sorteo</button>
		</div>
		<input type="text" id="num_sorteo" name="num_sorteo" value="" placeholder="NUMERO OBTENIDO" disabled>
		<input type="hidden" id="num_solicitudes" name="num_solicitudes" value="'.$nsolicitudes.'" placeholder="NUMERO OBTENIDO" disabled>
	</div>';

	//si se ha pulsado en el boton de asignar numero de sorteo
	if(isset($_POST['asignar'])) 
	{
		if($list->asignarNumSol($id_centro)!=1){ print("Error asignando numero para el sorteo");exit();}
		//actualizamos el centro para marcar la fase del sorteo
		$tcentro->setFaseSorteo(1);
		$fase_sorteo=1;
	}
	//si se ha realizado el sorteo
	if(isset($_POST['nsorteo']))
	{
			$modo='sorteo';
			$nsorteo=$_POST['nsorteo'];
			//Actualizamos el numero de sorteo para el centro
                        if($tcentro->setSorteo($nsorteo,$id_centro)==0) {print("ERROR SORTEO"); exit();}
			
			$dsorteo=$tcentro->getVacantes($id_centro);
			$vacantes_ebo=$dsorteo[0]->vacantes;
			$vacantes_tva=$dsorteo[1]->vacantes;
				
			if($list->actualizaSolicitudesSorteo($id_centro,$nsorteo,$nsolicitudes,$vacantes_ebo,$vacantes_tva)==0) print("NO HAY VACANTES");
			else
			{
				$tcentro->setFaseSorteo(2);
                                $fase_sorteo=2;
			//Si hemos llegado al dia d elas provisionales o posterior, generamos la tabla de soliciutdes para los listados provisionales
				if($estado_convocatoria==2)
				{
				$tsolicitud->copiaTablaProvisionales($id_centro);	
				########################################################################################
				$log_listado_solicitudes->warning("CREADA TABLA PROV. ESTADO: ".$tcentro->getEstado());
				########################################################################################
				}
			}	
			########################################################################################
			$log_listado_solicitudes->warning("ACTUALIZANDO DATOS ALUMNOS SORTEO vacantes: ".$nsolicitudes);
			########################################################################################
	}

	########################################################################################
	$log_listado_solicitudes->warning("OBTENIENDO SOLICITUDES, FASE: ".$fase_sorteo);
	########################################################################################
	if($hoy==DIA_SORTEO) {$dia_sorteo=1;}
	if($fase_sorteo==2) {
	//mostramos las solitudes completas sin incluir borrador
	$solicitudes=$list->getSolicitudes($id_centro,0,$fase_sorteo); 
	}
	elseif($fase_sorteo==0)
	{
			//mostramos las solitudes completas, incluyendo borrador
			$log_listado_solicitudes->warning("OBTENIENDO SOLICITUDES NUMERO DE SORTEO NO ASIGNADO");
			$solicitudes=$list->getSolicitudes($id_centro,0,$fase_sorteo); 
	}
	else
	{
			$log_listado_solicitudes->warning("OBTENIENDO SOLICITUDES NUMERO DE SORTEO ASIGNADO");
			//mostramos solicitudes con el numero de sorteo asignado
			$solicitudes=$list->getSolicitudes($id_centro,0,$fase_sorteo); 
	}
	$tablaresumen=$tcentro->getResumen($_POST['rol'],'alumnos');
	$nombre_centro=$tcentro->getNombre();

	$log_listado_solicitudes->warning("OBTENIENDO SOLICITUDES, SORTEO: FASE SORTEO: ".$fase_sorteo." DIA SORTEO: ".$dia_sorteo);
	  #Mostramos formulario para el sorteo si es el dia correcto
        $fase_sorteo=$tcentro->getFaseSorteo();
	#Mostramos formulario para el sorteo si es el dia correcto
	if($fase_sorteo==0)
	{
			if($dia_sorteo==1) print($form_sorteo);
			if($_POST['id_centro']!='1') print($list->showTablaResumenSolicitudes($tablaresumen,$nombre_centro,$id_centro));
			print($form_nuevasolicitud);
			print('<br>');
			print($list->showFiltrosCheck());
			print($filtro_solicitudes);
			print($list->showSolicitudes($solicitudes,$_POST['rol']));
	}
	elseif($fase_sorteo==1)
        {
                        if($_POST['id_centro']>='1') print($list->showTablaResumenSolicitudes($tablaresumen,$nombre_centro,$id_centro));
                        if($dia_sorteo==1) print($form_sorteo_parcial); //mostramos formulario sorteo solo si no se ha hecho ya
                        print($form_nuevasolicitud);
                        print('<br>');
                        print($list->showFiltrosCheck());
                        print($filtro_solicitudes);
                        print($list->showSolicitudes($solicitudes,$_POST['rol']));
        }
	else
	{
			print($list->showSolicitudes($solicitudes,$_POST['rol']));
	}

}


?>
