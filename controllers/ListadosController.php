<?php
class ListadosController extends ControladorBase{
    public $conectar;
    public $adapter;
    public $allalumnos; 
    public $tabla; 
    public function __construct($tabla="matricula",$conexion=1) 
		{
        parent::__construct();
        $this->tabla=$tabla;
				require_once DIR_CLASES.'LOGGER.php';
				require_once DIR_APP.'parametros.php';
				if($conexion==1)
				{
        $this->conectar=new Conectar();
        $this->adapter=$this->conectar->conexion();
				}
				$this->log_sorteo=new logWriter('log_sorteo',DIR_LOGS);
				$this->log_listado_general=new logWriter('log_listados_generales',DIR_LOGS);
				$this->log_listados_matricula=new logWriter('log_listados_matricula',DIR_LOGS);
				$this->log_listados_provisionales=new logWriter('log_listados_provisionales',DIR_LOGS);
				$this->log_listados_definitivos=new logWriter('log_listados_definitivos',DIR_LOGS);
				$this->log_listados_solicitudes=new logWriter('log_listado_solicitudes',DIR_LOGS);
				$this->log_gencsvs=new logWriter('log_gencsvs',DIR_LOGS);
    }
    
    public function getConexion()
		{
				return $this->adapter;
		}
  	public function getAlumnosCentro($centro)
		{
		//Creamos el objeto alumno
			$alumno=new Alumno($this->adapter,$this->tabla);
			//Conseguimos todos los alumnos
			$allalumnos=$alumno->getAll($centro);
		return $allalumnos;
		}
  public function getDefinitivos($id_centro)
		{
		//Creamos el objeto alumno
    	$alumno=new Alumno($this->adapter,'alumnos');
		  //Conseguimos todos los datos de baremacion de los alumnos
    	$allbaremos=$alumno->getBaremos($id_centro);
 
	return $allbaremos;
	}
  public function asignarNumSol($id_centro)
	{
		$this->log_sorteo->warning("ASIGNANDO NUMERO SORTEO");
		$sql="SET @r := 0";
		$this->adapter->query($sql);
		//ponemos todas a cero para evitar inconsistencias
		$sql1="UPDATE  alumnos SET nasignado =0 WHERE id_centro_destino=$id_centro";
		$sql2="UPDATE  alumnos SET nasignado = (@r := @r + 1) where id_centro_destino=$id_centro and fase_solicitud!='borrador' ORDER BY  RAND()";
		$this->log_sorteo->warning($sql1);
		$this->log_sorteo->warning($sql2);
		if($this->adapter->query($sql1) and $this->adapter->query($sql2))
		{
			$this->log_sorteo->warning("OK ASIGNANDO NUM SORTEO");
			return 1;
		}
		else{ 
			$this->log_sorteo->warning("ERROR ASIGNANDO NUM SORTEO: ");
			$this->log_sorteo->warning($sql1);
			$this->log_sorteo->warning($sql2);
			$this->log_sorteo->warning($this->adapter->error);
			return 0;
		}
	}
  public function actualizaSolicitudesSorteo($id_centro,$numero,$solicitudes,$nvebo=0,$nvtva=0)
	{
		//Creamos el objeto solicitud
    $solicitud=new Solicitud($this->adapter);
    $res=$solicitud->actualizaSolSorteo($id_centro,$numero,$solicitudes,$nvebo,$nvtva);
	return $res;
	}
  public function getMatriculas($id_centro=1,$tiposol=0,$fase_sorteo=0,$modo='normal')
	{
		$this->log_gencsvs->warning('ENTRANDO EN GET Matricula, MODO: '.$modo);
		$matricula=new Matricula($this->adapter);
		if($modo=='normal')
    		{	
		  //Conseguimos todas las matriculas. funcion por definir
    	//$allmatricula=$matricula->getAllMat($id_centro,$tiposol,$fase_sorteo);
 		}
		elseif($modo=='csv')
		{
			$this->log_gencsvs->warning('OBTENIENDO MATRICULA PARA CSV');
    			$allmatriculas=$matricula->getAllMatListados($id_centro,$tiposol);
		}
	return $allmatriculas;
	}
  public function getSolicitudes($id_centro=1,$tiposol=0,$fase_sorteo=0,$modo='normal',$subtipo_listado='',$provincia='todas',$estado_convocatoria=0)
	{
		$this->log_gencsvs->warning('ENTRANDO EN GETSOLICITUDEs, MODO: '.$modo);
		$solicitud=new Solicitud($this->adapter);
		if($modo=='normal')// listados previos al sorteo
    		{	
			  //Conseguimos todas las solcitudes del centro
	    		$allsolicitudes=$solicitud->getAllSolSorteo($id_centro,$tiposol,$fase_sorteo,$subtipo_listado,$provincia);
 		}
		elseif($modo=='csv')
		{
			$this->log_gencsvs->warning('OBTENIENDO SOL PARA CSV');
		    	$allsolicitudes=$solicitud->getAllSolListados($id_centro,$tiposol,$subtipo_listado,$estado_convocatoria);
		}
		elseif($modo=='provisionales')
		{
			$this->log_listados_provisionales->warning('OBTENIENDO PROVISIONALES GETALLSOLLISTADOS');
    			$allsolicitudes=$solicitud->getAllSolListados($id_centro,1,$subtipo_listado,$fase_sorteo,$estado_convocatoria);
		}
		elseif($modo=='definitivos')
		{
			$this->log_listados_definitivos->warning('OBTENIENDO DEFINITIVOS');
		    	$allsolicitudes=$solicitud->getAllSolListados($id_centro,2,$subtipo_listado,$estado_convocatoria);
		}
	return $allsolicitudes;
	}
    public function getAlumnos(){
	//Creamos el objeto alumno
        $alumno=new Alumno($this->adapter,$this->tabla);
         
        //Conseguimos todos los alumnos
        $allalumnos=$alumno->getAll();
 
        //Cargamos la vista index y le pasamos valores
	return $allalumnos;
	}

