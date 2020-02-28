<?php
class SolicitudController extends ControladorBase{
    public $conectar;
    public $adapter;
    public $lastid;
    public $datos_solicitud;
 
    public function __construct($rol='centro') 
		{
        parent::__construct();
        require_once DIR_BASE.'/includes/form_solicitud.php';
				require_once DIR_CLASES.'LOGGER.php';
				require_once DIR_APP.'parametros.php';
				$this->log_editar=new logWriter('log_editar_solicitud',DIR_LOGS);
				$this->log_nueva=new logWriter('log_nueva_solicitud',DIR_LOGS);
				$this->log_nuevoalumno=new logWriter('log_nuevoalumno',DIR_LOGS);
        $this->conectar=new Conectar();
        $this->adapter=$this->conectar->conexion();
        $this->formulario=$formsol;
        $this->rol=$rol;
    }
 
    public function getIdAlumnoPin($pin)
		{
			$sql="select a.id_alumno from usuarios u, alumnos a where a.id_usuario=u.id_usuario and u.clave_original=$pin";
				$this->log_nuevoalumno->warning("consulta nuevo alumno: ".$sql);
 			$query=$this->getConexion()->query($sql);
				if($query)
    	  return $query->fetch_object()->id_alumno;
			else return 0;
		}
    public function comprobarSolicitud($dni_tutor,$nombre_alumno,$apellido1)
		{
        	$solicitud=new Solicitud($this->adapter);
        	$dsolicitud=$solicitud->compSol($dni_tutor,$nombre_alumno,$apellido1);
		return $dsolicitud;
		}

