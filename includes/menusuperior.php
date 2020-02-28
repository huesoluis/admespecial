<?php if(isset($_SESSION['provincia'])) {$provincia=$_SESSION['provincia'];} else $provincia='ARAGON';?>            
<h2 style='text-align:center;'>ADMISION ALUMNOS EDUCACION ESPECIAL <?php echo strtoupper($provincia);?></h2>
		 <p hidden id='id_centro'><?php echo $_SESSION['id_centro'];?></p> 
		 <p hidden id='id_estado_convocatoria'><?php echo $_SESSION['estado_convocatoria'];?></p> 
		 <p hidden id='id_sorteo'><?php echo $_SESSION['sorteo'];?></p> 
<!--elementos a la izda-->
<nav id='navgir' class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
        <ul class="navbar-nav mr-auto">
                            <li class="nav-item  msuperior">
            			<a style='color:white!important;float:left!important'  href='<?php echo $_SESSION['url_base'];?>'>INICIO</a>
                            </li>
                            <li class="nav-item active msuperior dropdown">
				<?php if($_SESSION['sorteo']==1){?>
                                <a class="nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown">Documentación</a>
				 <div class="dropdown-menu">
				 <a class="dropdown-item" href="documentacion/instrucciones_proceso.pdf" id="docinst" target="_blank">Instrucciones generales</a>
				 </div>
				<?php }?>
                            </li>
        </ul>
    </div>
<!--elementos a la derecha-->
    <div class="mx-auto order-0">
    <!--espacio para el centro-->
    </div>
    <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
                            <li class="nav-item msuperior dropdown">
                                <a class="show_provisionales nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown" href="#">EXPORTAR DATOS</a>
				 <div class="dropdown-menu">
				<?php 	if($_SESSION['provincia']!='aragon' or $_SESSION['rol']=='admin'){ echo '<a class="exportcsv dropdown-item" href="#" id="csv_mat" data-tipo="csv" data-subtipo="csv_mat">Listado vacantes (csv)  </a>';}?>
				<?php 	if($_SESSION['rol']=='centro'){ echo '<a class="exportpdf dropdown-item" href="#" id="pdf_mat" data-tipo="pdf" data-subtipo="pdf_mat">Listado vacantes (pdf) </a>';}?>
				<a class="exportcsv dropdown-item" href="#" id="csv_sol" data-tipo="csv" data-subtipo="csv_sol">Listado solicitudes (csv)</a>
				<a class="exportcsv dropdown-item" href="#" id="csv_pro" data-tipo="csv" data-subtipo="csv_pro">Listado alumnos promocionan (csv)</a>
				<?php 	if($_SESSION['provincia']!='aragon' or $_SESSION['rol']=='admin'){ echo '<a class="exportcsv dropdown-item" href="#" id="csv_dup" data-tipo="csv" data-subtipo="csv_dup">Listado duplicados (csv) </a>';}?>
				 </div>
                            </li>
                            <li class="nav-item active msuperior">
                                <a class="show_matricula nav-link" href="#">Matricula</a>
                            </li>
                            <li class="nav-item active msuperior">
                                <a class="show_solicitudes nav-link" href="#">Solicitudes</a>
                            </li>
		<?php if($_SESSION['estado_convocatoria']>=1){?>
                            <li class="nav-item active msuperior dropdown">
				<?php if($_SESSION['sorteo']==1){?>
                                <a class="show_provisionales nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown" href="#">Sorteo</a>
				 <div class="dropdown-menu">
				 <a class="lgenerales dropdown-item" href="#" id="sor_ale" data-subtipo="sor_ale" data-tipo="sorteo">Numero aleatorio </a>
				 <a class="lgenerales dropdown-item" href="#" data-tipo="sorteo" data-subtipo="sor_bar">Solicitudes baremadas</a>
				 <a class="lgenerales dropdown-item" href="#" data-tipo="sorteo" data-subtipo="sor_det">Detalle baremo</a>
				 </div>
				<?php }?>
                            </li>
			<?php if($_SESSION['estado_convocatoria']>=1){?>
                            <li class="nav-item active msuperior dropdown">
                                 <a class="show_provisionales nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown" href="#">Provisional</a>
				 <div class="dropdown-menu">
				 <a class="lprovisionales dropdown-item" href="#" data-subtipo="admitidos_prov">Admitidos provisional</a>
				 <a class="lprovisionales dropdown-item" href="#" data-subtipo="noadmitidos_prov">No admitidos provisional</a>
				 <a class="lprovisionales dropdown-item" href="#" data-subtipo="excluidos_prov">Excluidos provisional</a>
																		 </div>
                            </li>
			<?php }?>
			<?php if($_SESSION['estado_convocatoria']==4){?>
                            <li class="nav-item active msuperior dropdown">
                                 <a class="show_provisionales nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown" href="#">Definitivos</a>
		                 <div class="dropdown-menu">
				 <a class="ldefinitivos dropdown-item" href="#" data-subtipo="admitidos_def">Admitidos definitivo</a>
				 <a class="ldefinitivos dropdown-item" href="#" data-subtipo="noadmitidos_def">No admitidos definitivo</a>
				 <a class="ldefinitivos dropdown-item" href="#" data-subtipo="excluidos_def">Excluidos definitivo</a>
				 </div>
                            </li>
			<?php }?>
		<?php }?>
        </ul>
    </div>
</nav>
<?php if($_SESSION['id_centro']==1) 
echo '<input class="form-control" id="fcentrosadmin" placeholder="Introduce datos del centro" type="text">';
?>
