<?php
class Solicitud extends EntidadBase{
    private $id_alumno;
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $dni;
    private $fnac;
    private $nacionalidad;
    private $loc_dfamiliar;
     
    public function __construct($adapter) 
		{
			parent::__construct($adapter,"alumnos");
			require_once DIR_CLASES.'LOGGER.php';
			require_once DIR_APP.'parametros.php';
			require_once DIR_BASE.'/scripts/informes/pdf/fpdf/classpdf.php';
			$this->log_consultas_nuevasolicitud=new logWriter('log_consultas_nuevasol',DIR_LOGS);
			$this->log_nueva_solicitud=new logWriter('log_nueva_solicitud',DIR_LOGS);
			$this->log_actualizar_solicitud=new logWriter('log_actualizar_solicitud',DIR_LOGS);
			$this->log_mostrar_solicitud=new logWriter('log_mostrar_solicitud',DIR_LOGS);
			$this->log_sorteo=new logWriter('log_sorteo',DIR_LOGS);
			$this->log_listado_solicitudes=new logWriter('log_listado_solicitudes',DIR_LOGS);
			$this->log_listados_definitivos=new logWriter('log_listados_definitivos',DIR_LOGS);
			$this->log_solpdf=new logWriter('log_solpdf',DIR_LOGS);
			$this->log_gencsvs=new logWriter('log_gencsvs',DIR_LOGS);
			$this->log_listados_provisionales=new logWriter('log_listados_provisionales',DIR_LOGS);
    }
     
  public function copiaTablaProvisionales($centro)
	{
		$sql_provisionales='DELETE alumnos_provisional from alumnos_provisional where id_centro_destino='.$centro;
		if($this->db()->query($sql_provisionales)==0) return 0;

		$sql_provisionales='INSERT IGNORE INTO alumnos_provisional SELECT * from alumnos where id_centro_destino='.$centro;
		$this->log_sorteo->warning("CARGANDO TABLA PROVISIONALES: ".$sql_provisionales);
		if($this->db()->query($sql_provisionales)) return 1;
		else return 0;

	}
  public function crearPdf($id)
	{
	$file="sol".$id;
	$pdf = new PDF();
	$pdf->SetFont('Arial','',14);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$texto="We could have specified italics with I, underlined with U or a regular font with an empty string
 (or any combination). Note that the font size is given in points, not millimeters (or another user unit); 
it's the only exception. The other standard fonts are Times, Courier, Symbol and ZapfDingbats.
We can now print a cell with Cell(). A cell is a rectangular area, possibly framed, which contains a line of text. It is output at the current position. We specify its dimensions, its text (centered or aligned), if borders should be drawn, and where the current position moves after it (to the right, below or to the beginning of the next line). To add a frame, we would do this: ";
	$texto1="We could have specified italics with I, underlined with U or a regular font with an empty string";
	$pdf->MultiCell(0,10,$texto);

	$pdf->Output(DIR_BASE.'/scripts/datossalida/pdfsolicitudes/'.$file.'.pdf','F');
	$dsolicitud=$this->getSol_pruebas($id,'existente',0);	
	$this->log_solpdf->warning("CREANDO PDF SOLICITUD");
	$this->log_solpdf->warning(print_r($dsolicitud,true));
	return 1;
	}