  public function showFormulariosolicitud(){
		require_once DIR_BASE.'/includes/form_solicitud.php';
		return $formsol;
	}
  public function showDefinitivos($a,$rol='centro'){
	//codigo para mostrar alumnos según tipo de inscripcion
		$li='';
		$html='<section class="col-lg-12 usuario" style="height:400px;">';
		$cabecera="<div class='filasol' id='cab_solicitudes'>";
		if($rol=='admin') $cabecera.="<span><b>CENTRO</b></span>";
                $cabecera.="<span class='cab_class dalumnofirst' data-idal=''><b>ALUMNO</b></span>";
                $cabecera.="<span class='cab_class dalumno' data-idal=''><b>CRITERIOS DE PRIORIDAD</b></span>";
                $cabecera.="<span class='cab_class dalumno' data-idal=''><b>NORDEN</b></span>";
                $cabecera.="<span class='cab_class dalumno' data-idal=''><b>BAREMO</b></span>";
                $cabecera.='<div class="right" id=""><span><b>CAMBIO ESTADO</b></span></div>&nbsp';
                $cabecera.='</div><hr/>';
    foreach($a as $user) 
		{
    $i= $user->id_alumno;
		$li.="<div class='filasol' id='filasol".$user->id_alumno."'>";
                $li.="<span class='calumno dalumnofirst' data-idal='".$i."'>".$user->id_alumno."-".strtoupper($user->apellido1).",".strtoupper($user->apellido2);
                $li.="<span class='trans_cole dalumno' data-idal='".$i."'>".$user->trans_cole."</span";
                $li.="<span class='trans_cole dalumno' data-idal='".$i."'>".$user->numero_sorteo."</span";
                $li.="<span class='trans_cole dalumno' data-idal='".$i."'>".$user->ptstotal."</span";
                $li.='<span><div class="right" id="'.$user->estado.'"><a  class="btn btn-danger estado" id="'.$i.'" >BORRADOR</a></div>&nbsp
                <div class="right" id="'.$user->estado.'"><a  class="btn btn-info estado" id="'.$i.'" >PROVISIONAL</a></div>&nbsp;
                <div class="right" id="'.$user->estado.'"><a  class="btn btn-success estado" id="'.$i.'" >DEFINITIVO</a></div>
                <hr/></span></div>';
   }
        $html.=$cabecera.$li;
        $html.='</section>';

	return $html;
	}
  public function showFiltrosTipo()
	{
			$botones=$this->check('TODAS');
			$botones.=$this->check('EBO','tipoestudio');
			$botones.=$this->check('TVA','tipoestudio');
			return $botones;
	}

