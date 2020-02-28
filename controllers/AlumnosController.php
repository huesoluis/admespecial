<?php
class AlumnosController extends ControladorBase{
    public $conectar;
    public $adapter;
     
    public function __construct() {
        parent::__construct();
          
        $this->conectar=new Conectar();
        $this->adapter=$this->conectar->conexion();
    }
    
    
    public function listadoMatriculados(){
	//Creamos el objeto alumno
        $alumno=new Alumno($this->adapter);
         
        //Conseguimos todos los alumnos
        $allalumnos=$alumno->getAll();
 
        //Cargamos la vista index y le pasamos valores
        $this->view("index",array(
            "allalumnos"=>$allalumnos,
            "resumencentros"=>$resumencentros
        ));
	
	}
 
    public function formNuevoAlumno(){
	//Creamos el objeto usuario
        $alumno=new Alumno($this->adapter);
         
        //Cargamos la vista index y le pasamos valores
        $this->view("index",array(
            "falumnos"=>"1"
        ));
    }
    public function index(){
        //Creamos el objeto centro
        $centro=new Centro($this->adapter);
        
        //obtenemos resumen
	$resumencentros=$centro->getResumen();
        
	//Creamos el objeto alumno
        $alumno=new Alumno($this->adapter);
         
        //Conseguimos todos los alumnos
        $allalumnos=$alumno->getAll();
 
        //Cargamos la vista index y le pasamos valores
        $this->view("index",array(
            "allalumnos"=>$allalumnos,
            "resumencentros"=>$resumencentros
        ));
    }
     
    public function crear(){
        if(isset($_POST["nombre"])){
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