  public function update($sol,$id){
  	$query_alumnos="UPDATE alumnos set ";
  	$query_hermanos="UPDATE hermanos set ";
  	$query_baremo="UPDATE baremo set ";
  	$query_tributantes="UPDATE tributantes set ";
		$this->log_actualizar_solicitud->warning("ENTRANDO EN UPDATE");
		//creamos la consulta para la tabla de alumnos
		//controlamos si hay algun cambio en los datos
		$dalumno=0;
		$dhermanos=0;
		$dbaremo=0;
		$numhermanos_baremo=0;
		$numhermanos_admision=0;
		$num_tributantes=0;
		$hermanos_admision=array();
		$hermanos_baremo=array();
		$tributantes=array();
		
		//generamos datos para la tabla baremo. Campos con prefijo baremo_
		foreach($sol as $key=>$elto)
		{
		if(strpos($key,'baremo_')!==false) 
			{
			if(strlen($elto)==0) continue;
			
			$key=str_replace("baremo_","",$key);	
			$key=str_replace($id,"",$key);	
			$query_baremo.=$key."='".$elto."',";	
			//contamos los hermanos que hay
			if(strpos($key,'hermanos_datos_baremo')!==false) $numhermanos_baremo++; 
			$dbaremo=1;
			}
		
		}
		if($dbaremo==1)
		{
    //cerramos actualizacion baremo
		$sql_baremo=trim($query_baremo,',')." WHERE id_alumno=".$id;
		if($dbaremo==1) $update=$this->db()->query($sql_baremo);
		$this->log_actualizar_solicitud->warning("CONSULTA ACTUALIZACION BAREMO");
		$this->log_actualizar_solicitud->warning($sql_baremo);
		}

		/*
		//generamos datos para la tabla tributantes. Campos con prefijo tributantes_
		foreach($sol as $key=>$elto)
		{
		if(strpos($key,'tributantes_')!==false) 
			{
			if(strlen($elto)==0) continue;
			
			$key=str_replace("tributantes_","",$key);	
			$key=str_replace($id,"",$key);	
			$query_tributantes.=$key."='".$elto."',";	
			//contamos los hermanos que hay
			if(strpos($key,'tributantes_')!==false) $num_tributantes++; 
			$dtributantes=1;
			}
		}
		if($dtributantes==1)
		{
    //cerramos actualizacion tributantes
		$sql_baremo=trim($query_baremo,',')." WHERE id_alumno=".$id;
		if($dbaremo==1) $update=$this->db()->query($sql_baremo);
		$this->log_actualizar_solicitud->warning("CONSULTA ACTUALIZACION BAREMO");
		$this->log_actualizar_solicitud->warning($sql_baremo);
		}
		*/
		//generamos datos para la tabla alumnos. Sin campos con prefijo -hermanos_-
		foreach($sol as $key=>$elto)
		{
		if(strpos($key,'baremo_')!==false) continue;
		if(strpos($key,'fase_solicitud')!==false){
			$query_alumnos.="fase_solicitud"."='".$elto."',";	
					continue;
					} 
		if(strpos($key,'reserva')!==false){
			$query_alumnos.="reserva"."='".$elto."',";	
					continue;
					} 
		if(strpos($key,'transporte')!==false){
			$query_alumnos.="transporte"."='".$elto."',";	
					continue;
					} 
		if(strpos($key,'estado_solicitud')!==false){
			$query_alumnos.="estado_solicitud"."='".$elto."',";	
					continue;
					} 
		//capturamos datos de alumnos
		if(strpos($key,'hermanos')===false and strpos($key,'tributantes_')===false) 
			{
			if(strlen($elto)==0) continue;
			$query_alumnos.=$key."='".$elto."',";	
			$dalumnos=1;
			}
		//generamos array para datos de hermanos en admision
		elseif(strpos($key,'admision')!==false)
			{
				//obtenemos el numero de hermano y el nombre del campo
				$campo=str_replace('_admision','',$key);
				$campo=str_replace('hermanos_','',$campo);
				$idc=substr($campo,-1);
				$campo=str_replace($idc,'',$campo);
				$hermanos_admision[$idc][$campo]=$sol[$key];
			}
		//generamos array para datos de hermanos en baremo
		elseif(strpos($key,'_baremo')!==false)
			{
				//obtenemos el numero de hermano y el nombre del campo
				$campo=str_replace('_baremo','',$key);
				$campo=str_replace('hermanos_','',$campo);
				$idc=substr($campo,-1);
				$campo=str_replace($idc,'',$campo);
				$hermanos_baremo[$idc][$campo]=$sol[$key];
			}
		//generamos array para datos de tributantes
		elseif(strpos($key,'tributantes_')!==false)
			{
				//obtenemos el numero de hermano y el nombre del campo
				$campo=str_replace('tributantes_','',$key);
				$idc=substr($campo,-1);
				//$campo=str_replace($idc,'',$campo);
				$campo=substr($campo,0,-1);
				$tributantes[$idc][$campo]=$sol[$key];
			}
		
		}
	
		//cerramos actualizacion alumno
    $sql=trim($query_alumnos,',')." WHERE id_alumno=".$id;

		if($dalumnos==1) $update=$this->db()->query($sql);
		$this->log_actualizar_solicitud->warning("CONSULTA ACTUALIZACION SOLICITUD ALUMNOS");
		$this->log_actualizar_solicitud->warning($sql);
    
		$this->log_actualizar_solicitud->warning("DATOS ACTUALIZACION SOLICITUD HERMANOS: ");
		$this->log_actualizar_solicitud->warning(print_r($hermanos_admision,true));

		//creamos la consulta para la tabla de hermanos, para admision
		foreach($hermanos_admision as $hermano)
		{
		$dhermanos=0;
		//si no hay datos de nombre de hermanos borramos el registro
		if($hermano['datos']=='') 
				{
							$sql="DELETE from hermanos where id_registro=".$hermano['id_registro'];
							$delete=$this->db()->query($sql);
			  			$this->log_actualizar_solicitud->warning("BORRANDO HERMANOS ADMISION");
							$this->log_actualizar_solicitud->warning($sql);
							continue;
				}
		else 
				{
				//comprobamos si hay hermanos en la tabla, en caso contrario hay q insertarlos
				$existe_hermano=$this->comprobarHermano($hermano['id_registro'],'admision');	
				
				$dhermanos=1;
				if($hermano['fnacimiento']=='') $hermano['fnacimiento']='2000-01-01';
				//if($hermano['id_registro']!=0)
				if($existe_hermano==1)
					{
					$sql=$query_hermanos."datos='".$hermano['datos']."',fnacimiento='".$hermano['fnacimiento']."',curso='".$hermano['curso']."',nivel_educativo='".$hermano['nivel_educativo']."',tipo='admision'";	
    			$sql=trim($sql,',')." WHERE id_registro='".$hermano['id_registro']."'";
					}
				else
					$sql="INSERT INTO hermanos VALUES(0,$id,'".$hermano['datos']."','".$hermano['fnacimiento']."','".$hermano['curso']."','".$hermano['nivel_educativo']."','admision')";
				
				$update=$this->db()->query($sql);
			  $this->log_actualizar_solicitud->warning("CONSULTA ACTUALIZACION SOLICITUD HERMANOS ADMISION");
				$this->log_actualizar_solicitud->warning($sql);
			}
		}
		//creamos la consulta para la tabla de hermanos, para baremo
		foreach($hermanos_baremo as $hermano)
		{
		$dhermanos=0;
		if($hermano['datos']=='') continue;
		else 
				{
				//comprobamos si hay hermanos en la tabla, en caso contrario hay q insertarlos
				$existe_hermano=$this->comprobarHermano($hermano['id_registro'],'baremo');	
				
				$dhermanos=1;
				if($hermano['fnacimiento']=='') $hermano['fnacimiento']='2000-01-01';
				if($existe_hermano==1)
					{
					$sql=$query_hermanos."datos='".$hermano['datos']."',fnacimiento='".$hermano['fnacimiento']."',curso='".$hermano['curso']."',nivel_educativo='".$hermano['nivel_educativo']."',tipo='baremo'";	
    			$sql=trim($sql,',')." WHERE id_registro='".$hermano['id_registro']."'";
					}
				else
					$sql="INSERT INTO hermanos VALUES(0,$id,'".$hermano['datos']."','".$hermano['fnacimiento']."','".$hermano['curso']."','".$hermano['nivel_educativo']."','baremo')";
				
				$update=$this->db()->query($sql);
			  $this->log_actualizar_solicitud->warning("CONSULTA ACTUALIZACION SOLICITUD HERMANOS BAREMO");
				$this->log_actualizar_solicitud->warning($sql);
			}
		}
		//creamos la consulta para la tabla de tributantes
		$this->log_actualizar_solicitud->warning("INICIANDO ACTUALIZACION SOLICITUD TRIBUTANTES");
		$this->log_actualizar_solicitud->warning(print_r($tributantes,true));
		foreach($tributantes as $tributante)
		{
		$dtributantes=0;
		if($tributante['nombre']=='') continue;
		else 
				{
		$this->log_actualizar_solicitud->warning("NOMBRE TRIBUTANTE EXISTE");
				//comprobamos si hay hermanos en la tabla, en caso contrario hay q insertarlos
				if($tributante['id_tributante']!=0)
					{
					$sql=$query_tributantes."nombre='".$tributante['nombre']."',apellido1='".$tributante['apellido1']."',apellido2='".$tributante['apellido2']."',parentesco='".$tributante['parentesco']."',dni='".$tributante['dni']."'";	
    			$sql=trim($sql,',')." WHERE id_tributante='".$tributante['id_tributante']."'";
					}
				else
					$sql="INSERT INTO tributantes VALUES($id,0,'".$tributante['nombre']."','".$tributante['apellido1']."','".$tributante['apellido2']."','".$tributante['parentesco']."','".$tributante['dni']."')";
				
				$update=$this->db()->query($sql);
			  $this->log_actualizar_solicitud->warning("CONSULTA ACTUALIZACION SOLICITUD TRIBUTANTES");
				$this->log_actualizar_solicitud->warning($sql);
			}
		}

		return 1;
    }

    public function comprobarHermano($idregistro,$tipo){

			$query="SELECT * FROM hermanos WHERE id_registro=$idregistro";	
			
			$res=$this->db()->query($query);
			$this->log_actualizar_solicitud->warning("CONSULTA COMPROBACION HERMANOS ADMISION--".$res->num_rows);
			$this->log_actualizar_solicitud->warning($query);
			if($res->num_rows>0) return 1;
			else return 0;
		}

    public function get_asignado($tipo,$idcentro){
			$query="SELECT nasignado FROM alumnos WHERE id_centro_destino=$idcentro and tipoestudios='$tipo' order by nasignado desc limit 1";	
			$this->log_nueva_solicitud->warning("CONSULTA NUM ASIGNADO: ".$query);
			$r=$this->db()->query($query);
			$row = $r->fetch_object();
			if(isset($row))	return $row->nasignado;
			else return 0;
		}

