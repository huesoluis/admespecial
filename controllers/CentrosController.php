<?php
class CentrosController extends ControladorBase{
    public $conectar;
    public $adapter;
	
    public function __construct($conectar=1) 
		{
    	parent::__construct();
		  if($conectar==1)
			{
			$this->conectar=new Conectar();
      $this->adapter=$this->conectar->conexion();
			}
			require_once DIR_CLASES.'LOGGER.php';
			require_once DIR_APP.'parametros.php';
    			require_once DIR_BASE.'/controllers/ListadosController.php';
			$this->log_listadoscentros=new logWriter('log_listadoscentros',DIR_LOGS);
    }
    
    public function index(){
        
        //Creamos el objeto centro
        $centro=new Centro($this->adapter);
        
        //Conseguimos todos los usuarios
        $allcentros=$centro->getAll();

        //Cargamos la vista index y le pasamos valores
        $this->view("index",array(
        ));
    }
    
    public function showTimeline($rol='centro',$id_centro='22000251',$datos='matricula')
    {
			$tline=' <div class="row progreso">
				<div class="col">INICIO</div>
				<div class="col">PROVISIONALES</div>
				<div class="col">DEFINITIVAS</div>
				</div><div class="row" style="background-color: #1b1b33;"> <hr width="60%" align="left"></div>';
			return $tline;
		}

		public function getAllCentros($provincia='aragon')
		{
			if($provincia=='todas')	$sql="SELECT id_centro FROM centros where id_centro>1 and id_centro in(select id_centro from matricula)";
			else	$sql="SELECT id_centro FROM centros where id_centro>1 and id_centro in(select id_centro from matricula) and provincia='$provincia'";
			$this->log_listadoscentros->warning("CONSULTA CENTROS");
			$this->log_listadoscentros->warning($sql);
			$query=$this->adapter->query($sql);
			if($query) return $query;
			else return 0;
		}

		public function getCentrosData($datos='matricula')
		{
     		 $centro=new Centro($this->adapter,'','no');
			$lcentros=array();
			$cabecera='si';
			if($rol=='admin') 
			{
				$cabecera='no';
				$centros=$this->getAllCentros('aragon');
			}
			$this->log_listadoscentros->warning("OBTENIENDO CENTROS");
			$this->log_listadoscentros->warning(print_r($centros,true));
			while ($row = $centros->fetch_object()) 
			{
				$centro->setId($row->id_centro);
				$this->log_listadoscentros->warning(print_r($row,true));
				$lcentros[]=$centro->getResumen('centro','matricula');
			}
			$this->log_listadoscentros->warning("OBTENIENDO CENTROS PARA CSV");
			$this->log_listadoscentros->warning(print_r($lcentros,true));
		return $lcentros;
    }
		public function showTablas($rol='centro',$id_centro='',$datos='matricula',$provincia='aragon')
		{
			$lcentros='';
			$cabecera='si';
			if($rol=='admin' || $provincia!='aragon') 
			{
				$cabecera='no';
				$centros=$this->getAllCentros($provincia);
			}
			$this->log_listadoscentros->warning("OBTENIENDO CENTROS PARA TABLAS");
			$this->log_listadoscentros->warning(print_r($centros,true));
			while ($row = $centros->fetch_object()) 
			{
				$this->log_listadoscentros->warning(print_r($row,true));
				$lcentros.=$this->showTabla('centro',$row->id_centro,'matricula',$cabecera);
			}
		return $lcentros;
    }

	public function showTabla($rol='centro',$id_centro='',$datos='matricula',$cabecera='si')
	{
			$list=new ListadosController($datos,0);
      //Creamos el objeto centro
      $centro=new Centro($this->adapter,$id_centro);
      $centro->setNombre();
      //obtenemos resumen
			$tablamatricula=$centro->getResumen($rol,$datos);
			$this->log_listadoscentros->warning("CABECERA EN SHOWTABLAS: ".$cabecera);
			
			return($list->showTablaResumenMatricula($tablamatricula,$datos,$centro->getNombre(),$rol,$cabecera,$id_centro));
    }

    public function crear(){
        if(isset($_POST["nombre"])){
            
            //Creamos un usuario
            $usuario=new Usuario();
            $usuario->setNombre($_POST["nombre"]);
            $usuario->setApellido($_POST["apellido"]);
            $usuario->setEmail($_POST["email"]);
            $usuario->setPassword(sha1($_POST["password"]));
            $save=$usuario->save();
        }
        $this->redirect("Usuarios", "index");
    }
    
    public function borrar(){
        if(isset($_GET["id"])){ 
            $id=(int)$_GET["id"];
            
            $usuario=new Usuario();
            $usuario->deleteById($id); 
        }
        $this->redirect();
    }
    
    
    public function hola(){
        $usuarios=new UsuariosModel($this->adapter);
        $usu=$usuarios->getUnUsuario();
        var_dump($usu);
    }

}
?>
