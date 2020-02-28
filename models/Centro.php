<?php
class Centro extends EntidadBase{
    private $id_centro;
    private $id_usuario;
    private $localidad;
    private $provincia;
    private $nombre_centro;
    
    public function __construct($adapter,$id_centro='',$ajax='no',$estadocentro=0) 
    //public function __construct($adapter,$id_centro='',$ajax='no') 
		{
			$table="centros";
			$this->id_centro=$id_centro;
			$this->estadocentro=$estadocentro;
			$this->conexion=$adapter;
			if($ajax=='no') parent::__construct($adapter, $table);
			require_once DIR_CLASES.'LOGGER.php';
			require_once DIR_APP.'parametros.php';
			$this->log_sorteo=new logWriter('log_sorteo',DIR_LOGS);
			$this->log_matricula=new logWriter('log_matricula',DIR_LOGS);
    }
   //devolvemos las vacantes en cada tpo de estudios 
    public function getNumSolicitudes($c=1)
		{
			$sql="SELECT count(*) as nsolicitudes FROM alumnos where fase_solicitud!='borrador' and id_centro_destino=$c";
			$this->log_sorteo->warning("OBTENIENDO NUMERO DE SOLICITUDES");
			$this->log_sorteo->warning($sql);
			$query=$this->conexion->query($sql);
			if($query) {$row = $query->fetch_object();return $row->nsolicitudes;}
			else return 0;
		}
    public function getNumeroSorteo()
	{
			$sql="select num_sorteo from centros where id_centro=$this->c";
			$query=$this->conexion->query($sql);
			if($query)
			return $query->fetch_object()->num_sorteo;
			else return 0;
	}
    public function getVacantes($rol='centro',$tipo='')
		{
			$sql="select ifnull(IF(t3.plazas-t2.np<0,0,t3.plazas-t2.np),t3.plazas) as vacantes from          (select tipo_alumno ta,num_grupos as ng,plazas from centros_grupos ce where ce.id_centro=".$this->id_centro." ) as t3          left join          (select  tipo_alumno_actual as tf, ifnull(count(*),0) as np from matricula where id_centro=".$this->id_centro." and estado='continua' group by tipo_alumno_actual ) as t2  on t3.ta=t2.tf;
";
			$query=$this->conexion->query($sql);
			$this->log_matricula->warning('sql vacantes: '.$sql);
			if($query)
    	{
				while ($row = $query->fetch_object()) 
				{
					$resultSet[]=$row;
				}
			}
      return $resultSet;
		} 
    public function getDatosMatriculaCentro($rol,$t,$centro)
		{
			$this->id_centro=$centro;
			return $this->getResumen($rol,$t);
		}
    public function getResumen($rol,$t) 
		{
			$resultSet=array();
			if($rol=='admin') 
			{
				if($t=='matricula')
					{
					$sql="
					select t1.ta,t3.ng as grupo,t3.plazas as puestos,IFNULL(t2.np,0) as plazasactuales,IFNULL(IF(t3.plazas-t2.np<0,0,t3.plazas-t2.np),0) as vacantes 
					from
					(select tipo_alumno ta,sum(num_grupos) as ng,sum(plazas) as plazas from centros_grupos ce group by ta) as t3
					join                   
					(select  tipo_alumno_actual as tf, count(*) as np from matricula where estado='continua' group by tipo_alumno_actual ) as t2  
					on t3.ta=t2.tf    
  					join                   
					(select  tipo_alumno_actual as ta, count(*) as np from matricula m group by tipo_alumno_actual  ) as t1 on t1.ta=t3.ta;
						";
					}
					elseif($t=='alumnos')
					{
					return "tabla:".$t;
					$sql="select t1.ta,'0' as grupo,'0' as puestos,t1.np as plazasactuales,t1.np-t2.np as vacantes from
					(select  tipo_alumno_actual as ta, count(*) as np from matricula group by tipo_alumno_actual ) as t1
					join
					(select  tipo_alumno_actual as tf, count(*) as np from matricula where estado='continua'  group by tipo_alumno_actual ) as t2
					on t1.ta=t2.tf";
					}
			}
			elseif($rol=='centro') 
			{
				if($t=='matricula')
					{
						$sql="
						select t1.ta,t3.ng as grupo,t3.plazas as puestos,IFNULL(t2.np,0) as plazasactuales,
						IFNULL(IF(ifnull(t3.plazas,0)-ifnull(t2.np,0)<0,0,ifnull(t3.plazas,0)-ifnull(t2.np,0)),0
						) 
						as vacantes from 
						(select tipo_alumno ta,num_grupos as ng,plazas from centros_grupos ce where ce.id_centro=".$this->id_centro.") as t3 
						left join 
						(select  tipo_alumno_actual as tf, count(*) as np from matricula where id_centro=".$this->id_centro." and estado='continua' group by tipo_alumno_actual ) as t2  
						on t3.ta=t2.tf 
						left join 
						(select  tipo_alumno_actual as ta, count(*) as np from matricula m,centros ce where m.id_centro=ce.id_centro and ce.id_centro=".$this->id_centro." group by tipo_alumno_actual  ) as t1 
						on t1.ta=t3.ta
						";
					}
					elseif($t=='alumnos')	
					{
					$sql="
					select t1.nc centro,t1.nb borrador,t2.np validada, t3.nd baremada
					from (select nombre_centro nc,count(*) as nb from centros c, alumnos a where a.id_centro_destino=c.id_centro and id_centro=".$this->id_centro." and fase_solicitud='borrador') t1 
					join
					(select nombre_centro nc,count(*) as np from centros c, alumnos a where a.id_centro_destino=c.id_centro and id_centro=".$this->id_centro." and fase_solicitud='validada') t2 on t1.nc=t2.nc
					join
					(select nombre_centro nc,count(*) as nd from centros c, alumnos a where a.id_centro_destino=c.id_centro and id_centro=".$this->id_centro." and fase_solicitud='baremada') t3 on t2.nc=t3.nc";
					}
			}
			$this->log_matricula->warning("CONSULTA DATOS RESUMEN MATRICULA: ".$sql);
			$query=$this->conexion->query($sql);
			if($query)
    	{
				while ($row = $query->fetch_object()) 
				{
					$resultSet[]=$row;
				}
			}
      return $resultSet;
    }