  public function showFiltrosCheck()
	{
			$botones="<div id='filtroscheck'>".$this->check('TODAS');
			$botones.="<br>FASE:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$botones.=$this->check('Borrador','fase');
			$botones.=$this->check('Validada','fase');
			$botones.=$this->check('Baremada','fase');
			$botones.="<br>ESTADO:&nbsp;&nbsp;";
			$botones.=$this->check('Irregular','estado');
			$botones.=$this->check('Duplicada','estado');
			$botones.=$this->check('Apta','estado');
			return $botones."</div>";
	}
  public function showBotones()
	{
	$botones=$this->boton('TODAS');
	$botones.=$this->boton('Borrador');
	$botones.=$this->boton('Validada irregular');
	$botones.=$this->boton('Validada duplicada');
	$botones.=$this->boton('Validada');
	$botones.=$this->boton('Baremada');
	return $botones;;
	}
  public function check($texto,$tipo='')
	{
		$ret='<label for="'.$texto.'" class="labelcheck">'.$texto.'</label>';
		if(strpos('TODAS',$texto)!==FALSE) return '<input value="0" class="filtrosoltodas" data-tipo="'.$tipo.'" id="'.$texto.'" type="checkbox">'.$ret;
		else return '<input value="0" class="filtrosol" data-tipo="'.$tipo.'" id="'.$texto.'" type="checkbox">'.$ret;

	}
  public function boton($texto)
	{
	return '<button type="button" class="btn btn-outline-dark filtrosol" id="'.$texto.'">'.$texto.'</button>';
	}
  public function showSolicitud($sol){
		
		$li="<tr class='filasol' id='filasol".$sol->id_alumno."' style='color:black'>";
    $li.="<td class='calumno dalumno' data-idal='".$sol->id_alumno."'>".$sol->id_alumno."-".strtoupper($sol->apellido1).",".strtoupper($sol->nombre)."</td>";
		$li.="<td id='print".$sol->id_alumno."' class='fase printsol'><i class='fa fa-print psol' aria-hidden='true'></i></td>";
		$li.="<td id='fase".$sol->id_alumno."' class='fase'>".$sol->fase_solicitud."</td>";
		$li.="<td id='estado".$sol->id_alumno."' class='estado'>".$sol->estado_solicitud."</td>";
		$li.="<td id='tipoens".$sol->id_alumno."' class='estado'>".$sol->tipoestudios."</td>";
		$li.="<td id='transporte".$sol->id_alumno."'>".$sol->transporte."</td>";
		$li.="<td id='pvalidados".$sol->id_alumno."'>".$sol->puntos_validados."</td>";
		$li.="<td id='nordsorteo".$sol->id_alumno."'>".$sol->nordensorteo."</td>";
		$li.="<td id='nasignado".$sol->id_alumno."'>".$sol->nasignado."</td>";
		$li.="</tr>";
	return $li;
	}
  public function showSolicitudes($a,$rol='centro')
	{
	//codigo para mostrar alumnos según tipo de inscripcion
	$html='<table class="table table-striped" id="sol_table" style="color:white">';
	$html.="<thead>
      <tr>
        <th>DATOS ALUMNO</th>
        <th></th>
        <th>FASE</th>
        <th>ESTADO</th>
        <th>TIPO</th>
        <th>CRITERIOS DE PRIORIDAD</th>
        <th>BAREMO</th>
        <th>NORDEN</th>
        <th>NALEATORIO</th>
      </tr>
    </thead>";
	$html.="<tbody>";
		foreach($a as $user) 
		{
			$html.=$this->showSolicitud($user);	
		}
	$html.="</tbody>";
	$html.='</table>';

	return $html;
	}
  public function showSolicitudListado($sol,$datos,$provisional=0){
	
		$i=0;	
		//los listados provisionales no permiten acceder a los datos de la solicitud
		if($provisional==1) $class='';
		else $class='calumno';

		$li="<tr class='filasol' id='filasol".$sol->id_alumno."' style='color:black'>";
		foreach($datos as $d)
			{
			if($i==0)
    		$li.="<td class='".$class." dalumno ".$d."' data-idal='".$sol->id_alumno."'>".strtoupper($sol->$d)."</td>";
			else
				$li.="<td id='".$d.$sol->id_alumno."' class='".$d."'>".$sol->$d."</td>";
			$i++;
			}
		$li.="</tr>";
	return $li;
	}
  public function showListado($a,$rol='centro',$cabecera=array(),$camposdatos=array(),$provisional=0)
	{
	$centroanterior='';
	$centroactual='';

	$ncolumnas=sizeof($cabecera);
	$colspan=$ncolumnas-1;
	$html='<table class="table table-striped" id="sol_table" style="color:white">';
	$html.="<thead>
      <tr>";
	foreach($cabecera as $cab)
		$html.="<th>".$cab."</th>";

  $html.="</tr></thead><tbody>";
	$cabadmin=0;
	$cab=0;
	if($rol=='centro')
			$html.="<tr class='filasol' style='color:white;background-color: #84839e;'><td colspan='".$ncolumnas."'><b>EBO</b></td></tr>";

	foreach($a as $sol) 
	{
		if($rol=='admin')
		{
			$centroanterior=$centroactual;
			$centroactual=$sol->id_centro;
			if($sol->tipoestudios=='tva' and $cabadmin==0)
			{
				$cabadmin=1;
			}
			if($centroactual!=$centroanterior)
				{
				$html.="<tr class='filasol' id='filasol".$sol->id_alumno."' style='color:white;background-color:#141259;'><td colspan='".$ncolumnas."'><b>".$sol->nombre_centro."</b></td></tr>";
				$html.="<tr class='filasol' id='filasol".$sol->id_alumno."' style='color:white;background-color: #84839e;'><td colspan='".$ncolumnas."'><b>".strtoupper($sol->tipoestudios)."</b></td></tr>";
				//$cabadmin=0;
				}
		}
		if($sol->tipoestudios=='tva' and $cab==0)
		{
			$html.="<tr class='filasol' id='filasol".$sol->id_alumno."' style='color:white;background-color: #84839e;'><td colspan='".$ncolumnas."'><b>".strtoupper($sol->tipoestudios)."</b></td></tr>";
			$cab=1;
		}
		$html.=$this->showSolicitudListado($sol,$camposdatos,$provisional);	
	}
	$html.="</tbody>";
	$html.='</table>';

	return $html;
	}
  public function showSolicitudes_old($a,$rol='centro')
	{
	//codigo para mostrar alumnos según tipo de inscripcion
	$li='';
	$html='<section class="col-lg-12 usuario" style="height:400px;">';
	$cabecera="<div class='filasol' id='cab_solicitudes'>";
	if($rol=='admin') $cabecera.="<span><b>CENTRO</b></span>";
	$cabecera.="<span><b>ESTADO</b></span>";
							$cabecera.="<span class='cab_class dalumno' data-idal=''><b>DATOS ALUMNO</b></span>";
							$cabecera.='<div class="right" id=""><span><b>CAMBIO ESTADO</b></span></div>&nbsp';
							$cabecera.='</div><hr/>';
					foreach($a as $user) 
					{
							$i= $user->id_alumno;
	$li.="<div class='filasol' id='filasol".$user->id_alumno."'>";
	$li.="<span id='estado".$user->id_alumno."'>".$user->estado."</span>";
							$li.="<span class='calumno dalumno' data-idal='".$i."'>".$user->id_alumno."-".strtoupper($user->apellido1).",";
							$li.= strtoupper($user->apellido1);
							$li.='<div class="right" id="'.$user->estado.'"><a  class="btn btn-danger estado" id="'.$i.'" >BORRADOR</a></div>&nbsp
							<div class="right" id="'.$user->estado.'"><a  class="btn btn-info estado" id="'.$i.'" >PROVISIONAL</a></div>&nbsp;
							<div class="right" id="'.$user->estado.'"><a  class="btn btn-success estado" id="'.$i.'" >DEFINITIVO</a></div>
							<hr/></span></div>';
					}
			$html.=$cabecera.$li;
			$html.='</section>';

	return $html;
	}
  public function showMatriculado($mat,$rol='centro')
	{
		$this->log_listados_matricula->warning("MOSTRANDO MATRICULA");
		$this->log_listados_matricula->warning(print_r($mat,true));
			$class='continua';
			$i= $mat->id_alumno;
			if($mat->estado=='continua') $estado='NO CONTINUA'; 
			else {$estado='CONTINUA';}

			$li='<tr>';
      $li.='<td>'.strtoupper($mat->apellidos).'</td>';
      $li.= '<td>'.strtoupper($mat->nombre).'</td>';
      $li.='<td id="tipoalumno'.$i.'">'.strtoupper($mat->tipo_alumno_actual).'</td>';
      $li.= '<td id="estado'.$i.'">'.strtoupper($mat->estado).'</td>';
     
			if($mat->tipo_alumno_actual=='tva')
			{ 
			if($rol=='admin' || $rol=='sp')
	      			$li.= '<td><button type="button" class="btn btn-info cambiar" id="cambiar'.$i.'">CAMBIA A EBO</button></td>';
      			$li.= '<td><button type="button" class="btn btn-info continua" id="continua'.$i.'">'.$estado.'</button></td>';
			}
			if($mat->tipo_alumno_actual=='ebo')
			{ 
			if($rol=='admin' || $rol=='sp')
      				$li.= '<td><button type="button" class="btn btn-info cambiar" id="cambiar'.$i.'">CAMBIA A TVA</button></td>';
	      		$li.= '<td><button type="button" class="btn btn-info continua" id="continua'.$i.'">'.$estado.'</button></td>';
			}
			$li.='</tr>';
		return $li;
	}
  public function showMatriculados($a,$rol='centro',$id_centro=9999)
	{
		$this->log_listados_matricula->warning("MOSTRANDO MATRICULADOS rol: ".$rol);
		$this->log_listados_matricula->warning(print_r($a,true));
	//codigo para mostrar alumnos según tipo de inscripcion
		$tmat='<table id="mat_table'.$id_centro.'" class="table usuario table-striped">
			    <thead class="theadmat">
			      <tr>
				<th>APELLIDOS</th>
				<th>NOMBRE</th>
				<th>TIPO</th>
				<th>ESTADO</th>';
		if($rol=='admin' || $rol=='sp')
			$tmat.='<th>CAMBIO TIPO</th>';
		$tmat.='<th>CAMBIO CONTINUA</th></tr></thead><tbody>';

		foreach($a as $matricula) 
		{
		$tmat.=$this->showMatriculado($matricula,$rol);    
    }
    $tmat.='</tbody></table>';

	return $tmat;
	}
  
