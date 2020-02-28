        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>MENU PRINCIPAL</h3>
            </div>

            <ul class="list-unstyled components" id="menulateral">
                <p></p>
		<li style="padding-left:10px;color:black;">
			<input style="padding-left:1px;" id="buscar_centros"  placeholder="Introduce el centro"  /> 
		</li>
		<hr>
                <li>
                    <p id="show_matricula"><a>Listado Alumnos Matriculados</a></p>
                </li>
		<hr>
                <li>
                    <p id="show_solicitudes"><a>Listado Solicitudes</a></p>
                </li>
                <li class="active" >
                    <p id="tabla_rmatricula" ><a href="<?php echo URL_BASE.'?controller=Centros';?>">Tabla Resumen Matrícula</a></p>
                </li>
		<hr>
                <li>
                    <p id="nalumno"><a href="<?php echo URL_BASE.'?controller=Alumnos&action=formNuevoAlumno';?>">Nueva Solicitud</a></p>
                </li>
		<hr>
                <li>
                    <p id="nalumno"><a href="">Listado Alumnos excluidos matriculados</a></p>
                </li>
		<hr>
                <li>
                    <p id="nalumno"><a href="">Listado Provisional</a></p>
                </li>
		<hr>
                <li>
                    <p id="nalumno"><a href="">Listado Definitivo</a></p>
                </li>
            </ul>

        </nav>