    public function get_datos_tabla($datos,$tabla,$id=0,$tipo){
		$darray=array();
		//quitamos el id del array para recoger solo las claves de hermanos
		unset($datos['id_alumno']);
		if($tabla=='alumnos')
		{
		foreach($datos as $key=>$elto)
			{
			#recogemos la fase y el estado de la solicitud
			if(strpos($key,'transporte')!==FALSE){ $darray['transporte']=$elto;continue;}
			if(strpos($key,'fase_solicitud')!==FALSE){ $darray['fase_solicitud']=$elto;continue;}
			if(strpos($key,'estado_solicitud')!==FALSE){ $darray['estado_solicitud']=$elto;continue;}
			if(strpos($key,'tributantes_')!==FALSE)  continue;
			if(strpos($key,'baremo_')===FALSE && strpos($key,'hermanos_')===FALSE)   $darray[$key]=$elto;
			}
		//determinamos el tipo de alumno, de momento ponemos ebo
		$tipoestudios='ebo';
		//asignamos un número para el sorteo, será el siguiente al último
			$darray['nasignado']=$this->get_asignado($tipoestudios,$darray['id_centro_destino'])+1;
		}
		elseif($tabla=='hermanos')
		{
			foreach($datos as $key=>$elto)
			{
			if(strpos($key,$tabla)!==FALSE)
			{
		//distinguimos entre datos de hermanos para el baremo y para la admision
			if($tipo=='baremo')
				{
				if(strpos($key,'_baremo')!==FALSE) 
					$darray[$key]=$elto;
				}
			elseif($tipo=='admision')
				{
				if(strpos($key,'_admision')!==FALSE) 
					{
					$darray[$key]=$elto;
					}
				}
			}
		}
			$darray['id_alumno']=$id;
		}
		elseif($tabla=='tributantes')
		{
			foreach($datos as $key=>$elto)
			{
				if(strpos($key,$tabla)!==FALSE)
				{
					$darray[$key]=$elto;
				}
			}
		$darray['id_alumno']=$id;
		$this->log_nueva_solicitud->warning("DATOS TRIBUTANTES");
		$this->log_nueva_solicitud->warning(print_r($darray,true));
		}
		else //tabla de baremo
		{
				$darray['id_alumno']=$id;
				foreach($datos as $key=>$elto)
					if(strpos($key,'baremo_')!==FALSE) 
						$darray[$key]=$elto;
		}
	return $darray;
	}

  public function save_tributantes($data)
	{
		$this->log_nueva_solicitud->warning("DATOS INSERCION TRIBUTANTE:");
		$this->log_nueva_solicitud->warning(print_r($data,true));
		$i=6;
		$guardar=1;
	foreach($data as $key=>$elto)
		{
		if($i%6==0) 
		{
			if($i!=6){
					$query.=")";
					//solo guardamos si hay datos
					if($guardar==1)	
					{
						$this->log_nueva_solicitud->warning("CONSULTA INSERCION TRIBUTANTES: ");
						$this->log_nueva_solicitud->warning($query);
						$savetributante=$this->db()->query($query);
					}
					$guardar=1;
				}
			//solo guardamos si hay datos
			if($data[$key]=='') $guardar=0;
			$query="INSERT INTO tributantes(id_alumno,nombre,id_tributante,apellido1,apellido2,parentesco,dni) VALUES('".$data['id_alumno']."'"; 
			$query.=",'".$data[$key]."'";
			$i++;
		}
		else
		{
			if($data[$key]==''){ $data[$key]='DEFAULT';$query.=",".$data[$key];}
			else  $query.=",'".$data[$key]."'";
			 $i++;
		}
		}
	return 1;
	}
  public function save_tributantes_old($data)
	{
		$query="INSERT INTO tributantes("; 
		foreach($data as $key=>$elto)
			 {
			 if(strpos($key,'tipo_familia')!==FALSE)  $key='tipo_familia';
			 if(strlen($elto)!=0)
				{ 
				//quitamos el indice del campo
				if($key!='id_alumno') $key=substr($key, 0, -1);
				$query.=str_replace('tributantes_','',$key).",";
				}
			 }
		$query=trim($query,',');
		$query.=") VALUES(";
		foreach($data as $key=>$elto)
			 {
			 if(strlen($elto)!=0) 
				$query.="'".$elto."',";
			 	
			 }
		$query=trim($query,',');
		$query.=")";
		
		$savebaremo=$this->db()->query($query);

		$this->log_nueva_solicitud->warning("CONSULTA INSERCION TRIBUTANTE:");
		$this->log_nueva_solicitud->warning($query);

		if($savebaremo) return 1;
		else return 0;
	}
  public function save_baremo($data)
	{
		$query="INSERT INTO baremo("; 
		foreach($data as $key=>$elto)
			 {
			//Campos de tipo RADIO se llaman igual, hay q quitarles el codigo o id
			 if(strpos($key,'tutores_centro')!==FALSE and strpos($key,'validar')===FALSE)  $key='tutores_centro';
			 if(strpos($key,'renta_inferior')!==FALSE and strpos($key,'validar')===FALSE)  $key='renta_inferior';
			 if(strpos($key,'proximidad_domicilio')!==FALSE and strpos($key,'validar')===FALSE)  $key='proximidad_domicilio';
			 if(strpos($key,'discapacidad')!==FALSE and strpos($key,'validar')===FALSE)  $key='discapacidad';
			 if(strpos($key,'tipo_familia')!==FALSE and strpos($key,'validar')===FALSE)  $key='tipo_familia';
			 if(strlen($elto)!=0) $query.=str_replace('baremo_','',$key).",";
			 }
		$query=trim($query,',');
		$query.=") VALUES(";
		foreach($data as $key=>$elto)
			 {
			 if(strlen($elto)!=0) 
				$query.="'".$elto."',";
			 	
			 }
		$query=trim($query,',');
		$query.=")";
		
		$savebaremo=$this->db()->query($query);

		$this->log_nueva_solicitud->warning("CONSULTA INSERCION BAREMO:");
		$this->log_nueva_solicitud->warning($query);

		if($savebaremo) return 1;
		else return 0;
	}
  public function save_hermanos($data,$tipo)
	{
		$this->log_nueva_solicitud->warning("DATOS INSERCION HERMANOS:");
		$this->log_nueva_solicitud->warning(print_r($data,true));
		$i=5;
		$guardar=1;
	foreach($data as $key=>$elto)
		{
		if($i%5==0) 
		{
			if($i!=5){
					$query.=",'".$tipo."')";
					//solo guardamos si hay datos
					if($guardar==1)	
					{
						$this->log_nueva_solicitud->warning("CONSULTA INSERCION HERMANOS ".$tipo.":");
						$this->log_nueva_solicitud->warning($query);
						$savehermano=$this->db()->query($query);
					}
					$guardar=1;
				}
			//solo guardamos si hay datos
			if($data[$key]=='') $guardar=0;
			$query="INSERT INTO hermanos(id_alumno,datos,id_registro,fnacimiento,curso,nivel_educativo,tipo) VALUES('".$data['id_alumno']."'"; 
			$query.=",'".$data[$key]."'";
			$i++;
			
		}
		else
		{
			if($data[$key]==''){ $data[$key]='DEFAULT';$query.=",".$data[$key];}
			else  $query.=",'".$data[$key]."'";
			 $i++;
		}
		}
	}
  