	public function listadoMatriculados($a)
	{
	//codigo para mostrar alumnos según tipo de inscripcion
	$li='<section class="col-lg-12 usuario" style="height:400px;">';
            foreach($a as $user) 
		{
                $i= $user->id_alumno;
                $li.= $user->id_alumno."-".$user->nombre."-";
                $li.= $user->apellido1."-";
                $li.=$user->nacionalidad;
                $li.='<div class="right"><a  class="btn btn-danger continua" id="'.$i.'" >NOCONTINUA</a></div>
                <hr/>';
            	}
        $li.='</section>';

	return $li;
	}
    
  public function showTablaResumenSolicitudesCentros($a,$nombre_centro='')
	{
		$centros=$this->getCentrosIds();	
		foreach($centros as $centro)
		{
		}
	}
  public function showTablaResumenSolicitudes($a,$nombre_centro='',$id_centro)
	{
	$tres='<div id="tresumen'.$id_centro.'" class="container tresumensol"><h2>SOLICITUDES <span class="cabcensol" id="cabcensol'.$id_centro.'">'.strtoupper($nombre_centro).'</span></h2>';
	$movil=array();
	if(sizeof($a)>0)
	{
		foreach($a as $key => $row) 
			{ 
			foreach($row as $field => $value) 
			{ 
			$movil[$field][] = $value; 
			} 
		}
	}
 	$tres.='<table class="table table-dark table-striped mov">
    <thead>
      <tr>
        <th>CENTRO</th>
        <th>NUMERO SOLICITUDES</th>
      </tr>
    </thead>
    <tbody>';
	$i=0;
	$campos=array('Borrador','Validadas','Baremadas');
	foreach($movil as $me)
	{
	if($i==0) {$i++; continue;}
	$tres.="<tr>
        	<td>".$campos[$i-1]."</td>
        	<td>".$me[0]."</td>
	      	</tr>";
	$i++;
	}
    	$tres.="</tbody> </table>";
	#tabla para modo escritorio
 	$tres.='<table class="table table-dark table-striped desk" id="table'.$id_centro.'">
    <thead>
      <tr>
        <th>Centro</th>
        <th>Borrador</th>
        <th>Validadas</th>
        <th>Baremadas</th>
      </tr>
    </thead>
    <tbody>';
	 
	foreach($a as $obj)
	$tres.="<tr>
        <td style='width: 16.66%'>".$obj->centro."</td>
        <td>".$obj->borrador."</td>
        <td>".$obj->validada."</td>
        <td>".$obj->baremada."</td>
      	</tr>";
	
    	$tres.="</tbody> </table></div>";
		
	return $tres;
	}
  public function showTablaResumenMatricula($a,$datos='matricula',$nombre_centro='',$rol='centro',$cabecera='si',$id_centro){
	$display='';
	//if($cabecera=='no') $display='none';
	$this->log_listado_general->warning('DISPLAY/CABECERA: '.$display.'--'.$cabecera);
	$tres='<div id="table'.$id_centro.'" class="container tresumenmat"><h2>'.strtoupper($datos).'-- <span  class="cabcenmat" id="cabcen'.$id_centro.'">'.strtoupper($nombre_centro).'</span></h2>';
 	$tres.='<table class="table table-dark table-striped desk" id="table'.$id_centro.'">';
  	if($cabecera=='si')
		 $tres.='<thead>
		      <tr>
			<th>Tipo</th>
			<th>Grupos</th>
			<th>Puestos</th>
			<th>Plazas Ocupadas</th>
			<th>Vacantes</th>
		      </tr>
		    </thead>
		    <tbody>';
			else
			 $tres.='<thead>
		      <tr>
			<th>Tipo</th>
			<th>Grupos</th>
			<th>Puestos</th>
			<th>Plazas Ocupadas</th>
			<th>Vacantes</th>
		      </tr>
		    </thead>
		    <tbody>';
 
	foreach($a as $obj)
	{
	if($obj->ta=='') $obj->ta='tva';
	$tres.="<tr>
        <td>".$obj->ta."</td>
        <td>".$obj->grupo."</td>
        <td>".$obj->puestos."</td>
        <td>".$obj->plazasactuales."</td>
        <td id='vacantesmat_".$obj->ta."_desk".$id_centro."'>".$obj->vacantes."</td>
      	</tr>";
	}
    	$tres.="</tbody> </table></div>";
		
	return $tres;
	}
    public function vlistadoMatriculados(){
	//Creamos el objeto alumno
        $alumno=new Alumno($this->adapter);
         
        //Conseguimos todos los alumnos
        $this->allalumnos=$alumno->getAll();
 
        //Cargamos la vista index y le pasamos valores
        $this->view("index",array(
            "allalumnos"=>$this->allalumnos,
	    "listadohtml"=>$this->listadoInscritos($this->allalumnos)
        ));
	
	}
    public function listadoInscritos($a){
	//codigo para mostrar alumnos según tipo de inscripcion
	$li='<section class="col-lg-12 usuario" style="height:400px;">';
            foreach($a as $user) 
		{
                $i= $user->id_alumno;
                $li.= $user->id_alumno."-".$user->nombre."-";
                $li.= $user->apellido1."-";
                $li.=$user->nacionalidad;
                $li.='<div class="right"><a  class="btn btn-danger continua" id="'.$i.'" >NOCONTINUA</a></div>
                <hr/>';
            	}
        $li.='</section>';

	return $li;
	}
 
