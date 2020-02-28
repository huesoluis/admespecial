<?php
class Matricula extends EntidadBase{
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
			parent::__construct($adapter,"matricula");
			require_once DIR_CLASES.'LOGGER.php';
			require_once DIR_APP.'parametros.php';
			require_once DIR_BASE.'scripts/informes/pdf/fpdf/classpdf.php';
			$this->log_gencsvs_mat=new logWriter('log_gencsvs_mat',DIR_LOGS);
    }
	public function getAllMatListados($c=1,$tipo=0) 
	{
	        $resultSet=array();
		if($c<=1)
			$sql="SELECT * FROM matricula order by id_centro desc";
		else
			$sql="SELECT * FROM matricula where id_centro=$c";
		
		$this->log_gencsvs_mat->warning("CONSULTA MATRICULAS CSV");
		$this->log_gencsvs_mat->warning($sql);
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
				$query="SELECT a.*,b.puntos_totales FROM alumnos a, baremo b  where a.id_alumno=b.id_alumno and b.id_alumno=$id";
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
    
	public function getCentroNombre($idcentro) {
		$query="select nombre_centro from centros  where id_centro='".$idcentro."'";
		#$this->log_actualizar_solicitud->warning($query);
		$soldata=$this->db()->query($query);
    if($soldata->num_rows==0) return 0;
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