	public function nvalores_array($array)
	{
		$nv=0;
		foreach($array as $key=>$elto)
		{
		if($elto!='') $nv++;
		}
	return $nv;
	}
	
	public function existeCuenta($clave=0,$usuario='')
	{
	$squery="SELECT nombre_usuario,clave_original FROM usuarios where nombre_usuario=".$usuario." and clave_original=".$clave;
   	$this->log_nueva_solicitud->warning("DATOS CUENTA ".$squery); 
	$query=$this->db->query($squery);
	if($query->num_rows>0) return 0;
	else return 1;
	}
	public function save($sol,$id,$rol='centro')
	{
	$nsol=$sol;
	$this->log_nueva_solicitud->warning("ENTRANDO EN SAVE");
	$hadmision='si';
	$hbaremo='si';

	//comprobamos si se ha marcado  hermanos en admision o en baremo
	if(!isset($sol['num_hadmision']))  
		{
		$this->log_nueva_solicitud->warning("NO HAY HERMANOS EN ADMISION");
		$hadmision='no';
		}
	if(!isset($sol['num_hbaremo']))  
		{
		$this->log_nueva_solicitud->warning("NO HAY HERMANOS EN BAREMO");
		$hbaremo='no';
		}

	//filtramos los datos de los alumnos
	$sol=$this->get_datos_tabla($sol,'alumnos',0,'');	
	$this->log_nueva_solicitud->warning("DATOS DE ALUMNOS");
	$this->log_nueva_solicitud->warning(json_encode($sol));

	$clave=rand(1000,9999);
	while($this->existeCuenta($clave,$sol['dni_tutor1'])==0)	
		$clave=rand(1000,9999);

	if(strlen($sol['dni_tutor1']==0)) return 0; 
	
	$this->log_nueva_solicitud->warning("CONSULTA INSERCION USUARIO:");
  $query="INSERT INTO usuarios(nombre_usuario,rol,clave,clave_original) VALUES('".$sol['dni_tutor1']."','alumno',md5('".$clave."'),$clave)";
	$this->log_nueva_solicitud->warning($query);
	
	$saveusuario=$this->db()->query($query);


	//PROCESANDO ALUMNO
	if($saveusuario)
		{
			$this->log_nueva_solicitud->warning("USUARIO INSERTADO, ENTRANDO EN ALUMNO");
			$id_usuario=$this->getLast('usuario',$clave);
			if($id_usuario==0) 
				{
				$this->log_nueva_solicitud->warning("NO SE HA OBTENIDO ID USUARIO");
				return 0;
				}
			$sol['id_usuario']=$id_usuario;
			$query="INSERT INTO alumnos(id_alumno,"; 
			foreach($sol as $key=>$elto)
				 {
				 if(strlen($elto)!=0) $query.=$key.",";
				 }
			$query=trim($query,',');
			$query.=") VALUES(0,";
			#$query.="'".$id."',";
			//obtenemos los valores del alumno	
			foreach($sol as $key=>$elto)
				 {
				 if($key=='hadmision') continue;
				 if(strlen($elto)!=0)
				 	if($key=='id_centro_estudios_origen')
						 $query.="'".trim($elto,'*')."',";
					else
						 $query.="'".$elto."',";
				 }
			$query=trim($query,',');
			$query.=")";
			#$savealumno=$this->db()->query($query);
			$id_alumno=$this->insertarAlumno($query);
			$this->log_nueva_solicitud->warning("CONSULTA INSERCION ALUMNO:");
			$this->log_nueva_solicitud->warning($query);
			$this->log_nueva_solicitud->warning("ID INSERCION ALUMNO:");
			$this->log_nueva_solicitud->warning($id_alumno);
			
			if($id_alumno)
			{
				//filtramos los datos del baremo
				$sol_baremo=$this->get_datos_tabla($nsol,'baremo',$id_alumno,'baremo');	
				$this->log_nueva_solicitud->warning("DATOS DE BAREMO");
				$this->log_nueva_solicitud->warning(json_encode($sol_baremo));
				
				//filtramos los datos de hermanso, de admision y baremo
				$solhermanos_admision=$this->get_datos_tabla($nsol,'hermanos',$id_alumno,'admision');	
				$solhermanos_baremo=$this->get_datos_tabla($nsol,'hermanos',$id_alumno,'baremo');	
				$sol_tributantes=$this->get_datos_tabla($nsol,'tributantes',$id_alumno,'tributantes');	
				if($hadmision=='si')
					$savehermanos_admision=$this->save_hermanos($solhermanos_admision,'admision');
				if($hbaremo=='si')
					$savehermanos_baremo=$this->save_hermanos($solhermanos_baremo,'baremo');
				if($this->save_baremo($sol_baremo))
				{
					$this->log_nueva_solicitud->warning("GUARDADO BAREMO");
					$this->log_nueva_solicitud->warning("ENTRANDO TRIBUTANTES:");
					$this->log_nueva_solicitud->warning(print_r($sol_tributantes,true));
					$this->log_nueva_solicitud->warning($this->nvalores_array($sol_tributantes));

					if($this->nvalores_array($sol_tributantes)>1)
					{
						$this->log_nueva_solicitud->warning("GUARDANDO TRIBUTANTE");
						if($this->save_tributantes($sol_tributantes))
						{
							$this->log_nueva_solicitud->warning("GUARDADO TRIBUTANTE");
						}
						else
						{
						$this->log_nueva_solicitud->warning("ERROR GUARDANDO TRIBUTANTE, CODIGO: ".$this->db()->errno);
						$this->log_nueva_solicitud->warning("ERROR GUARDANDO TRIBUTANTE, ERROR MSG: ".$this->db()->error);
						return 0;
						}
					}
					else	$this->log_nueva_solicitud->warning("NO DATOS TRIBUTANTE");
				if($rol=='alumno') return $clave;
				else	return $this->getSol($id_alumno);
				}
				else
				{
					$this->log_nueva_solicitud->warning("ERROR GUARDANDO BAREMO, CODIGO: ".$this->db()->errno);
					$this->log_nueva_solicitud->warning("ERROR GUARDANDO BAREMO, ERROR MSG: ".$this->db()->error);
					return 0;
				}
		}
		else
		{ 
				$this->log_nueva_solicitud->warning("ERROR GUARDANDO ALUMNO, CODIGO: ".$this->db()->errno);
				$this->log_nueva_solicitud->warning("ERROR GUARDANDO ALUMNO, ERROR MSG: ".$this->db()->error);
				if($this->db()->errno==1062) return -2;
				else return 0;
		}
		}//FIN SAVEUSUARIO
		else
		{	
				$this->log_nueva_solicitud->warning("ERROR GUARDANDO USUARIO, CODIGO: ".$this->db()->errno);
				$this->log_nueva_solicitud->warning("ERROR GUARDANDO USUARIO, ERROR MSG: ".$this->db()->error);
				if($this->db()->errno==1062) return -1;
				else return 0;
		}
	return 0;
  }
//FIN FUNCION SAVE

