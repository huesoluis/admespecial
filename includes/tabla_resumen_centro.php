<div id="tresumen" class="container">
<h2>TABLA RESUMEN PLAZAS</h2>
<?php $d=$datos['resumencentros'];
$movil=array();
foreach($d as $key => $row) 
{ 
	foreach($row as $field => $value) 
	{ 
	$movil[$field][] = $value; 
	} 
}
?>
 <table class="table table-dark table-striped mov">
    <thead>
      <tr>
        <th></th>
        <th>ebo</th>
        <th>tva</th>
      </tr>
    </thead>
    <tbody>
	<?php 
	$i=0;
	$campos=array('grupos','puestos','plazas ocupadas','vacantes');
	foreach($movil as $me)
	{
	if($i==0) {$i++; continue;}
	print("<tr>
        	<td>".$campos[$i-1]."</td>
        	<td>".$me[0]."</td>
        	<td>".$me[1]."</td>
	      	</tr>");
	$i++;
	}
	?>
    </tbody>
  </table>
 <table class="table table-dark table-striped desk">
    <thead>
      <tr>
        <th>Tipo</th>
        <th>Grupos</th>
        <th>Puestos</th>
        <th>Plazas Ocupadas</th>
        <th>Vacantes</th>
      </tr>
    </thead>
    <tbody>
	<?php 
foreach($d as $obj)
	print("<tr>
        <td>".$obj->ta."</td>
        <td>".$obj->grupo."</td>
        <td>".$obj->puestos."</td>
        <td>".$obj->plazasactuales."</td>
        <td>".$obj->vacantes."</td>
      	</tr>");
	?>
    </tbody>
  </table>
</div>
