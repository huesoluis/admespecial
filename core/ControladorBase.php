<?php
class ControladorBase{

    public function __construct() {
	require_once DIR_BASE.'core/Conectar.php';
        require_once DIR_BASE.'core/EntidadBase.php';
        require_once DIR_BASE.'core/ModeloBase.php';
        
        //Incluir todos los modelos
        foreach(glob(DIR_BASE."models/*.php") as $file){
            require_once $file;
        }
    }
    
    //Plugins y funcionalidades
    
    public function view($vista,$datos){
        foreach ($datos as $id_assoc => $valor) {
            ${$id_assoc}=$valor; 
        }
        
        require_once 'core/AyudaVistas.php';
        $helper=new AyudaVistas();
    
        require_once 'views/'.$vista.'View.php';
    }
    
    public function redirect($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO){
        header("Location:index.php?controller=".$controlador."&action=".$accion);
    }
    
    //Métodos para los controladores

}
?>