  public function insertarAlumno($query)
	{
	$r=$this->db()->query($query);
	if($r) return $this->db()->insert_id;
	else return -1;
	} 
  public function getLast($tipo='alumno',$clave='') 
	{
	if($tipo=='alumno')
	$query="select id_alumno from alumnos order by id_alumno desc limit 1";
	else
	$query="select id_usuario from usuarios where clave=md5('".$clave."')";
	
	$soldata=$this->db()->query($query);
	if(!$soldata) return 0;
        if($row = $soldata->fetch_object()) {
           $solSet=$row;
       		if($tipo=='alumno') 	return $solSet->id_alumno;
       		else 	return $solSet->id_usuario;
        }
	else return 0;
	}
	
//comprobar si los datos corresponden a una solicitud existente
  public function compSol($d,$n,$a1) {
	$solSet=array();
	$query="select * from alumnos where dni_tutor1='".$d."' and nombre='".$n."' and apellido1='".$a1."'";
	$soldata=$this->db()->query($query);
        if($row = $soldata->fetch_object()) {
           $solSet=$row;
        }
        return $solSet;
    }
    public function getHermanos($tipo,$id) 
		{
   	$this->log_mostrar_solicitud->warning("OBtENIENDO DATOS DE ".$tipo); 
    $resultSet=array();
		if($tipo=='tributantes')
		{
		$squery="SELECT id_tributante,nombre,apellido1,apellido2,parentesco,dni FROM tributantes where id_alumno=".$id;
   	$this->log_mostrar_solicitud->warning("OBtENIENDO DATOS DE TRIBUTANTES: ".$squery); 
		$query=$this->db->query($squery);
		$i=1;
		$sufijo=$tipo;
		while ($row = $query->fetch_object()) 
		{
           $resultSet['tributantes_id_tributante'.$i]=$row->id_tributante;
           $resultSet['tributantes_nombre'.$i]=$row->nombre;
           $resultSet['tributantes_apellido1'.$i]=$row->apellido1;
           $resultSet['tributantes_apellido2'.$i]=$row->apellido2;
           $resultSet['tributantes_parentesco'.$i]=$row->parentesco;
           $resultSet['tributantes_dni'.$i]=$row->dni;
	   			 $i++;
    }
		$r1=array('nombre1'=>'','apellido11'=>'','apellido21'=>'','parentesco1'=>'', 'dni1'=>'');
		$r2=array('nombre2'=>'','apellido12'=>'','apellido22'=>'','parentesco2'=>'','dni2'=>'');
		$r3=array('nombre3'=>'','apellido13'=>'','apellido23'=>'','parentesco3'=>'','dni3'=>'');
		
			if($query->num_rows==0)		{	$resultSet=array_merge($r1,$r2,$r3);}
			if($query->num_rows==1)		{	$resultSet=array_merge($resultSet,$r2,$r3);}
			if($query->num_rows==2)		{	$resultSet=array_merge($resultSet,$r3);}
		}
		else
		{
		$squery="SELECT id_registro,datos,fnacimiento,curso,nivel_educativo FROM hermanos where tipo='".$tipo."' and id_alumno=".$id;
   	$this->log_mostrar_solicitud->warning("CONSULTA HERMANOS: ".$squery); 
		$query=$this->db->query($squery);
		$i=1;
		$sufijo=$tipo;
		while ($row = $query->fetch_object()) 
		{
           $resultSet['hermanos_id_registro_'.$sufijo.$i]=$row->id_registro;
           $resultSet['hermanos_datos_'.$sufijo.$i]=$row->datos;
           $resultSet['hermanos_fnacimiento_'.$sufijo.$i]=$row->fnacimiento;
           $resultSet['hermanos_curso_'.$sufijo.$i]=$row->curso;
           $resultSet['hermanos_nivel_educativo_'.$sufijo.$i]=$row->nivel_educativo;
	   			 $i++;
    }
		$r1=array('hermanos_id_registro_'.$sufijo.'1'=>'','hermanos_datos_'.$sufijo.'1'=>'','hermanos_fnacimiento_'.$sufijo.'1'=>'','hermanos_curso_'.$sufijo.'1'=>'','hermanos_nivel_educativo_'.$sufijo.'1'=>'');
		$r2=array('hermanos_id_registro_'.$sufijo.'2'=>'','hermanos_datos_'.$sufijo.'2'=>'','hermanos_fnacimiento_'.$sufijo.'2'=>'','hermanos_curso_'.$sufijo.'2'=>'','hermanos_nivel_educativo_'.$sufijo.'2'=>'');
		$r3=array('hermanos_id_registro_'.$sufijo.'3'=>'','hermanos_datos_'.$sufijo.'3'=>'','hermanos_fnacimiento_'.$sufijo.'3'=>'','hermanos_curso_'.$sufijo.'3'=>'','hermanos_nivel_educativo_'.$sufijo.'3'=>'');
    
		if($query->num_rows==0)		{	$resultSet=array_merge($r1,$r2,$r3);}
    if($query->num_rows==1)		{	$resultSet=array_merge($resultSet,$r2,$r3);}
    if($query->num_rows==2)		{	$resultSet=array_merge($resultSet,$r3);}
   	$this->log_mostrar_solicitud->warning("HERMANOS: "); 
   	$this->log_mostrar_solicitud->warning(print_r($resultSet,true)); 
		}   
    return $resultSet;
		}
  