    public function setSorteo($ns=0,$c=1) 
		{
			$sql="update centros set num_sorteo=$ns where id_centro=$c";
			$this->log_sorteo->warning("ACTUALIZANDO VALORES CENTROS");
			$this->log_sorteo->warning($sql);
			$query=$this->conexion->query($sql);
			if($query)
				return 1;
			else{ 
						$this->log_sorteo->warning("ERROR");
						$this->log_sorteo->warning($this->conexion->error);
						return 0;
					}
		}
    public function getFases($c=1) 
		{
		$resultado=array(0,0,0);
		$sql="select nombre_centro nc,count(*) as nb,fase_solicitud from centros c, alumnos a where a.id_centro_destino=c.id_centro and id_centro=$this->id_centro  group by nombre_centro,fase_solicitud";
		$this->log_matricula->warning("CONSULTA DATOS ESTADO SOLICITUDES: ".$sql,DIR_LOGS);
			$query=$this->conexion->query($sql);
			if($query)
    	{
				while ($row = $query->fetch_object()) 
				{
					$this->log_matricula->warning(print_r($row,true));
					if($row->fase_solicitud=='borrador')	$resultado[0]=$row->nb;
					if($row->fase_solicitud=='validada')	$resultado[1]=$row->nb;
					if($row->fase_solicitud=='baremada')	$resultado[2]=$row->nb;
				}
			}
			else 
				$this->log_matricula->warning($this->conexion->error);
		$this->log_matricula->warning(print_r($resultado,true));
			
			return  $resultado;
    }
    public function getDatosSorteo($c=1,$tipo='') 
		{
			$sql="select vacantes_ebo,vacantes_tva,num_sorteo_ebo,num_sorteo_tva,solicitudes_ebo,solicitudes_tva from centros where id_centro=$c";
			$query=$this->conexion->query($sql);
			if($query)
    	{
				while ($row = $query->fetch_object()) 
				{
					$resultSet[]=$row;
				}
			}
			
			return  $resultSet;
    }
    public function setFaseSorteo($f) 
    {
			$sql="update centros set fase_sorteo='$f' where id_centro='$this->id_centro'";
			$this->log_sorteo->warning("ACTUALIZANDO FASE SORTEO");
			$this->log_sorteo->warning($sql);
			$query=$this->conexion->query($sql);
			if($query)
			return  1;
			else return 0;
    }
    public function getFaseSorteo() {
			$query=$this->conexion->query($sql);
			$sql="select fase_sorteo rom centros where id_centro=$this->id_centro";
			$this->log_sorteo->warning("OBTENIENDO FASE SORTEO");
			$this->log_sorteo->warning($sql);
			$query=$this->conexion->query($sql);
			if($query)
    			{
			return  $query->fetch_object()->fase_sorteo;
			}
			else return 0;
			

    }
    public function setEstado($e) {
    }
    public function getEstado() {
	$ec = $this->conexion->query("SELECT num_sorteo FROM centros WHERE id_centro =".$this->id_centro)->fetch_object()->num_sorteo; 
        return $ec;
    }

    public function getDb() {
        return $this->db;
    }
    public function getId() {
        return $this->id_centro;
    }

    public function setId($id) {
        $this->id_centro = $id;
    }
    
    public function getNombre() {
        return $this->nombre_centro;
    }

    public function setNombre() {
				//si el centros es -1,-2 o -3 es un servicio provincial asi iq no tiene nombre
				if($this->id_centro<0) $this->nombre_centro='sp';
				else
				{
				$nombre_centro = $this->conexion->query("SELECT nombre_centro FROM centros WHERE id_centro =".$this->id_centro)->fetch_object()->nombre_centro; 
      		$this->nombre_centro = $nombre_centro;
				}
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

}
?>
