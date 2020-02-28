<?php 
class CSVS {
   public $sql_base; 
   public $acceso; 
   public $dato;
   public $names;
  public function __construct($filtro='',$sql='',$acceso='',$dato='',$names=''){
    	$this->filtro = $filtro;
    	$this->sql_base = $sql;
    	$this->acceso = $acceso;
    	$this->dato = $dato;
    	$this->names = $names;
	}

  public function gen_csv($fam='',$sql=''){
	$res=$this->acceso->query_csv($this->acceso->c,$sql,$fam,$this->names);
	 return ;
  }
   
   
}
 
?>