	public function getSol_pruebas($id,$tiposol='nueva',$id_centro) 
	{
	$datos_baremo=array();
	$datos_alumno=array();
	$datos_tributantes=array();
	if($tiposol=='existente')
	{
	$query_baremo="select b.* from alumnos a left join baremo b on a.id_alumno=b.id_alumno where a.id_alumno=".$id;

	$soldata=$this->db()->query($query_baremo);
        if($row = $soldata->fetch_object()) {
           $solSet_baremo=$row;
        }
	$datos_baremo = json_decode(json_encode($solSet_baremo), True);
	$datos_baremo=array_combine(array_map(function($k){ return 'baremo_'.$k;}, array_keys($datos_baremo)),$datos_baremo);
	$this->log_mostrar_solicitud->warning("BAREMO");
	$this->log_mostrar_solicitud->warning(print_r($datos_baremo,true));

	}	

	$query_alumno="select * from alumnos a where a.id_alumno=".$id;
	$soldata=$this->db()->query($query_alumno);
        if($row = $soldata->fetch_object()) {
           $solSet_alumno=$row;
        }

	$datos_alumno = json_decode(json_encode($solSet_alumno), True);

	$hermanos_admision=$this->getHermanos('admision',$id);
	$hermanos_baremo=$this->getHermanos('baremo',$id);
	$tributantes=$this->getHermanos('tributantes',$id);

	if(sizeof($datos_alumno)!=0)	$sol_completa=$datos_alumno;
	if(sizeof($datos_baremo)!=0)	$sol_completa=array_merge($datos_alumno,$datos_baremo);
	if(sizeof($hermanos_admision)!=0)	$sol_completa=array_merge($sol_completa,$hermanos_admision);
	if(sizeof($hermanos_baremo)!=0)	$sol_completa=array_merge($sol_completa,$hermanos_baremo);
	if(sizeof($tributantes)!=0)	$sol_completa=array_merge($sol_completa,$tributantes);
        
	//vaciamos el array en caso de que sea una solicitud nueva
	if($tiposol=='nueva')
		{
		foreach($sol_completa as $k=>$sol)
			$sol_completa[$k]='';
		//Obtenemos nombre del centro a partir dle id
		$nombre_centro=$this->getNombre($id_centro);
		$sol_completa['nombre_centro_destino']=$nombre_centro;
		$this->log_nueva_solicitud->warning("NUEVA SOLICITUD, nombre centro: ".$nombre_centro);
		$this->log_nueva_solicitud->warning(print_r($sol_completa,true));
		}
	else
		{
		//obtenmos nombres de los centros para mostrar
		if($sol_completa['id_centro_estudios_origen']!='') $sol_completa['id_centro_estudios_origen']=$this->getCentroNombre($sol_completa['id_centro_estudios_origen']); 
		if($sol_completa['id_centro_destino']!='') $sol_completa['id_centro_destino']=$this->getCentroNombre($sol_completa['id_centro_destino']); 
		if($sol_completa['id_centro_destino1']!='') $sol_completa['id_centro_destino1']=$this->getCentroNombre($sol_completa['id_centro_destino1']); 
		if($sol_completa['id_centro_destino2']!='') $sol_completa['id_centro_destino2']=$this->getCentroNombre($sol_completa['id_centro_destino2']); 
		if($sol_completa['id_centro_destino3']!='') $sol_completa['id_centro_destino3']=$this->getCentroNombre($sol_completa['id_centro_destino3']); 
		if($sol_completa['id_centro_destino4']!='') $sol_completa['id_centro_destino4']=$this->getCentroNombre($sol_completa['id_centro_destino4']); 
		if($sol_completa['id_centro_destino5']!='') $sol_completa['id_centro_destino5']=$this->getCentroNombre($sol_completa['id_centro_destino5']); 
		if($sol_completa['id_centro_destino6']!='') $sol_completa['id_centro_destino6']=$this->getCentroNombre($sol_completa['id_centro_destino6']); 
		$this->log_mostrar_solicitud->warning("EDITAR SOLICITUD");
		$this->log_mostrar_solicitud->warning(print_r($sol_completa,true));
		}
	return $sol_completa;
  }
  