    public function imprimirSolicitud($id)
		{

    return $this->showFormSolicitud($id,$id_centro=0,$this->rol,$collapsed=0,$imprimir=1);
		}
    public function procesarFormularioExistente($id,$dsolicitud,$collapsed=1,$rol='centro',$imprimir=0)
		{
			//si es para imprimir quitamos el boton de actualizar
			if($imprimir==1) 
			{
				$this->formulario=str_replace('<a class="btn btn-primary send" >GRABAR SOLICITUD</a>','',$this->formulario);
			}
			
		
			//si collapsed es 0 lo mostramos desplegado
			if($collapsed==0) 
			{
				$this->formulario=str_replace('data-toggle="collapse"','data-toggle="collapse" aria-expanded="true"',$this->formulario);
			 	$this->formulario=str_replace('class="collapse"','class="collapse show"',$this->formulario);
			}
			//Ocultamos boton de validacion para alumnos
			if($rol=='alumno')
				{
				$this->formulario=preg_replace('/<button name="boton.* class="btn btn-outline-dark validar".*<\/button>/','',$this->formulario);
				$this->formulario=preg_replace('/<button name="boton.* class="btn btn-outline-dark".*<\/button>/','',$this->formulario);
				}
			//ponemos id en las cabeceras de cada seccion de datos	
			$this->formulario=str_replace('data-target="#personales"','data-target="#personales'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="personales"','id="personales'.$id.'"',$this->formulario);
			$this->formulario=str_replace('data-target="#expone"','data-target="#expone'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="expone"','id="expone'.$id.'"',$this->formulario);
			$this->formulario=str_replace('data-target="#solicita"','data-target="#solicita'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="solicita"','id="solicita'.$id.'"',$this->formulario);
			$this->formulario=str_replace('data-target="#baremo"','data-target="#baremo'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo"','id="baremo'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="id_puntos_baremo_totales"','id="id_puntos_baremo_totales'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="id_puntos_baremo_validados"','id="id_puntos_baremo_validados'.$id.'"',$this->formulario);
			//comprobamos si los pts validados y de baremo son iguales para poner en verde el titulo
			if($dsolicitud['baremo_puntos_totales']==$dsolicitud['baremo_puntos_validados'])
			{
				$this->formulario=str_replace('class="btn btn-primary bform crojo"','class="btn btn-primary bform cverde"',$this->formulario);
				$this->log_editar->warning("PUNTOS TOTALES/VALIDADOS ".$dsolicitud['baremo_puntos_totales']."/".$dsolicitud['baremo_puntos_validados']);
			}		
			//cambiamos el identificador de la sección del baremo
			$this->formulario=str_replace('id="labelbaremo"','id="labelbaremo'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="btotales"','id="btotales'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="bvalidados"','id="bvalidados'.$id.'"',$this->formulario);
			
			//cambiamos los id de los centros de escolarización de origen , si llos hay
			$this->formulario=str_replace('id="nuevaesc"','class="nuevaesc" id="nuevaesc'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="tnuevaesc"','id="tnuevaesc'.$id.'"',$this->formulario);
			$this->formulario=str_replace('class="row filanuevaesc"','class="row filanuevaesc'.$id.'"',$this->formulario);
			$this->formulario=str_replace('class="row freserva"','class="row freserva'.$id.'"',$this->formulario);

			$this->formulario=str_replace('id="hermanosadmision"','id="hermanosadmision'.$id.'"',$this->formulario);
			
			$this->formulario=str_replace('id="num_hadmision"','id="num_hadmision'.$id.'"',$this->formulario);
			$this->formulario=str_replace('id="thermanosadmision"','id="thermanosadmision'.$id.'"',$this->formulario);
			
			$this->formulario=str_replace('id="reserva"','id="reserva'.$id.'"',$this->formulario);
		
			//lo mismo para baremar hermanos, se muestra si se hace click en el check	
			$this->formulario=str_replace('id="num_hbaremo"','id="num_hbaremo'.$id.'" class="num_hbaremo"',$this->formulario);
			$this->formulario=str_replace('class="row hno_baremo"','class="row hno_baremo'.$id.'"',$this->formulario);
			//seccion tributantes
			$this->formulario=str_replace('id="labeltributo"','id="labeltributo'.$id.'"',$this->formulario);
			
			$this->formulario=str_replace('GRABAR','ACTUALIZAR',$this->formulario);
			$this->formulario=str_replace('form lang="es" id="fsolicitud"','form id="fsolicitud'.$id.'"',$this->formulario);
			$this->formulario=str_replace('send"','send" data-idal="'.$id.'"',$this->formulario);
			foreach($dsolicitud as $skey=>$sval)
				{
				//calculo escolariazacion
				if($skey=='nuevaesc') 
					{
					if($sval==0) $check="";
					else $check="checked";
					$this->formulario=str_replace('value="0" name="nuevaesc"','value="'.$sval.'" name="nuevaesc" '.$check,$this->formulario);
					if($check=="checked")
						{
						$this->formulario=str_replace('id="tnuevaesc'.$id.'"','id="tnuevaesc'.$id.'" style="display:none!important"',$this->formulario);
						$this->formulario=str_replace('class="row filanuevaesc'.$id.'"','class="row filanuevaesc'.$id.'" style="display:none!important"',$this->formulario);
						}
					continue;
					}
				if($skey=='num_hadmision') 
					{
					if($sval==0) $check="";
					else $check="checked";
					$this->formulario=str_replace('value="0" name="num_hadmision"','value="'.$sval.'" name="num_hadmision" '.$check,$this->formulario);
					if($check=="checked")
						{
						$this->formulario=str_replace('id="thermanosadmision'.$id.'"','id="thermanosadmision'.$id.'" style="display:flex!important"',$this->formulario);
						
						}
					continue;
					}
				//Para el caso de campos de opciones
				if($skey=='modalidad_origen')
					{
					$this->formulario=str_replace('id="'.$skey.'"','id="'.$skey.$id.'"',$this->formulario);
					$this->formulario=str_replace('value="'.$sval.'"','value="'.$sval.'" selected',$this->formulario);
					continue;
					}
				//CAMPOS DE SOLICITA
				if($skey=='tipoestudios')
					{	
					$val=strtoupper($sval);
					$origen="<option>$val</option>";
					$destino="<option selected>$val</option>";
					$this->formulario=str_replace($origen,$destino,$this->formulario);
					}

				//CAMPOS DE BAREMACION
				//calculo puntos baremo
				if($skey=='baremo_puntos_totales' or $skey=='baremo_puntos_validados') 
					{
					if($skey=='baremo_puntos_validados')
						{	
						$origen='<span id="id_puntos_baremo_validados'.$id.'">0</span>';
						$destino='<span id="id_puntos_baremo_validados'.$id.'">'.$sval.'</span>';
						$this->formulario=str_replace($origen,$destino,$this->formulario);
						}
					if($skey=='baremo_puntos_totales')
						{	
						$origen='<span id="id_puntos_baremo_totales'.$id.'">0</span>';
						$destino='<span id="id_puntos_baremo_totales'.$id.'">'.$sval.'</span>';
						$this->formulario=str_replace($origen,$destino,$this->formulario);
						}
					$this->formulario=str_replace('name="'.$skey.'" value="0"','name="'.$skey.'", value="'.$sval.'"'.$check,$this->formulario);
					continue;
					}
				//calculo hermanos baremo
				if($skey=='num_hbaremo') 
					{
					if($sval==0) $check="";
					else $check="checked";
					$this->formulario=str_replace('value="0" name="num_hbaremo"','value="'.$sval.'" name="num_hbaremo" '.$check,$this->formulario);
					if($check=="checked")
						$this->formulario=str_replace('class="row hno_baremo'.$id.'"','class="row hno_baremo'.$id.'" style="display:flex!important"',$this->formulario);
					continue;
					}
				//controles de formulario tipo radio
				if($skey=='baremo_proximidad_domicilio' or $skey=='baremo_discapacidad' or $skey=='baremo_tipo_familia' or $skey=='transporte' or $skey=='fase_solicitud' or $skey=='estado_solicitud' or $skey=='reserva')
					{
					$this->log_editar->warning("DATOS CAMPOS RESERVA O SIMILAR: clave- ".$skey." valor- ".$sval);
					
					$this->formulario=str_replace('name="'.$skey.'" value="'.$sval.'"','name="'.$skey.$id.'" value="'.$sval.'" checked="checked"',$this->formulario);
					$this->formulario=str_replace('name="'.$skey.'"','name="'.$skey.$id.'"',$this->formulario);
					if($sval=='dllimitrofe')
						$this->formulario=str_replace('id="calle_dllimitrofe" class="md-form mb-0" style="display:none"','id="calle_dllimitrofe" class="md-form mb-0" style="display:block"',$this->formulario);
					if($sval=='dlaboral')
						$this->formulario=str_replace('id="calle_dlaboral" class="md-form mb-0" style="display:none"','id="calle_dlaboral" class="md-form mb-0" style="display:block"',$this->formulario);
					
					if($skey=='reserva' and strrpos($dsolicitud['id_centro_estudios_origen'],'*')!==FALSE)	
						{
						$this->log_editar->warning("DATOS CAMPOS RESERVA:".$dsolicitud['id_centro_estudios_origen']);
						$this->formulario=str_replace('class="row freserva'.$id.'" style="display:none"','class="row freserva"',$this->formulario);
						}
					continue;
					}
				if($skey=='baremo_calle_dlaboral' or $skey=='baremo_calle_dllimitrofe')
					{
					$this->formulario=str_replace('name="'.$skey.'"','name="'.$skey.$id.'" value="'.$sval.'"',$this->formulario);
					continue;
					}
				//controles de validacion de baremo tipo radio
				if($skey=='baremo_validar_proximidad_domicilio' or $skey=='baremo_validar_discapacidad' or $skey=='baremo_validar_tipo_familia')
					{
						$soriginal='<input type="hidden" id="'.$skey.'" value="0" name="'.$skey.'">';
						$sdestino='<input type="hidden" id="'.$skey.$id.'" value="'.$sval.'" name="'.$skey.'">';
						$this->formulario=str_replace($soriginal,$sdestino,$this->formulario);

						//botones de validacion de baremo
						$origen='<button name="boton_'.$skey.'" type="button" class="btn btn-outline-dark validar">';
						$destino='<button name="boton_'.$skey.$id.'" type="button" class="btn btn-outline-dark validar">';
						$this->formulario=str_replace($origen,$destino,$this->formulario);
					if($sval==1)
						{
						$tval=end(explode('_',$skey));
						$soriginal='<button name="boton_'.$skey.$id.'" type="button" class="btn btn-outline-dark validar">Validar '.$tval.'</button>';
						$sdestino='<button name="boton_'.$skey.$id.'" type="button" class="btn btn-outline-dark validar">Invalidar '.$tval.'</button>';
						$this->formulario=str_replace($soriginal,$sdestino,$this->formulario);
						}
					continue;
					}
				//controles de validacion de baremo tipo check
				if($skey=='baremo_validar_tutores_centro' or $skey=='baremo_validar_renta_inferior' or $skey=='baremo_validar_hnos_centro')
					{
						$this->log_editar->warning("DATOS CAMPO VALIDACION CHECK: ");
						$soriginal='<input type="hidden" id="'.$skey.'" value="0" name="'.$skey.'">';
						$sdestino='<input type="hidden" id="'.$skey.$id.'" value="'.$sval.'" name="'.$skey.'">';
						$this->formulario=str_replace($soriginal,$sdestino,$this->formulario);
						if($sval==1)
						{
						if(strpos('tutores',$skey)!==FALSE) $cval="tutores trabajan centro";	
						if(strpos('renta',$skey)!==FALSE) $cval="renta";	
						if(strpos('hnos',$skey)!==FALSE) $cval="hermanos";
	
						$soriginal='<button name="boton_'.$skey.'" type="button" class="btn btn-outline-dark">Validar';
						$sdestino='<button name="boton_'.$skey.'" type="button" class="btn btn-outline-dark">Invalidar';
						$this->formulario=str_replace($soriginal,$sdestino,$this->formulario);
						}
				//botones de validacion de baremo
				$origen='<button name="boton_'.$skey.'" type="button" class="btn btn-outline-dark">';
				$destino='<button name="boton_'.$skey.$id.'" type="button" class="btn btn-outline-dark">';
				$this->formulario=str_replace($origen,$destino,$this->formulario);
					continue;
					}
				if($skey=='baremo_tutores_centro' or $skey=='baremo_renta_inferior')
					{
					if($sval==0) $check="";
					else
					{ 
						$check="checked";
						if($skey=='baremo_renta_inferior')
						{		
									$soriginal='id="tributo" style="display:none"';
									$sdestino='id="tributo" style="display:inline-block"';
									$this->formulario=str_replace($soriginal,$sdestino,$this->formulario);
									
									$soriginal='id="labeltributo'.$id.'" style="display:none"';
									$sdestino='id="labeltributo'.$id.'" style="display:inline-block"';
									$this->formulario=str_replace($soriginal,$sdestino,$this->formulario);
						}
					}
					$this->formulario=str_replace('id="'.$skey.'" value="0"','id="'.$skey.$id.'" value="'.$sval.'"'.$check,$this->formulario);
					continue;	
					}

				$this->formulario=str_replace('id="'.$skey.'" value=""','id="'.$skey.$id.'" value="'.$sval.'"',$this->formulario);
				$this->formulario=str_replace('id="'.$skey.'" value="0"','id="'.$skey.$id.'" value="'.$sval.'"',$this->formulario);
				}
		}

    public function procesarFormularioNuevo($nuevoid,$dsolicitud,$rol='alumno')
		{

			$this->formulario=str_replace('name="fase_solicitud"','name="fase_solicitud'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('name="estado_solicitud"','name="estado_solicitud'.$nuevoid.'"',$this->formulario);
		
			//ponemos valores por defecto en la fase y estado de solicitud
			$this->formulario=str_replace('value="borrador"','value="borrador" checked="checked"',$this->formulario);
			$this->formulario=str_replace('value="normal"','value="normal" checked="checked"',$this->formulario);
			
			$this->formulario=str_replace('div class="container" id="tablasolicitud"','div class="container" id="fnuevasolicitud"',$this->formulario);
			$this->formulario=str_replace('form lang="es" id="fsolicitud"','form id="fsolicitud'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('send"','send" data-idal="'.$nuevoid.'"',$this->formulario);

			//cambiamos los id de los centros de escolarización de origen , si llos hay
			$this->formulario=str_replace('id="tnuevaesc"','id="tnuevaesc'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('class="row filanuevaesc"','class="row filanuevaesc'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="hermanosadmision"','id="hermanosadmision'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="thermanosadmision"','id="thermanosadmision'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="reserva_plaza"','id="reserva_plaza'.$nuevoid.'"',$this->formulario);

			$this->formulario=str_replace('class="row freserva"','class="row freserva'.$nuevoid.'"',$this->formulario);
			
			//para nueva escolarizacion, se muestra si se hace click en el check	
			$this->formulario=str_replace('id="nuevaesc"','class="nuevaesc" id="nuevaesc'.$nuevoid.'"',$this->formulario);

			//lo mismo para baremar hermanos, se muestra si se hace click en el check	
			$this->formulario=str_replace('id="num_hbaremo"','id="num_hbaremo'.$nuevoid.'" class="num_hbaremo"',$this->formulario);
			$this->formulario=str_replace('class="row hno_baremo"','class="row hno_baremo'.$nuevoid.'"',$this->formulario);
				
			//Datos para el caluclo del baremo
			$this->formulario=str_replace('id="btotales"','id="btotales'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="bvalidados"','id="bvalidados'.$nuevoid.'"',$this->formulario);
			
			//Datos del baremo
			
			//tipo radio, no tiene id sino name

			$this->formulario=str_replace('name="baremo_proximidad_domicilio"','name="baremo_proximidad_domicilio'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('name="baremo_tutores_centro"','name="baremo_tutores_centro'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('name="baremo_renta_inferior"','name="baremo_renta_inferior'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_tutores_centro"','id="baremo_tutores_centro'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_renta_inferior"','id="baremo_renta_inferior'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('name="baremo_discapacidad"','name="baremo_discapacidad'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('name="baremo_tipo_familia"','name="baremo_tipo_familia'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('name="transporte"','name="transporte'.$nuevoid.'"',$this->formulario);
			
			
			//validacion del baremo

			//Ocultamos boton para alumnos
			if($rol=='alumno')
				{
				$this->formulario=preg_replace('/<button name="boton.* class="btn btn-outline-dark validar".*<\/button>/','',$this->formulario);
				$this->formulario=preg_replace('/<button name="boton.* class="btn btn-outline-dark".*<\/button>/','',$this->formulario);
				}
			$this->formulario=str_replace('name="boton_baremo_validar_proximidad_domicilio"','name="boton_baremo_validar_proximidad_domicilio'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="labelbaremo"','id="labelbaremo'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_validar_proximidad_domicilio"','id="baremo_validar_proximidad_domicilio'.$nuevoid.'"',$this->formulario);
			
			$this->formulario=str_replace('name="boton_baremo_validar_tutores_centro"','name="boton_baremo_validar_tutores_centro'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_validar_tutores_centro"','id="baremo_validar_tutores_centro'.$nuevoid.'"',$this->formulario);
			
			$this->formulario=str_replace('name="boton_baremo_validar_renta_inferior"','name="boton_baremo_validar_renta_inferior'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_validar_renta_inferior"','id="baremo_validar_renta_inferior'.$nuevoid.'"',$this->formulario);
			
			$this->formulario=str_replace('name="boton_baremo_validar_hnos_centro"','name="boton_baremo_validar_hnos_centro'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_validar_hnos_centro"','id="baremo_validar_hnos_centro'.$nuevoid.'"',$this->formulario);
			
			$this->formulario=str_replace('name="boton_baremo_validar_discapacidad"','name="boton_baremo_validar_discapacidad'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_validar_discapacidad"','id="baremo_validar_discapacidad'.$nuevoid.'"',$this->formulario);

			$this->formulario=str_replace('name="boton_baremo_validar_tipo_familia"','name="boton_baremo_validar_tipo_familia'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="baremo_validar_tipo_familia"','id="baremo_validar_tipo_familia'.$nuevoid.'"',$this->formulario);
			
			$this->formulario=str_replace('id="id_puntos_baremo_totales"','id="id_puntos_baremo_totales'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="id_puntos_baremo_validados"','id="id_puntos_baremo_validados'.$nuevoid.'"',$this->formulario);
			
			$this->formulario=str_replace('class="proxdomi"','class="proxdomi'.$nuevoid.'"',$this->formulario);
			
			//Datos de hermanos del baremo

			$this->formulario=str_replace('id="hermanos_datos_baremo1"','id="hermanos_datos_baremo1'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="hermanos_datos_baremo2"','id="hermanos_datos_baremo2'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="hermanos_datos_baremo3"','id="hermanos_datos_baremo3'.$nuevoid.'"',$this->formulario);
			
			//Tributantes
			$this->formulario=str_replace('id="labeltributo"','id="labeltributo'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="oponenautorizar"','id="oponenautorizar'.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="cumplen"','id="cumplen'.$nuevoid.'"',$this->formulario);
			for($i=0;$i<4;$i++)
			{	
			$this->formulario=str_replace('id="tributantes_nombre'.$i.'"','id="tributantes_nombre'.$i.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="tributantes_apellido1'.$i.'"','id="tributantes_apellido1'.$i.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="tributantes_apellido2'.$i.'"','id="tributantes_apellido2'.$i.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="tributantes_parentesco'.$i.'"','id="tributantes_parentesco'.$i.$nuevoid.'"',$this->formulario);
			$this->formulario=str_replace('id="tributantes_dni'.$i.'"','id="tributantes_dni'.$i.$nuevoid.'"',$this->formulario);
			}
			foreach($dsolicitud as $skey=>$sval)
			{
				
				if($skey=='id_centro_destino' and $rol!='alumno')
				$this->formulario=str_replace('id="'.$skey.'" value=""','id="'.$skey.$nuevoid.'" value="'.$dsolicitud['nombre_centro_destino'].'" disabled',$this->formulario);
				else $this->formulario=str_replace('id="'.$skey.'"','id="'.$skey.$nuevoid.'"',$this->formulario);
			}
		return 1;
		}

    public function showFormSolicitud($id=0,$id_centro=0,$rol='alumno',$collapsed=1,$imprimir=0)
		{
			//Creamos una nueva solicitud
			$solicitud=new Solicitud($this->adapter);
			//nueva solicitud
			if($id==0)
			{ 
				$this->lastid=$solicitud->getLast();	
				$nuevoid=$this->lastid+1;
				$dsolicitud=$solicitud->getSol_pruebas($this->lastid,'nueva',$id_centro);
				
				$this->procesarFormularioNuevo($nuevoid,$dsolicitud,$rol);
			}
			//modificacion solicitud
			else
			{
				$dsolicitud=$solicitud->getSol_pruebas($id,'existente',0);
				
				$this->log_editar->warning("DATOS SOLICITUD A EDITAR");
				$this->log_editar->warning(print_r($dsolicitud,true));
				
				$this->procesarFormularioExistente($id,$dsolicitud,$collapsed,$rol,$imprimir);
			}
		return $this->formulario;
		}

  public function showSolicitud($sol){
		
		$sol=(object)$sol;
		$li="<tr class='filasol' id='filasol".$sol->id_alumno."' style='color:black'>";
		$li.="<td class='calumno dalumno' data-idal='".$sol->id_alumno."'>".$sol->id_alumno."-".strtoupper($sol->apellido1).",".strtoupper($sol->nombre)."</td>";
		$li.="<td id='print".$sol->id_alumno."' class='fase printsol'><i class='fa fa-print psol' aria-hidden='true'></i></td>";
		$li.="<td id='fase".$sol->id_alumno."' class='fase'>".$sol->fase_solicitud."</td>";
		$li.="<td id='estado".$sol->id_alumno."' class='estado'>".$sol->estado_solicitud."</td>";
		$li.="<td id='tipoens".$sol->id_alumno."'>".$sol->tipoestudios."</td>";
		$li.="<td id='transporte".$sol->id_alumno."'>".$sol->transporte."</td>";
		$li.="<td id='baremo".$sol->id_alumno."'>".$sol->puntos_validados."</td>";
		$li.="<td id='nordensorteo".$sol->id_alumno."'>".$sol->nordensorteo."</td>";
		$li.="<td id='nasignado".$sol->id_alumno."'>".$sol->nasignado."</td>";
		$li.="</tr>";
	return $li;
	}
    public function showSolicitud_old2($sol)
		{
		$li="<tr class='filasol' id='filasol".$sol['id_alumno']."'>";
		$li.="<span id='estado".$sol['id_alumno']."'>".$sol['estado']."</span>";
                $li.="<span class='calumno dalumno' data-idal='".$sol['id_alumno']."'>".$sol['id_alumno']."-".strtoupper($sol['apellido1']).",";
                $li.= strtoupper($sol['apellido1']);
                $li.='<div class="right" id="'.$sol['estado'].'"><a  class="btn btn-danger estado" id="'.$sol['id_alumno'].'" >BORRADOR</a></div>&nbsp
                <div class="right" id="'.$sol['estado'].'"><a  class="btn btn-info estado" id="'.$sol['id_alumno'].'" >PROVISIONAL</a></div>&nbsp;
                <div class="right" id="'.$sol['estado'].'"><a  class="btn btn-success estado" id="'.$sol['id_alumno'].'" >DEFINITIVO</a></div>
                <hr/></span>';
        $li.='</tr>';

		return $li;
		}
    public function showSolicitud_old($sol)
		{
		$li="<div class='filasol' id='filasol".$sol['id_alumno']."'>";
		$li.="<span id='estado".$sol['id_alumno']."'>".$sol['estado']."</span>";
                $li.="<span class='calumno dalumno' data-idal='".$sol['id_alumno']."'>".$sol['id_alumno']."-".strtoupper($sol['apellido1']).",";
                $li.= strtoupper($sol['apellido1']);
                $li.='<div class="right" id="'.$sol['estado'].'"><a  class="btn btn-danger estado" id="'.$sol['id_alumno'].'" >BORRADOR</a></div>&nbsp
                <div class="right" id="'.$sol['estado'].'"><a  class="btn btn-info estado" id="'.$sol['id_alumno'].'" >PROVISIONAL</a></div>&nbsp;
                <div class="right" id="'.$sol['estado'].'"><a  class="btn btn-success estado" id="'.$sol['id_alumno'].'" >DEFINITIVO</a></div>
                <hr/></span>';
        $li.='</div>';

		return $li;
		}
    
		public function getConexion()
		{
			return $this->adapter;
		}
     
  public function crear(){
  	if(isset($_POST["nombre"]))
		{
					//Creamos un usuario
					$alumno=new Alumno($this->adapter);
					$alumno->setNombre($_POST["nombre"]);
					$alumno->setApellido1($_POST["apellido1"]);
					$alumno->setApellido2($_POST["apellido2"]);
					$alumno->setDni($_POST["dni"]);
					$alumno->setFnac($_POST["fnac"]);
					$alumno->setNacionalidad($_POST["nacionalidad"]);
					$save=$alumno->save();
        }
       // $this->redirect("Alumnos", "index");
    }
     
    public function borrar(){
        if(isset($_GET["id"])){
            $id=(int)$_GET["id"];
             
            $alumno=new Alumno($this->adapter);
            $alumno->deleteById($id);
        }
        $this->redirect();
    }
     
}
?>