    public function genCsv($solicitudes,$idcentro=1,$tipo,$cab=array(),$datos=array(),$dir)
		{
			$linea=array();
			$nfichero=$dir.'/'.$tipo.'.csv';
			$fp = fopen($nfichero, 'w'); 
			$this->log_gencsvs->warning("GENERANDO CSV FICHERO: ".$nfichero);
			$this->log_gencsvs->warning("GENERANDO CSV, CABECERA:");
			$this->log_gencsvs->warning(print_r($datos,true));
			$this->log_gencsvs->warning("GENERANDO CSV, CONTENIDO:");
			$this->log_gencsvs->warning(print_r((array)$solicitudes,true));
			//grabamos cabecera
			fputcsv($fp,$cab,';');
			foreach($solicitudes as $sol)
			{
			$sol=(array)$sol;
			$linea=array();
				foreach($datos as $k)
				{
					$linea[$k]=utf8_decode($sol[$k]);
				}
				
				fputcsv($fp,$linea,';');
			}
			fclose($fp);
		return $tipo.'.csv';
    }

    public function getResumenMatriculaCentros()
		{
      //obtenemos resumen
			$resumencentros=$centro->getDatosMatriculaCentro($rol,'matricula',$id_centro);
			return $resumencentros;
		
		}

    public function getCentrosIds($provincia='todas')
		{
			if($provincia!='todas')
				$sql="SELECT distinct(cg.id_centro) from centros_grupos cg, centros c where c.id_centro=cg.id_centro and provincia='$provincia'";
			else
				$sql="SELECT distinct(id_centro) from centros_grupos";
			$this->log_listados_solicitudes->warning("CONSULTA LISTADO SOLICITUDES: ".$sql);
			$r=$this->adapter->query($sql);
			 while ($obj = $r->fetch_object()) 
				{
        $ares[]=$obj;
    		}
			return $ares;
		}
    public function getResumenMatriculaCentro($rol,$id_centro=1,$tiposol=0,$modo='csv')
		{

			$i=0;
			$amatcentros=array();	
				$centros=$this->getCentrosIds();
				foreach($centros as $c)
				{
					$centro=new Centro($this->adapter,$c->id_centro,'no');
					$centro->setNombre();
					$matcentros=$centro->getDatosMatriculaCentro($rol,'matricula',$c->id_centro);
					$amatcentros[$i]['nombre_centro']=str_replace(',','',$centro->getNombre());
					$amatcentros[$i]['gruposebo']=$matcentros[0]->grupo;
					$amatcentros[$i]['puestosebo']=$matcentros[0]->puestos;
					$amatcentros[$i]['plazasactualesebo']=$matcentros[0]->plazasactuales;
					$amatcentros[$i]['vacantesebo']=$matcentros[0]->vacantes;
					$amatcentros[$i]['grupostva']=$matcentros[1]->grupo;
					$amatcentros[$i]['puestostva']=$matcentros[1]->puestos;
					$amatcentros[$i]['plazasactualestva']=$matcentros[1]->plazasactuales;
					$amatcentros[$i]['vacantestva']=$matcentros[1]->vacantes;
					$i++;
				}
			return $amatcentros;
		
		}
    public function listado(){
        //Creamos el objeto centro
        $centro=new Centro($this->adapter);
        
	//obtenemos resumen
	$resumencentros=$centro->getResumen();
        
	//Creamos el objeto usuario
        $alumno=new Alumno($this->adapter);
         
        //Conseguimos todos los usuarios
        $allalumnos=$alumno->getAll();
 
        //Cargamos la vista index y le pasamos valores
        $this->view("listado",array(
            "allalumnos"=>$allalumnos,
            "resumencentros"=>$resumencentros
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
     
     
}
?>