	public function getSolAdmitidas($nvebo=0,$nvtva=0,$c=0) 
	{
				$sqlbaseebo="SELECT a.id_alumno FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on c.id_centro=a.id_centro_destino where a.tipoestudios='ebo' and  fase_solicitud!='borrador' and estado_solicitud not in('irregular','duplicada') and a.id_centro_destino=$c order by c.id_centro, a.transporte desc,b.puntos_validados desc,a.nordensorteo asc,a.nasignado desc LIMIT $nvebo";
				$sqlbasetva="SELECT a.id_alumno FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on c.id_centro=a.id_centro_destino where a.tipoestudios='tva' and fase_solicitud!='borrador' and estado_solicitud not in('irregular','duplicada') and a.id_centro_destino=$c order by c.id_centro, a.transporte desc,b.puntos_validados desc,a.nordensorteo asc,a.nasignado desc LIMIT $nvtva";
				$qebo=$this->db->query($sqlbaseebo);
				$qtva=$this->db->query($sqlbasetva);
		$this->log_sorteo->warning(print_r($sqlbaseebo,true));
		$this->log_sorteo->warning(print_r($sqlbasetva,true));

        if($qebo and $qtva)
				{
				if($qebo->num_rows>0)
					while ($row = $qebo->fetch_object()) 
					{
						 $resultSet['ebo'][]=$row;
					}
				if($qtva->num_rows>0)
					while ($row = $qtva->fetch_object()) 
					{
						 $resultSet['tva'][]=$row;
					}
		$this->log_sorteo->warning("SOLICITUDES ADMITIDAS");
		$this->log_sorteo->warning(print_r($resultSet,true));
  			}     
	if(!isset($resultSet)) return 0; 
	else return $resultSet;
				
	}
	public function actualizaSolSorteo($c=1,$numero=0,$solicitudes=0,$nvebo=0,$nvtva=0) 
	{
		$resultSet=array();
		//ponemos todas llas solicitudes a noadmitidos por si ya ha habido otro sorteo
		$sql_excluida="update alumnos set est_desp_sorteo='noadmitida' where id_centro_destino=$c";
		
		$sql1="UPDATE alumnos a set nordensorteo=$solicitudes+nasignado-$numero+1 where nasignado<$numero and id_centro_destino='$c' and fase_solicitud!='borrador' ";
		$sql2="UPDATE alumnos a set nordensorteo=nasignado-$numero+1 where nasignado>=$numero and id_centro_destino='$c' and fase_solicitud!='borrador' ";

		$this->log_sorteo->warning("OBTENIENDO IDS SOLICITUDES");
		//obtenemos los ids de solicitudes admitidas según ls criterios del baremo
		$ids=$this->getSolAdmitidas($nvebo,$nvtva,$c);
		$this->log_sorteo->warning("RESPUESTA IDS:");
		$this->log_sorteo->warning(print_r($ids,true));
		if($ids==0) return 0;
		
		$idsebo='';
		if(isset($ids['ebo'])) 
			foreach($ids['ebo'] as $id)
				$idsebo.=$id->id_alumno.",";
		$idsebo=rtrim($idsebo,',');
	
		$idstva='';
		if(isset($ids['tva']))
			foreach($ids['tva'] as $id)
				$idstva.=$id->id_alumno.",";
			$idstva=rtrim($idstva,',');	
		$sql_actestebo='';
		$sql_actesttva='';
		//actualizamos campo de estado de la solicid despues del sorteo para marcar las sol admitidas, siempre excluyendo las borrador
		if(strlen($idsebo)>0)
			$sql_actestebo="update alumnos set est_desp_sorteo='admitida' where tipoestudios='ebo' and id_centro_destino=$c and fase_solicitud!='borrador' and id_alumno in(".$idsebo.")";
		if(strlen($idstva)>0)
			$sql_actesttva="update alumnos set est_desp_sorteo='admitida' where tipoestudios='tva' and id_centro_destino=$c and fase_solicitud!='borrador' and id_alumno in(".$idstva.")";
	
		$this->log_sorteo->warning("ACTUALIZANDO SORTEO");
		$this->log_sorteo->warning(print_r($sql1,true));
		$this->log_sorteo->warning(print_r($sql2,true));
		$this->log_sorteo->warning(print_r($sql_excluida,true));
		$this->log_sorteo->warning("ACTUALIZANDO SORTEO EBO");
		$this->log_sorteo->warning(print_r($sql_actestebo,true));
		$this->log_sorteo->warning(print_r($sql_actesttva,true));

		$query0=$this->db->query($sql_excluida);
		$query1=$this->db->query($sql1);
		$query2=$this->db->query($sql2);
		$this->log_sorteo->warning("ACT EST EBO");
		if(strlen($idsebo)>0)
			$query3=$this->db->query($sql_actestebo);
		else $query3=1;

		if(strlen($idstva)>0)
			$query4=$this->db->query($sql_actesttva);
		else $query4=1;

		if($query0 and $query1 and $query2 and $query3 and $query4)
			return 1;
		else 
		{
			$this->log_sorteo->warning("ERROR ACTUALIZANDO SOLICITUDES SORTEO");
			return 0;
		}
	}
	public function genSolDefinitivas($c=1,$nvebo=0,$nvtva=0) 
	{
				//obteneos la consulta q nos devuelve el listado ordenado de solicitudes
		$ids=$this->getSolAdmitidas($nvebo,$nvtva,$c);

		$idsebo='';
		foreach($ids['ebo'] as $id)
			$idsebo.=$id->id_alumno.",";
		$idsebo=rtrim($idsebo,',');	
		$idstva='';
		foreach($ids['tva'] as $id)
			$idstva.=$id->id_alumno.",";
		$idstva=rtrim($idstva,',');	
		if($c!=1)
		{
				//ponemos todas llas solicitudes a noadmitidas
				$sql_noadmitida="update alumnos set est_desp_sorteo='noadmitida' where id_centro_destino=$c";
				$sqlebo="UPDATE alumnos a set est_desp_sorteo='admitida' where fase_solicitud!='borrador' and tipoestudios='ebo' and id_centro_destino=$c and estado_solicitud='apta' and a.id_alumno in(".$idsebo.")";
				$sqltva="UPDATE alumnos a set est_desp_sorteo='admitida' where fase_solicitud!='borrador' and tipoestudios='tva' and id_centro_destino=$c and estado_solicitud='apta' and a.id_alumno in(".$idstva.")";

				$this->log_listados_definitivos->warning($sql_noadmitida);
				$this->log_listados_definitivos->warning($sqlebo);
				$this->log_listados_definitivos->warning($sqltva);
				$noad=$this->db->query($sql_noadmitida);
				$qebo=$this->db->query($sqlebo);
				$qtva=$this->db->query($sqltva);
        if($qebo and $qtva and $noad)
					return 1;
				else return 0;
		}

	}
	public function getAllSolSorteo($c=1,$tipo=0,$fase_sorteo=0,$subtipo_listado='',$provincia='todas') {
        $resultSet=array();
				$centro='id_centro_destino';
				//si no son para actualizar el sorteo, listado normal completo.Antes de asignar el numero de sorteo
				if($fase_sorteo==0)
				{
					if($c!=1)
						$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.tipoestudios,nasignado, b.puntos_validados FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno where $centro=".$c." order by a.tipoestudios, a.apellido1,a.nombre,a.transporte desc,b.puntos_validados desc,b.hermanos_centro desc,b.proximidad_domicilio,b.renta_inferior,b.discapacidad,b.tipo_familia,a.nordensorteo asc,a.nasignado desc";
					else
						$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.tipoestudios, nasignado, b.puntos_validados FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno where order by a.id_centro_destino,a.tipoestudios, a.apellido1,a.nombre,a.transporte desc,b.puntos_validados desc,b.hermanos_centro desc,b.proximidad_domicilio,b.renta_inferior,b.discapacidad,b.tipo_familia,a.nordensorteo asc,a.nasignado desc";
				}
				else //En cualquier otro caso quitamos las q están en fase de borrador
				{
					if($c!=1)
						$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado, b.puntos_validados,b.proximidad_domicilio,b.tutores_centro,b.renta_inferior,b.discapacidad,b.tipo_familia,b.hermanos_centro FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno where $centro=".$c." and fase_solicitud!='borrador' order by a.tipoestudios, a.apellido1,a.nombre, a.transporte desc,b.puntos_validados desc,a.nordensorteo asc,a.nasignado desc";
					else
						$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado, b.puntos_validados,b.proximidad_domicilio,b.tutores_centro,b.renta_inferior,b.discapacidad,b.tipo_familia,b.hermanos_centro,a.id_centro_destino as id_centro,c.nombre_centro as nombre_centro FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on c.id_centro=a.id_centro_destino where fase_solicitud!='borrador' order by c.id_centro,a.tipoestudios, a.apellido1,a.nombre,c.id_centro, a.transporte desc,b.puntos_validados desc,a.nordensorteo asc,a.nasignado desc";
				}

				$this->log_listado_solicitudes->warning("CONSULTA SOLICITUDES SORTEO");
				$this->log_listado_solicitudes->warning($sql);
				$query=$this->db->query($sql);
        if($query)
				while ($row = $query->fetch_object()) 
				{
           $resultSet[]=$row;
        }
        return $resultSet;
	}
	public function getAllSolListados($c=1,$tipo=0,$subtipo_listado='',$fase_sorteo=0,$estado_convocatoria=0) 
	{
		if($estado_convocatoria<3) $tabla_alumnos='alumnos';// 3 . Inicio peridoo provisionales-definitivos
		else $tabla_alumnos='alumnos_provisional';
		$this->log_listados_provisionales->warning("CONSULTA SOLICITUDES PROVISIONALES TABLA:".$tabla_alumnos);
		
        	$resultSet=array();
		if($tipo==0) //todas las solicitudes, incluyendo las q están en borrador
		{
			if($c<=1)
			{
				if($subtipo_listado=='dup') //solicitudes duplicadas
					$sql="select a.apellido1,a.apellido2,a.tipoestudios,a.fnac,a.dni_tutor1,a.nombre,a.id_alumno,c.nombre_centro from alumnos a join (select apellido1,nombre from alumnos group by apellido1,nombre having count(*)>1) dup on a.apellido1=dup.apellido1 and dup.nombre=a.nombre join centros c on c.id_centro=a.id_centro_destino join baremo b on b.id_alumno=a.id_alumno order by c.id_centro,a.tipoestudios b.puntos_validados desc";
				else  //solicitudes normales
					$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.loc_dfamiliar,a.nordensorteo,a.nasignado as nasignado,b.*,c.nombre_centro,c.provincia,c2.nombre_centro as nombre_centro_origen FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro left join centros c2 on c2.id_centro=a.id_centro_estudios_origen  order by c.id_centro desc,a.tipoestudios asc, b.puntos_validados desc";
			}
			else
				$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado,b.*,c.nombre_centro,c.provincia,c2.nombre_centro as nombre_centro_origen FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro left join centros c2 on c2.id_centro=a.id_centro_estudios_origen  where c.id_centro=$c order by c.id_centro,a.tipoestudios asc, b.puntos_validados desc";
				
				$this->log_gencsvs->warning("CONSULTA SOLICITUDES CSV");
				$this->log_gencsvs->warning($sql);
		}
				elseif($tipo==1) //provisionales
				{
					if($subtipo_listado=='admitidos_prov')
						if($c<=1)
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado,c.nombre_centro,b.puntos_validados FROM $tabla_alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta' and est_desp_sorteo='admitida' order by c.id_centro desc,a.tipoestudios desc,a.transporte desc,b.puntos_validados desc";
						else
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado,c.nombre_centro,b.puntos_validados FROM $tabla_alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta'  and est_desp_sorteo='admitida' and id_centro=$c order by a.tipoestudios asc,a.transporte desc, b.puntos_validados desc";
					elseif($subtipo_listado=='noadmitidos_prov')
						if($c<=1)
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado,b.puntos_validados FROM $tabla_alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta'  and est_desp_sorteo='noadmitida' order by c.id_centro, a.tipoestudios asc,a.transporte desc, b.puntos_validados desc";
						else
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado, b.puntos_validados FROM $tabla_alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta'  and est_desp_sorteo='noadmitida' and id_centro=$c order by a.tipoestudios asc,a.transporte desc, b.puntos_validados desc";
					elseif($subtipo_listado=='excluidos_prov')
						if($c<=1)
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado, b.puntos_validados FROM $tabla_alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and( estado_solicitud='duplicada' or estado_solicitud='irregular') order by c.id_centro desc, a.tipoestudios asc,a.transporte desc, b.puntos_validados desc";
						else
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado, b.puntos_validados FROM $tabla_alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador'  and( estado_solicitud='duplicada' or estado_solicitud='irregular') and id_centro=$c order by a.tipoestudios asc,a.transporte desc, b.puntos_validados desc";
				$this->log_listados_provisionales->warning("CONSULTA SOLICITUDES PROVISIONALES ADMITIDOS SUBTIPO: ".$subtipo_listado);
				$this->log_listados_provisionales->warning($sql);
				}
				elseif($tipo==2) //definitivos
				{
					if($subtipo_listado=='admitidos_def')
						if($c<=1)
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta'  and est_desp_sorteo='admitida' order by c.id_centro desc, a.tipoestudios desc,a.transporte desc, b.puntos_validados desc";
						else
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta'  and est_desp_sorteo='admitida' and id_centro=$c order by  a.tipoestudios desc,a.transporte desc,b.puntos_validados desc";
					elseif($subtipo_listado=='noadmitidos_def')
						if($c<=1)
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta'  and est_desp_sorteo='noadmitida' order by c.id_centro desc, a.tipoestudios desc,a.transporte desc, b.puntos_validados desc";
						else
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and estado_solicitud='apta'  and est_desp_sorteo='noadmitida' and id_centro=$c order by a.tipoestudios desc,a.transporte desc, b.puntos_validados desc";
					elseif($subtipo_listado=='excluidos_def')
						if($c<=1)
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador' and( estado_solicitud='duplicada' or estado_solicitud='irregular') order by c.id_centro desc, a.tipoestudios desc,a.transporte desc, b.puntos_validados desc";
						else
							$sql="SELECT a.id_alumno,a.nombre,a.apellido1,a.apellido2,a.tipoestudios,a.fase_solicitud,a.estado_solicitud,a.transporte,a.nordensorteo,a.nasignado as nasignado FROM alumnos a left join baremo b on b.id_alumno=a.id_alumno left join centros c on a.id_centro_destino=c.id_centro where fase_solicitud!='borrador'  and( estado_solicitud='duplicada' or estado_solicitud='irregular') and id_centro=$c order by a.tipoestudios desc,a.transporte desc, b.puntos_validados desc";
				$this->log_listados_definitivos->warning("CONSULTA SOLICITUDES DEFINITIVOS SUBTIPO: ".$subtipo_listado);
				$this->log_listados_definitivos->warning($sql);
				}

				$query=$this->db->query($sql);
        if($query)
				while ($row = $query->fetch_object()) 
				{
           $resultSet[]=$row;
        }
        return $resultSet;
	}

	public function getSol($id) {
				$sol_completa=array();
				$query="SELECT a.*,IFNULL(b.puntos_validados,0) as puntos_validados FROM alumnos a, baremo b  where a.id_alumno=b.id_alumno and b.id_alumno=$id";
				$this->log_nueva_solicitud->warning("CONSULTA SOLICITUD CREADA: ".$query);
				$soldata=$this->db()->query($query);
        if($row = $soldata->fetch_object()) 
				{
           $solSet=$row;
        }
		//convertimos objeto en array
				foreach($solSet as $k=>$vsol)
					$sol_completa[$k]=$vsol;
        
		return $sol_completa;
    }
    
	public function getTipoCentro($idcentro) 
	{
		//averiguamos si es de ed especial
		$query="select nombre_centro from centros c,centros_grupos cg where c.id_centro=cg.id_centro and c.id_centro='".$idcentro."' limit 1";
		$this->log_actualizar_solicitud->warning("AVERIGUANDO TIPO DE CENTRO");
		$this->log_actualizar_solicitud->warning($query);
		$soldata=$this->db()->query($query);
    if($soldata->num_rows==0) return 0;
		else return 1;
	}

	public function getCentroNombre($idcentro) 
	{
		//averiguamos si es de ed especial
		if($this->getTipoCentro($idcentro)==1)
			$query="select concat(nombre_centro,'*') as nombre_centro from centros c,centros_grupos cg where c.id_centro=cg.id_centro and c.id_centro='".$idcentro."' limit 1";
		else
			$query="select nombre_centro from centros c,centros_grupos cg where c.id_centro=cg.id_centro and c.id_centro='".$idcentro."'";

		$this->log_actualizar_solicitud->warning("devolviendo nombre de centro");
		$this->log_actualizar_solicitud->warning($query);
		$soldata=$this->db()->query($query);
    if($soldata->num_rows==0);
	  if($row = $soldata->fetch_object()) 
		{
   	 $solSet=$row;
		return $solSet->nombre_centro;
    }
		else return 0;
    }
	public function getCentroId($nombrecentro) {
		$query="select id_centro from centros  where nombre_centro='".$nombrecentro."'";
		$this->log_actualizar_solicitud->warning($query);
		$soldata=$this->db()->query($query);
    if($soldata->num_rows==0) return 0;
	  if($row = $soldata->fetch_object()) 
		{
   	 $solSet=$row;
		return $solSet->id_centro;
    }
		else return 0;
    }
		public function getId() {
        return $this->id_alumno;
    }
 
    public function setId($id) {
        $this->id_alumno = $id;
    }
     
    public function getNombre($idc) {
			$query="select nombre_centro from centros  where id_centro='".$idc."'";
			$soldata=$this->db()->query($query);
			if($soldata->num_rows==0) return 0;
			if($row = $soldata->fetch_object()) 
			{
			 $solSet=$row;
			return $solSet->nombre_centro;
			}
			else return 0;
    }
 
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
 
    public function getApellido1() {
        return $this->apellido1;
    }
    public function getApellido2() {
        return $this->apellido2;
    }
 
    public function setApellido1($apellido) {
        $this->apellido1 = $apellido;
    }
    public function setApellido2($apellido) {
        $this->apellido2 = $apellido;
    }
 
    public function getDni() {
        return $this->dni;
    }
 
    public function setDni($d) {
        $this->dni = $d;
    }
 
 
    public function getNacionalidad() {
        return $this->nacionalidad;
    }
 
    public function setNacionalidad($n) {
        $this->nacionalidad = $n;
    }
    public function getFnac() {
        return $this->fnac;
    }
 
    public function setFnac($n) {
        $this->fnac = $n;
    }
 
 
}
?>
