$(document).ready(function(){

var botoncontrol="<button id='' type='button' class='btn btn-outline-dark'>Validar baremo</button>";

$( ":input" ).select(function() {

});

//METODOS DE CALCULO DE SORTEO

$('body').on('click', '#boton_asignar_numero', function(e){
var vrol=$('#rol').attr("value");
var vestado_convocatoria=$('#estado_convocatoria').text();
var vidcentro=$('#id_centro').text();
	$.ajax({
	  method: "POST",
	  data: {asignar:'1',id_centro:vidcentro,rol:vrol,estado_convocatoria:vestado_convocatoria},
	  url:'../scripts/ajax/listados_solicitudes.php',
	      success: function(data) {
			        $("#tresumen"+vidcentro).remove();
                                $("#filtroscheck").remove();
                                $("#nuevasolicitud").remove();
                                $("#form_sorteo").remove();
                                $("#sol_table").remove();
                                $("#filtrosol").after(data);
                                $("#num_sorteo").prop("disabled",false);
		},error: function (request, status, error) {
        alert(error);
    }
	});
});

//REALIZAR SORTEO

$('body').on('click', '#boton_realizar_sorteo', function(e){
var vid=$(this).attr("id");
var vrol=$('#rol').attr("value");
var vidcentro=$('#id_centro').text();
var vsolicitudes=$(this).attr("data-solicitudes");
var vnum_sorteo=$('#num_sorteo').val();
var vnum_solicitudes=$('#num_solicitudes').val();
var isnum = /^\d+$/.test(vnum_sorteo);
if (!isnum) {
    alert('No es un numero');
return;
}
if (parseInt(vnum_solicitudes)<parseInt(vnum_sorteo) || parseInt(vnum_sorteo)<=0) {
    alert('Introduce un numero entre 1 y '+vnum_solicitudes);
return;
}
	$.ajax({
	  method: "POST",
	  data: {id_centro:vidcentro,nsorteo:parseInt(vnum_sorteo),rol:vrol},
	  url:'../scripts/ajax/listados_solicitudes.php',
	      success: function(data) {
				if(data.indexOf('NO HAY VACANTES')!=-1)
				{
					$.alert({
								title: 'NO HAY VACANTES EN O NO HAY SOLCITUDES APTAS',
								content: 'CONTINUAR'
							});
				return;
				}
				else
				{
				$("#l_matricula").html(data);
				$("#tresumen").hide();
				}
		},
	      error: function() {
		alert('Problemas listando solicitud!');
	      }
	});
});
//METODOS DE CALCULO DE HERMANOS
function calcular_hermanos(id)
{
var total_hadmision=0;
var th1=$('#hermanos_datos_admision1'+id).val();
var th2=$('#hermanos_datos_admision2'+id).val();
var th3=$('#hermanos_datos_admision3'+id).val();

if(th1!='') total_hadmision++; 
if(th2!='') total_hadmision++; 
if(th3!='') total_hadmision++; 

return total_hadmision;
}

$('body').on('change', 'input[id*=hermanos_datos_admision]', function(e){
var vid=$(this).attr("id");
vid=vid.replace('hermanos_datos_admision','');
var vid =vid.substring(1, vid.length);
var nher=calcular_hermanos(vid);
$("#hermanosadmision"+vid).attr("value",calcular_hermanos(vid));
});

$('body').on('change', 'input[id*=num_hadmision],input[id*=num_hbaremo]', function(e){

var vid=$(this).attr("id");
vid=vid.replace('num_hadmision','');
vid=vid.replace('num_hbaremo','');
var nhermanosadmin=calcular_hermanos(vid);

$("#num_hadmision"+vid).attr('value',nhermanosadmin);
});

//METODOS DE CALCULO DE BAREMO

//EVENTOS CHECK BAREMO
/////////////////////////////////////////////////////////////////////////////////////////////////

//Mostrar/ocultar domicilio laboral

$('body').on('change', 'input[type=radio][name*=baremo_proximidad_domicilio]', function(e){
var vid=$(this).attr("name");
var vid=vid.replace('baremo_proximidad_domicilio','');

var bar_def=recalcular_baremo(vid);
if($(this).attr("data-dom")=='laboral')
{
$("#calle_dlaboral").toggle('slow');
$('#calle_dllimitrofe').hide('slow');
}else if($(this).attr("data-dom")=='limitrofe')
{
$("#calle_dllimitrofe").toggle('slow');
$('#calle_dlaboral').hide('slow');
}
else{
$('#calle_dlaboral').hide('slow');
$('#calle_dllimitrofe').hide('slow');
}
});

$('body').on('change', 'input[type=checkbox][name*=baremo_tutores_centro]', function(e){
var vid=$(this).attr("id");
var vid=vid.replace('baremo_tutores_centro','');
var bar_def=recalcular_baremo(vid);
var val=$(this).attr("value");
if(val=='0')
		$(this).attr('value','1');
else
		$(this).attr('value','0');
});


$('body').on('change', 'input[type=checkbox][name*=baremo_renta_inferior]', function(e){
var vid=$(this).attr("id");
var vid=vid.replace('baremo_renta_inferior','');

var bar_def=recalcular_baremo(vid);
var val=$(this).attr("value");

$("#labeltributo"+vid).toggle();
$("#tributo").toggle();
if(val=='0')
		$(this).attr('value','1');
else
		$(this).attr('value','0');

});


$('body').on('change', 'input[id*=hermanos_datos_baremo]', function(e){

var vid=$(this).attr("id");
vid=vid.replace('hermanos_datos_baremo','');
var nhermano=vid.charAt(0);
//quitamos el primer caracter
vid=vid.substr(1);
var bar_def=recalcular_baremo(vid);
$("#id_puntos_baremo"+vid).text(bar_def);
});

$('body').on('change', 'input[type=radio][name*=baremo_discapacidad]', function(e)
{
	var vid=$(this).attr("name");
	var vid=vid.replace('baremo_discapacidad','');
	var bar_def=recalcular_baremo(vid);

});

/////////////////////////////////////////////////////////////////////////////////////////////////

$('body').on('change', 'input[type=radio][name*=baremo_tipo_familia]', function(e)
{
	var vid=$(this).attr("name");
	var vid=vid.replace('baremo_tipo_familia','');
	var bar_def=recalcular_baremo(vid);

});

function recalcular_baremo(id){
var totalbaremo=0;
var total_hbaremo=0;
var total_baremo_validado=0;

var baremo1=$('input[name=baremo_proximidad_domicilio'+id+']:checked').attr("data-baremo");
var baremo1_validado=$('#baremo_validar_proximidad_domicilio'+id).val();


var baremo2=$('input[id=baremo_tutores_centro'+id+']:checked').attr("data-baremo");
var baremo2_validado=$('#baremo_validar_tutores_centro'+id).val();

var baremo3=$('input[id=baremo_renta_inferior'+id+']:checked').attr("data-baremo");
var baremo3_validado=$('#baremo_validar_renta_inferior'+id).val();

var baremo4=$('input[name=baremo_discapacidad'+id+']:checked').attr("data-baremo");
var baremo4_validado=$('#baremo_validar_discapacidad'+id).val();

var baremo5=$('input[name=baremo_tipo_familia'+id+']:checked').attr("data-baremo");
var baremo5_validado=$('#baremo_validar_tipo_familia'+id).val();

var baremo_h1=$('#hermanos_datos_baremo1'+id).val();
var baremo_h2=$('#hermanos_datos_baremo2'+id).val();
var baremo_h3=$('#hermanos_datos_baremo3'+id).val();
var baremo6_validado=$('#baremo_validar_hnos_centro'+id).val();

if(baremo1)
	{
	totalbaremo=totalbaremo+parseInt(baremo1);
	if(baremo1_validado==1) 	total_baremo_validado=total_baremo_validado+parseInt(baremo1);
	}
if(baremo2)
	{
	totalbaremo=totalbaremo+parseInt(baremo2);
	if(baremo2_validado==1) 	total_baremo_validado=total_baremo_validado+parseInt(baremo2);
	}
if(baremo3)
	{
	totalbaremo=totalbaremo+parseInt(baremo3);
	if(baremo3_validado==1) 	total_baremo_validado=total_baremo_validado+parseInt(baremo3);
	}
if(baremo4)
	{
	totalbaremo=totalbaremo+parseFloat(baremo4);
	if(baremo4_validado==1) 	total_baremo_validado=total_baremo_validado+parseFloat(baremo4);
	}
if(baremo5)
	{
	totalbaremo=totalbaremo+parseFloat(baremo5);
	if(baremo5_validado==1) 	total_baremo_validado=total_baremo_validado+parseFloat(baremo5);
	}
//calculo baremo de hermanos en el centro
if($('#num_hbaremo'+id).is(':checked'))
{
if(baremo_h1.length>=3) 
	{
	total_hbaremo=total_hbaremo+8;
	}
if(baremo_h2.length>=3) 
	{
	total_hbaremo=total_hbaremo+1;
	}
if(baremo_h3.length>=3) 
	{
	total_hbaremo=total_hbaremo+1;
	}
if(baremo6_validado==1){ 	total_baremo_validado=total_baremo_validado+parseFloat(total_hbaremo);	}
}
else
	{ 
	total_hbaremo=0;
	}
totalbaremo=totalbaremo+total_hbaremo;

$("#id_puntos_baremo_totales"+id).text(totalbaremo);
$("#id_puntos_baremo_validados"+id).text(total_baremo_validado);
//cambiamos valore sen campos de formulario
$("#btotales"+id).val(totalbaremo);
$("#bvalidados"+id).val(total_baremo_validado);
comprobar_baremo(totalbaremo,total_baremo_validado,id);

return totalbaremo;
}

//METODOS VALIDACION DE BAREMO
/////////////////////////////////////////////////////////////////////////////////////////////////

$('body').on('click', 'button[name*=boton_baremo_validar_proximidad_domicilio]', function(e){
var vid=$(this).attr("name");
vid=vid.replace('boton_baremo_validar_proximidad_domicilio','');
var texto=$(this).text();

if(texto=='Validar domicilio')
{
var val_def=recalcular_validacion(vid);

if(val_def!=0)
	{
	$('#labelbaremo'+vid).removeClass('crojo');
	$('#labelbaremo'+vid).addClass('cverde');
	}
//modificamos el campo oculto para el formulario
$('#baremo_validar_proximidad_domicilio'+vid).val('1');
$(this).text('Invalidar domicilio');
}
else
	{
	$(this).text('Validar domicilio');
	$('#baremo_validar_proximidad_domicilio'+vid).val('0');
	$('#labelbaremo'+vid).removeClass('cverde');
	$('#labelbaremo'+vid).addClass('crojo');
	}
var rb=recalcular_baremo(vid);
});


$('body').on('click', 'button[name*=boton_baremo_validar_tutores_centro]', function(e){
var vid=$(this).attr("name");
vid=vid.replace('boton_baremo_validar_tutores_centro','');
var texto=$(this).text();

if(texto=='Validar tutores trabajan centro')
{
var val_def=recalcular_validacion(vid);

if(val_def!=0)
	{
	$('#labelbaremo'+vid).removeClass('crojo');
	$('#labelbaremo'+vid).addClass('cverde');
	}

$('#baremo_validar_tutores_centro'+vid).val('1');
$(this).text('Invalidar tutores trabajan centro');
}
else
	{
	$(this).text('Validar tutores trabajan centro');
	$('#baremo_validar_tutores_centro'+vid).val('0');
	$('#labelbaremo'+vid).removeClass('cverde');
	$('#labelbaremo'+vid).addClass('crojo');
	}
var bar_def=recalcular_baremo(vid);
});

$('body').on('click', 'button[name*=boton_baremo_validar_renta_inferior]', function(e){
var vid=$(this).attr("name");
vid=vid.replace('boton_baremo_validar_renta_inferior','');
var texto=$(this).text();
if(texto=='Validar renta')
{
var val_def=recalcular_validacion(vid);

if(val_def!=0)
	{
	$('#labelbaremo'+vid).removeClass('crojo');
	$('#labelbaremo'+vid).addClass('cverde');
	}

$('#baremo_validar_renta_inferior'+vid).val('1');
$(this).text('Invalidar renta');
}
else
	{
	$(this).text('Validar renta');
	$('#baremo_validar_renta_inferior'+vid).val('0');
	$('#labelbaremo'+vid).removeClass('cverde');
	$('#labelbaremo'+vid).addClass('crojo');
	}
var rb=recalcular_baremo(vid);
});


$('body').on('click', 'button[name*=boton_baremo_validar_hnos_centro]', function(e){
var vid=$(this).attr("name");
vid=vid.replace('boton_baremo_validar_hnos_centro','');
var texto=$(this).text();

if(texto=='Validar hermanos')
{
var val_def=recalcular_validacion(vid);

if(val_def!=0)
	{
	$('#labelbaremo'+vid).removeClass('crojo');
	$('#labelbaremo'+vid).addClass('cverde');
	}
//modificamos el campo oculto para el formulario
$('#baremo_validar_hnos_centro'+vid).val('1');
$(this).text('Invalidar hermanos');
}
else
	{
	$(this).text('Validar hermanos');
	$('#baremo_validar_hnos_centro'+vid).val('0');
	$('#labelbaremo'+vid).removeClass('cverde');
	$('#labelbaremo'+vid).addClass('crojo');
	}
var rb=recalcular_baremo(vid);
});

$('body').on('click', 'button[name*=boton_baremo_validar_discapacidad]', function(e){
var vid=$(this).attr("name");
vid=vid.replace('boton_baremo_validar_discapacidad','');
var texto=$(this).text();

if(texto=='Validar discapacidad')
{
var val_def=recalcular_validacion(vid);

if(val_def!=0)
	{
	$('#labelbaremo'+vid).removeClass('crojo');
	$('#labelbaremo'+vid).addClass('cverde');
	}
//modificamos el campo oculto para el formulario
$('#baremo_validar_discapacidad'+vid).val('1');
$(this).text('Invalidar discapacidad');
}
else
	{
	$(this).text('Validar discapacidad');
	$('#baremo_validar_discapacidad'+vid).val('0');
	$('#labelbaremo'+vid).removeClass('cverde');
	$('#labelbaremo'+vid).addClass('crojo');
	}
var rb=recalcular_baremo(vid);
});

$('body').on('click', 'button[name*=boton_baremo_validar_tipo_familia]', function(e){
var vid=$(this).attr("name");
vid=vid.replace('boton_baremo_validar_tipo_familia','');
var texto=$(this).text();

if(texto=='Validar familia')
{
var val_def=recalcular_validacion(vid);

if(val_def!=0)
	{
	$('#labelbaremo'+vid).removeClass('crojo');
	$('#labelbaremo'+vid).addClass('cverde');
	}
//modificamos el campo oculto para el formulario
$('#baremo_validar_tipo_familia'+vid).val('1');
$(this).text('Invalidar familia');
}
else
	{
	$(this).text('Validar familia');
	$('#baremo_validar_tipo_familia'+vid).val('0');
	$('#labelbaremo'+vid).removeClass('cverde');
	$('#labelbaremo'+vid).addClass('crojo');
	}
var rb=recalcular_baremo(vid);
});
/////////////////////////////////////////////////////////////////////////////////////////////////
function comprobar_baremo(tb,tbv,vid){
if(tb==tbv)
	{
	$('#labelbaremo'+vid).removeClass('crojo');
	$('#labelbaremo'+vid).addClass('cverde');
	}
else{
	$('#labelbaremo'+vid).removeClass('cverde');
	$('#labelbaremo'+vid).addClass('crojo');
}
return 1;
}

function recalcular_validacion(id){
var totalvalido=1;
var valido=0;
var vpd=$('#baremo_validar_proximidad_domicilio'+id).val();
if(vpd==0) return 0;
var vtc=$('#baremo_validar_tutores_centro'+id).val();
if(vtc==0) return 0;
var vr=$('#baremo_validar_renta_inferior'+id).val();
if(vr==0) return 0;

return 1;

}
/////////////////////////////////////////////////////////////////////////////////////////////////

//CHECKS FILTRO SOLICITUDES
$('body').on('click', '.filtrosol,.filtrosoltodas', function(e)
{
		var expression = false;
    var nombre = $(this).attr("id");
    var tipo = $(this).attr("data-tipo");

		filtrar_solicitudes(tipo,nombre);

});

function filtrar_solicitudes(t,n)
{
		var estado=$(this).prop('checked');
		n=n.toLowerCase();
		//si pulsamos en el check de todas se muestran todas y ponemo sel estado a false
    if(n.indexOf("todas")!=-1)
	{
    	$(".filasol").each(function () 
			{
	    	$(this).show();
				$(".filtrosol").prop('checked', false); 
	    });
		if(estado)
		{
	        $(".filtrosol").each(function () 
		{
    		if($(this).attr("id").indexOf('TODAS')==-1) $(this).prop('checked', false);
		});
		}	
		return;
	}
	//detectar los checks marcados
	var todas=$('#TODAS').prop('checked');
	var borrador=$('#Borrador').prop('checked');
	var validada=$('#Validada').prop('checked');
	var baremada=$('#Baremada').prop('checked');
	var ebo=$('#EBO').prop('checked');
	var tva=$('#TVA').prop('checked');
	var nb='nofase';
	var na='nofase';
	var nv='nofase';
	
	var nte='notipo';

	if(validada) na='validada';
	if(borrador) nb='borrador';
	if(baremada) nb='baremada';
	if(ebo) nte='ebo';
	if(tva) nte='tva';
	var finder = "";
	//si ambos, ebo y tva están pulsados mostramos todo
	var pulsados=0;
	if(ebo && tva) pulsados=1;

	$(".filasol").each(function () 
	{
  	var title = $(this).text();
		if(t.indexOf("fase")<0) var valor = $(this).children().eq(2).text();
		else var valor = $(this).children().eq(2).text();
				if (valor.toLowerCase().indexOf(nb.toLowerCase()) >= 0 || valor.toLowerCase().indexOf(na.toLowerCase()) >= 0 || valor.toLowerCase().indexOf(nv.toLowerCase()) >= 0) 
				{
						$(this).show();
				} 
				else 
				{
						$(this).hide();
				}
});
	$(".filasol").each(function () 
	{
  	var title = $(this).text();
		var valor = $(this).children().eq(3).text();
				if (valor.toLowerCase().indexOf(nte.toLowerCase()) >= 0) 
				{
						$(this).show();
				} 
				else if(pulsados==0 && !todas) 
				{
						$(this).hide();
				}
});
};
//filtro general de solicitudes
$('body').on('keyup', '#filtrosol', function(e){
		var expression = false;
            var value = $(this).val();
            if(value.length<=2){
            $(".filasol").each(function () {
	    			$(this).show();
	    			});
	    			}
            if(value.length<3) return;

	    var finder = "";
	    if (value.indexOf("\"") > -1 && value.lastIndexOf("\"") > 0) {
                finder = value.substring(eval(value.indexOf("\"")) + 1, value.lastIndexOf("\""));
                expression = true;
            }
            $(".filasol").each(function () {
                var title = $(this).text();
                if (expression) {
                    if ($(this).text().toLowerCase().search(finder.toLowerCase()) == -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                } else {
                    if (title.toLowerCase().indexOf(value.toLowerCase()) < 0) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        });
//FIN filtro solicitudes

//CHECKS FILTRO TIPO ENSENANZA


$('body').on('click', '.bform', function(e){
 $([document.documentElement, document.body]).animate({
        scrollTop: $(this).offset().top
    }, 1000);

});

//EVENTOS DE OCULTAR/MOSTRAR EN FORMULARIO
//Hermanos para el baremo
$('body').on('change', 'input[id*=num_hbaremo]', function(e){

var vid=$(this).attr("id");
vid=vid.replace('num_hbaremo','');

var bar_def=recalcular_baremo(vid);
$("#id_puntos_baremo"+vid).text(bar_def);

var val=$("#num_hbaremo"+vid).attr("value");
if(val=='0')
$("#num_hbaremo"+vid).attr('value','1');
else
$("#num_hbaremo"+vid).attr('value','0');

$(".hno_baremo"+vid).slideToggle('slow');
});

//CAMPOS TIPO CHECKBOX

$('body').on('change', '.nuevaesc', function(e){
var val=$(this).attr("value");
var id=$(this).attr("id");
var vid=id.replace('nuevaesc','');
var tabla=".fila"+id;
if(val=='0')
{	
	$(this).attr('value','1');
	$(".freserva"+vid).hide('slow');
}
else
{
		var centro=$("#id_centro_estudios_origen"+vid).val();
		if(centro.indexOf("*")!=-1) $(".freserva"+vid).show('slow');
		else $(".freserva"+vid).hide('slow');
		$(this).attr('value','0');
}
$(tabla).slideToggle('slow');
});

$('body').on('change', '.check_hadmision', function(e){
var val=$(this).attr("value");
var id=$(this).attr("id");
var tabla="#t"+id;
if(val=='0')
{	
	
	$(this).attr('value','1');
}
else
{
		$(this).attr('value','0');
}
$(tabla).slideToggle('slow');
});

$('body').on('change', '.oponenautorizar, .cumplen', function(e){
var val=$(this).attr("value");
var vcheck=$(this).attr("name");
var vid=$(this).attr("id");
vid=vid.replace('oponenautorizar','');
vid=vid.replace('cumplen','');
if(val=='0')
{
		$(this).attr('value','1');
		block(vcheck,vid,'1');
}
else
{
		block(vcheck,vid,'0');
		$(this).attr('value','0');
}

});


function block(c,id,n){
if(c.indexOf('oponenautorizar')!=-1)
{
if(n=='0') $("#cumplen"+id).attr('disabled', false);
else $("#cumplen"+id).attr('disabled', true);
}
else
{
if(n=='0') $("#oponenautorizar"+id).prop('disabled', false);
else $("#oponenautorizar"+id).prop('disabled', true);
}
}

/////////////////////////////////////////////////////////////////////////////////
//ACTUALIZAR - CREAR NUEVA SOLICITUD
$('body').on('click', '.send', function(e){
  var tipo=$(this).text();
  var vrol=$('#rol').attr("value");
  var vid_centro='';
  if(vrol=='alumno') 	
	vid_centro=$("input[name='id_centro_destino']").val();	
  else vid_centro=$('#id_centro').text();

  if(typeof tipo === 'undefined' || tipo === null) tipo="ACTUALIZAR SOLICITUD";

  var vid=$(this).attr("data-idal");
	var vptsbaremo=$("#id_puntos_baremo"+vid).text();
  var fsolicitud=$('#fsolicitud'+vid).serialize();
	//Validacion formulario, de momento se omite
	var valid='1';
	var valid=validarFormulario(fsolicitud,vid);
	var mensaje="Debes incluir un ";
	if(valid!='1')
	{
		mensaje=mensaje+valid.split('-')[0];
		if(valid.indexOf('fnac')==0)
		{
			if(campo_dnisol(fsolicitud)==0)
			{
				mensaje="Debes incluir un DNI del alumno por ser mayor de 14 años";
				$('input[name=dni_alumno]').focus();	
			}
		}
		$('input[name='+valid.split('-')[1]+']').focus();	
    		$.alert({
        		title: 'FORMULARIO INCOMPLETO',
        		content: mensaje
    			});
	return 0;
	}
	else
	{
	$.ajax({
	  method: "POST",
	  data: { fsol:fsolicitud,idsol:vid,modo:tipo,id_centro_destino:vid_centro,ptsbaremo:vptsbaremo,rol:vrol},
	  url:'../scripts/ajax/guardar_solicitud.php',
	 	success: function(data) {
		if(data.indexOf('10')!=-1)
		{
			$.alert({
				title: 'ERROR CREANDO SOLICITUD',
				content: data
				});
		return;
		} 
		if(data.indexOf('1062')!=-1) 
		{
			error='El dni del tutor ya existe';
			$('input[name=dni_tutor1]').focus();	
			$.alert({
				title: 'ERROR CREANDO SOLICITUD',
				content: error
				});
		}
		else if(data.indexOf('no_nombre_usuario')!=-1) 
		{
			error='debes introducir un nombre de usuario';
			$('input[name=dni_tutor1]').focus();	
			$.alert({
				title: 'ERROR CREANDO SOLICITUD',
				content: error
				});
		}
		else{
			if(tipo=='GRABAR SOLICITUD')
			{
			if(vrol.indexOf('alumno')!=-1)
			{ 
				$.alert({
					title: 'SOLICITUD GUARDADA CORRECTAMENTE, PARA MODIFICAR USA EL PIN: '+data,
					content: 'DE ACUERDO'
					});
			//añadimos boton para imprimir
			var bimp= $('<a href="imprimirsolicitud.php?id='+vid+'"><input class="btn btn-primary imprimirsolicitud" style="background-color:brown;padding-left:20px" type="button" value="Vista Previa Impresion Documento"/></a>');
			$('.send').text("ACTUALIZAR SOLICITUD");
			$('.send').after(bimp);
			return;
			}
			if(data.indexOf('ERROR')!=-1){ alert(data);return ;}
  		else {
						$('#sol_table').find('tbody').prepend(data);
							$.alert({
								title: 'SOLICITUD GUARDADA CORRECTAMENTE',
								content: 'DE ACUERDO'
								});
					}
		 				$('#fnuevasolicitud').remove();
			}
			else if(tipo=='ACTUALIZAR SOLICITUD')
			{
				if(vrol.indexOf('alumno')!=-1)
				{
				$.alert({
					title: 'SOLICITUD ACTUALIZADA',
					content: 'OK'
					});
				return;
				}
				$.alert({
					title: 'SOLICITUD ACTUALIZADA',
					content: 'OK'
					});
		 		$('#fsolicitud'+vid).hide();
		 		$('.show_solicitudes').click();
			}
			}
		},
			error: function (request, status, error) {
							alert(error);
					}
	});
	}
});

function campo_dnisol(str) {
	//determina si 
  var res = str.match(/&dni_alumno=.*&fnac/g);
  res=res[0].replace('&fnac','');
  res=res.replace('&dni_alumno=','');
	if(comprobar_nif(res.length)==0) return 0;
	else return 1;
}

function validarFormulario(fd,id)
{
var valido='1';
var res = fd.split("&");

var renta=0;
for (let i = 0; i < res.length; i++)
{
	d=res[i].split("=");
	if(d[0].indexOf('fnac')==0)
	{
	if(d[1]=='') {return 'Fecha nacimiento-fnac';};
	//comprobar edad alumno
	if(calcEdad(d[1])>=14) return 'fnac';
	if(calcEdad(d[1])<=2) return 'Edad mayor';
	}
//comp datos identificadores
if(d[0].indexOf('apellido1')==0)
	{
	//comprobar q el primer apellido existe
	if(d[1].length<=2) {return 'Primer Apellido-apellido1';};
	}
if(d[0].indexOf('nombre')==0)
	{
	if(d[1]=='') {return 'Nombre-nombre';};
	}
if(d[0].indexOf('dni_tutor1')==0)
	{
	if(comprobar_nif(d[1])==0) {return 'DNI TUTOR VÁLIDO-dni_tutor1';};
	}
//comp datos sección datos personales
if(d[0].indexOf('datos_tutor1')==0)
	{
	if(d[1]=='') {return 'Datos de tutor/a-datos_tutor1';};
	}
if(d[0].indexOf('baremo_proximidad_domicilio')==0)
	{
	console.log("validando dlaboral: "+id);
	var valor1=$("input[id='baremo_calle_dlaboral"+id+"']").val();
	var valor2=$("input[id='baremo_calle_dllimitrofe"+id+"']").val();
	if($("input[value='dlaboral']").is(':checked'))
	{
		console.log("CHECKED: "+valor1.length+" valor");
		if(valor1.length<=2) return "Valor para el domicilio laboral";
	}
	if($("input[value='dllimitrofe']").is(':checked'))
	{
		console.log("CHECKED: "+valor2.length+" valor");
		if(valor2.length<=2) return "Valor para el domicilio laboral en zona limitrofe";
	}
	}
//		if(d[1]=='') return "Valor para el domicilio laboral";
//	if($("input[value='dllimitrofe']").is(':checked')) 
//		if(d[1]=='') return "Valor para el domicilio laboral en zona limítrofe";
/*
if(d[0].indexOf('calle_dfamiliar')==0)
	{
	if(d[1]=='') {return 'Datos del domicilio familiar-calle_dfamiliar';};
	}
if(d[0].indexOf('num_dfamiliar')==0)
	{
	if(d[1]==''|| d[1].length>3) {return 'Numero del domicilio familiar no superior a 3 dígitos-num_dfamiliar';};
	}
if(d[0].indexOf('piso_dfamiliar')==0)
	{
	if(d[1]=='') {return 'Piso del domicilio familiar-piso_dfamiliar';};
	}
if(d[0].indexOf('cp_dfamiliar')==0)
	{
	if(d[1]==''|| d[1].length>5) {return 'CP del domicilio familiar debe tener 5 dígitos-cp_dfamiliar';};
	}
if(d[0].indexOf('loc_dfamiliar')==0)
	{
	if(d[1]=='') {return 'Debes indicar una localidad-loc_dfamiliar';};
	}
if(d[0].indexOf('tel_dfamiliar1')==0)
	{
	if(d[1]==''|| d[1].length!=9) {return 'Debes indicar un teléfono habitual correcto-tel_dfamiliar1';};
	}
*/
//comp datos sección expone

if(d[0]=='id_centro_destino')
	{
	if(d[1]=='') {return 'Debes indicar un centro de destino';}
	}
//si se marca la renta inferior debe indicarse datos de tributantes
if(d[0]=='baremo_renta_inferior')
	{
	if(d[1]=='1') {renta=1;}
	}
if(d[0]=='oponenautorizar' || d[0]=='cumplen')
	{
	if(d[1]=='1') {var botontrib=1;}
	}
if(d[0]=='tributantes_nombre1')
	{
	if(d[1]!='') {var ntrib1=1;}
	}
}
//Comprobamos que se haya completado la info tributaria
if(renta=='1')
	{
		if(botontrib=='0' || ntrib1=='0') return 'Debes completar la información de la renta';
	}
return valido;
};

//AÑADIR FORMULARIO DE MODIFICACION DE SOLICITUD
function disableForm(formID){
  $(formID).find(':input').attr('disabled', 'disabled');
}
$('body').on('click', '.calumno', function(e){
  var ots = $(this);
  var vmodo='normal';
  var vid=$(this).attr("data-idal");
  var idappend="filasol"+vid;
  var vestado=$('#id_estado_convocatoria').text();
  var vpin=$('#pin').attr("value");
  var vrol=$('#rol').attr("value");
  if($('#fsolicitud'+vid).length) 
  	{
	$('#fsolicitud'+vid).toggle();
	return;
	}
$.ajax({
  method: "POST",
  data: {id_alumno:vid,modo:vmodo,pin:vpin,rol:vrol},
  url:'../scripts/ajax/editar_solicitud.php',
   	success: function(data) 
	{
		if(vrol.indexOf("alumno")!=-1)
		{
			$("#l_matricula").after(data);
		}
	console.log(data);
      	$("#"+idappend).after(data);
	if(vestado=='3') {disableForm($('#fsolicitud'+vid)) ;}
      	},
      	error: function() {
        alert('PROBLEMAS EDITANDO SOLICITUD!');
      	}
});
});
//FIN AÑADIR FORMULARIO DE MODIFICACION DE SOLICITUD


//////////////////////////////////////////////////////////////
//AÑADIR FORMULARIO NUEVA SOLICITUD

$('body').on('click', '#nuevasolicitud', function(e)
{
var vcentro=$('#id_centro').text();
var vrol=$('#rol').attr("value");
var vmodo='nueva';
if($('#fnuevasolicitud').length) 
{
	$('#fnuevasolicitud').toggle();
	return;
}
	$.ajax({
	  method: "POST",
	  url: "../scripts/ajax/editar_solicitud.php",
	  data: {codigo_centro:vcentro,id_alumno:'0',modo:vmodo,rol:vrol},
	      success: function(data) {
				if(vrol.indexOf("alumno")!=-1)
					{
					$("#l_matricula").after(data);
					}
				else
				$("#cab_fnuevasolicitud").after(data);
	      },
	      error: function() {
		alert('Erorr en la llamada AJAX de solicitudes');
	      }
	});
});

//////////////////////////////////////////////////////////////
//LISTADOS GENERALES
//////////////////////////////////////////////////////////////

//LISTADO SOLICITUDES BRUTO
$(".show_solicitudes").click(function () {  
  var vid_centro=$('#id_centro').text();
  var vrol=$('#rol').attr("value");
var vestado_convocatoria=$('#estado_convocatoria').attr("value");
$.ajax({
  method: "POST",
  url: "../scripts/ajax/listados_solicitudes.php",
  data: {id_centro:vid_centro,rol:vrol,estado_convocatoria:vestado_convocatoria},
      success: function(data) {
				if(vrol=='admin' || vrol.indexOf('sp')!=-1)
				{
				$(".tresumensol").remove();
				$(".tresumenmat").remove();
				$("#l_matricula").html(data);
				}
				else
				{
				$("#l_matricula").html(data);
				$("#tresumen").hide();
				}
      },
      error: function() {
        alert('Erorr LISTADO solicitudes');
      }
});
});

//LISTADO SOLICITUDES GENERALES
$(".lgenerales").click(function () {  
  var vpdf='1';
  var vid_centro=$('#id_centro').text();
  var vrol=$('#rol').attr("value");
  var vtipo=$(this).attr("data-tipo");
  var vsubtipo=$(this).attr("data-subtipo");
$.ajax({
  method: "POST",
  url: "../scripts/ajax/listados_generales.php",
  data: {id_centro:vid_centro,rol:vrol,tipo:vtipo,subtipo:vsubtipo,pdf:vpdf},
      success: function(data) {

				if(vrol=='centro')
				{
				$("#l_matricula").html(data);
				$("#tresumen").hide();
				}
				else
				{
				$("#l_matricula").html(data);
				$(".container").hide();
				}
      },
      error: function() {
        alert('Error LISTANDO solicitudes: '+vsubtipo);
      }
});
});
//LISTADO PROVISIONALES
$(".lprovisionales").click(function () {  
  var vpdf='1';
  var vid_centro=$('#id_centro').text();
  var vrol=$('#rol').attr("value");
  var vtipo=$(this).attr("data-tipo");
  var vsubtipo=$(this).attr("data-subtipo");
  var vestado_convocatoria=$('#estado_convocatoria').text();
$.ajax({
  method: "POST",
  url: "../scripts/ajax/listados_provisionales.php",
  data: {id_centro:vid_centro,rol:vrol,tipo:vtipo,subtipo:vsubtipo,pdf:vpdf,estado_convocatoria:vestado_convocatoria},
      success: function(data) {

				if(vrol=='centro')
				{
				$("#l_matricula").html(data);
				$("#tresumen").hide();
				}
				else
				{
				$("#l_matricula").html(data);
				$(".container").hide();
				}
      },
      error: function() {
        alert('Error LISTANDO solicitudes: '+vsubtipo);
      }
});
});
//LISTADOS DEFINITIVOS
$(".ldefinitivos").click(function () {  
  var vpdf='1';
  var vid_centro=$('#id_centro').text();
  var vrol=$('#rol').attr("value");
  var vtipo=$(this).attr("data-tipo");
  var vsubtipo=$(this).attr("data-subtipo");
$.ajax({
  method: "POST",
  url: "../scripts/ajax/listados_definitivos.php",
  data: {id_centro:vid_centro,rol:vrol,tipo:vtipo,subtipo:vsubtipo,pdf:vpdf},
      success: function(data) {

				if(vrol=='centro')
				{
				$("#l_matricula").html(data);
				$("#tresumen").hide();
				}
				else
				{
				$("#l_matricula").html(data);
				$(".container").hide();
				}
      },
      error: function() {
        alert('Error LISTANDO solicitudes: '+vsubtipo);
      }
});
});

//LISTADO MATRICULA
$('body').on('click', '.show_matricula', function(e){
  var vid_centro=$('#id_centro').text();
  var vrol=$('#rol').attr("value");
  var vprovincia=$('#provincia').attr("value");
$.ajax({
  method: "POST",
  url: "../scripts/ajax/listados_matriculas.php",
  data: {id_centro:vid_centro,rol:vrol,provincia:vprovincia},
      success: function(data) 
			{
				if(vrol=='admin') 
				{
						$("#l_matricula").html(data);
				}
				else
				{
					//	$(".tresumenmat").hide();
						$("#tresumen"+vid_centro).show();
						$("#l_matricula").html(data);
				}
      },
      error: function() {
        alert('Erorr LISTADO matricula');
      }
});
});

//MOSTRAR ALUMNOS MATRICULADOS DE CADA CENTRO
$('body').on('click', '.cabcenmat', function(e){
  var vid_centro=$(this).attr('id');
  vid_centro=vid_centro.replace('cabcen','');
  var vrol=$('#rol').attr("value");
  if(vrol.indexOf('sp')!=-1) vrol='sp';
$.ajax({
  method: "POST",
  url: "../scripts/ajax/mostrar_matriculados.php",
  data: {id_centro:vid_centro,rol:vrol},
      success: function(data) 
			{
				if(vrol.indexOf('admin')!=-1 || vrol.indexOf('sp')!=-1)
				{
				console.log("en matricula");
				if($('#mat_table'+vid_centro).length) $('#mat_table'+vid_centro).toggle();
				else $('#table'+vid_centro).after(data).show('slow');
	
				}
				else
				{
				$("#tresumen").show();
				$("#l_matricula").html(data);
				}
      },
      error: function() {
        alert('Erorr LISTADO matricula');
      }
});
});
//MOSTRAR SOLICITUDES DE CADA CENTRO
$('body').on('click', '.cabcensol', function(e){
  var vid_centro=$(this).attr('id');
  vid_centro=vid_centro.replace('cabcensol','');
  var vrol=$('#rol').attr("value");
  var vprovincia=$('#provincia').attr("value");
$.ajax({
  method: "POST",
  url: "../scripts/ajax/mostrar_solicitudes.php",
  data: {id_centro:vid_centro,rol:vrol,provincia:vprovincia},
      success: function(data) 
			{
				if(vrol.indexOf('admin')!=-1 || vrol.indexOf('sp')!=-1)
				{
				if($('#sol_table').length) $('#sol_table').hide();
				$('#sol_table').remove();
				$('#table'+vid_centro).after(data);
	
				}
				else
				{
				$("#tresumen").show();
				$("#l_matricula").html(data);
				}
      },
      error: function() {
        alert('Erorr MOSTRANDO SOLICITUDES');
      }
});
});

/////////////////////////////////////////////////////////////
//GENERAR PDF
$("#pdff").click(function () {  
  var id=$(this).attr("id");
  var est=$('#'+id).text();
$.ajax({
  method: "POST",
  url: "../scripts/pdf/demo1.php",
  data: {estado:est},
      success: function(data) {
	$("#idpdf").remove();
	 var r= $('<br><a id="idpdf" href="scripts/pdf/out5.pdf" target="_blank"><input type="button" value="DESCARGA PDF"/></a>');
        $("body").append(r);
      },
      error: function() {
        alert('Erorr en la llamada AJAX');
      }
});
});


//CAMBIO ESTADO BOTONES
$('body').on('click', '.cambiar', function(e){
e.stopPropagation();
  //var vidcentro=$('#id_centro').text();
  //var vidcentro=$('.cabcenmat').attr("id");
  //vidcentro=vidcentro.replace('cabcen','');

  vidcentro=$(this).parent('td').parent('tr').parent('tbody').parent('table').attr('id');
  vidcentro=vidcentro.replace('mat_table','');
  var ots = $(this);
  var vid=$(this).attr("id");
  vid=vid.replace('cambiar','');
  var vcontinua=$("#estado"+vid).text();
  var vtipoalumno=$('#tipoalumno'+vid).text();
  var vestado_pulsado=$(this).text();
  var vestado_actual=$(this).parent('div').parent('div').attr("id");
$.ajax({
  method: "POST",
  data: { id_alumno:vid,estado_pulsado:vestado_pulsado,estado_actual:vestado_actual,id_centro:vidcentro,continua:vcontinua},
  url:'../scripts/ajax/cambio_estado_solicitud.php',
      success: function(data) 
      {
	if(vcontinua.indexOf('NO')!=-1){ alert("El alumno no continua, no afecta plazas vacantes");return;}
	cambiar_tipo(ots,vestado_pulsado,vid);
	var vacantes_ebo =data.split(":")[0];
	var vacantes_tva =data.split(":")[1];
       	$('#vacantesmat_ebo_desk'+vidcentro).html(vacantes_ebo);
   	$('#vacantesmat_tva_desk'+vidcentro).html(vacantes_tva);
   	var  npo_ebo=$('#vacantesmat_ebo_desk'+vidcentro).prev().text();
   	var  npo_tva=$('#vacantesmat_tva_desk'+vidcentro).prev().text();
	if(vestado_pulsado.indexOf('EBO')!=-1)
	{
		console.log("pulsado ebo");
		console.log(ots.parent('td').parent('tr').parent('tbody').parent('table').attr('id'));
		//$(this).closest('#vacantesmat_ebo_desk').prev().html(+npo_ebo+1);
		$('#vacantesmat_ebo_desk'+vidcentro).prev().html(+npo_ebo+1);
		$('#vacantesmat_tva_desk'+vidcentro).prev().html(+npo_tva-1);
		//$('#vacantesmat_tva_desk').prev().html(+npo_tva-1);
	}
	if(vestado_pulsado.indexOf('TVA')!=-1)
	{
		$('#vacantesmat_ebo_desk'+vidcentro).prev().html(+npo_ebo-1);
	   	$('#vacantesmat_tva_desk'+vidcentro).prev().html(+npo_tva+1);
	}
      },
      error: function() {
        alert('Problemas cambiando de estado!');
      }
});
});

function cambiar_tipo(t,est,id) {
	est=est.replace('CAMBIA A ','');
  $("#tipoalumno"+id).html(est);
		if(est=='EBO')
		$("#cambiar"+id).html("CAMBIA A TVA");
		else
		$("#cambiar"+id).html("CAMBIA A EBO");
}
$('body').on('click', '.continua', function(e){
  var ots = $(this);
  var id=$(this).attr("id");
  var est=$('#'+id).text();
	id=id.replace('estado','');
	id=id.replace('continua','');
	id=id.replace('cambiar','');
  var vidcentro=$(this).parent('td').parent('tr').parent('tbody').parent('table').attr('id');
  vidcentro=vidcentro.replace('mat_table','');
  var vtipoalumno=$('#tipoalumno'+id).text();
$.ajax({
  method: "POST",
  data: { id_alumno:id,estado:est,tipoalumno:vtipoalumno,id_centro:vidcentro},
  url:'../scripts/ajax/cambio_estado_continua.php',
      success: function(data) 
			{
	 if(data.indexOf('error')!=-1){ alert("No hay plazas vacantes");return;}
	 var vacantes_ebo =data.split(":")[0];
	 var vacantes_tva =data.split(":")[1];
	  //cambiarboton(ots);
	  cambiarestado(id,est);
		console.log(vidcentro);
   	  console.log($('#vacantesmat_ebo_desk'+vidcentro).html());
	  vtipoalumno=vtipoalumno.toLowerCase();
   	  $('#vacantesmat_ebo_desk'+vidcentro).html(vacantes_ebo);
   	  $('#vacantesmat_tva_desk'+vidcentro).html(vacantes_tva);
   	  var  numpzasocupadas=$('#vacantesmat_'+vtipoalumno+'_desk'+vidcentro).prev().text();
   	  var  numpuestos=$('#vacantesmat_'+vtipoalumno+'_desk'+vidcentro).prev().prev().text();
			//modificamos tabla vacantes
   	  if(est=='NO CONTINUA')
	  {
   	  	if(+numpzasocupadas-1>=0) $('#vacantesmat_'+vtipoalumno+'_desk'+vidcentro).prev().html(+numpzasocupadas-1);
   	  }
			//else if(+numpzasocupadas+1<=numpuestos) $('#vacantesmat_'+vtipoalumno+'_desk').prev().html(+numpzasocupadas+1);
			else  $('#vacantesmat_'+vtipoalumno+'_desk'+vidcentro).prev().html(+numpzasocupadas+1);
      },
      error: function() 
			{
        alert('Error cambiando estado!');
      }
});
});

function cambiarestado(id,est) 
{
	$("#estado"+id).html(est);
	if(est=='CONTINUA' || est=='NO CONTINUA')	
	{
		if(est=='NO CONTINUA')
		$("#continua"+id).html("CONTINUA");
		else
		$("#continua"+id).html("NO CONTINUA");
	
	}
	else
	{
		if(est=='CAMBIA A EBO')
		$("#cambiar"+id).html("CAMBIA A TVA");
		else
		$("#cambiar"+id).html("CAMBIA A EBO");
	
	}
}

function cambiarboton(t) {
       if(t.hasClass("btn-warning"))
       {
       		$(t).addClass("btn-danger");
       		$(t).removeClass("btn-warning");
       }
       else{
       		$(t).addClass("btn-warning");
       		$(t).removeClass("btn-danger");
       }
       $(t).text(function(i, v){
		   return v === 'CONTINUA' ? 'NO CONTINUA' : 'CONTINUA'
		});
    };

//OCULTAR PANEL LATERAL

$('#sidebarCollapse').on('click', function () 
{
                $('#sidebar, #content').toggleClass('active');
                $('.collapse.in').toggleClass('in');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
});



///////////////////////////////////////////////////////////////////////////
//FORMULARUIO SOLICITUD
// INICIO TOOLTIP
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

// Initialize popover component
$(function () {
  $('[data-toggle="popover"]').popover()
})


//AUTOCOMPLETAR

var loc_options = 
	{
	url: "../datosweb/localidades.json",
	getValue: "name",
		list: 
		{
			maxNumberOfElements: 10,
			match: 
			{
			enabled: true
			},
			onKeyEnterEvent: function() 
			{
			var vcentro = $('#localidad').getSelectedItemData().name;
			},
			onClickEvent: function() 
			{
			var vcentro = $('#localidad').getSelectedItemData().name;
			}
		}
	};
$("#localidad").easyAutocomplete(loc_options);
$("#localidad_origen").easyAutocomplete(loc_options);
$('input[id*=loc_dfamiliar]').easyAutocomplete(loc_options);


var cen_options = 
	{
	url: "../datosweb/centros_especial.json",
	getValue:"nombre_centro",
		list: 
		{
			onSelectItemEvent: function() {
			var nombre = $("#fcentrosadmin").getSelectedItemData().nombre_centro;
			var idcentro = $("#fcentrosadmin").getSelectedItemData().id_centro;
			//$("#tresumen").children("h2").text(nombre);
			$("#id_centro").text(idcentro);
			$("#rol").text('centro');
			},
			maxNumberOfElements: 10,
			match: 
			{
			enabled: true
			},
			onKeyEnterEvent: function() 
			{
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
			},
			onClickEvent: function() 
			{
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
			}
		}
	};
$("#buscar_centros").easyAutocomplete(cen_options);
$("#fcentrosadmin").easyAutocomplete(cen_options);
$("#admin_centros").easyAutocomplete(cen_options);

var nac_options = 
	{
	url: "../datosweb/nacionalidades.json",
	getValue: "gentilico",
		list: 
		{
			maxNumberOfElements: 10,
			match: 
			{
			enabled: true
			},
			onKeyEnterEvent: function() 
			{
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
			},
			onClickEvent: function() 
			{
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
			}
		}
	};
$("#nacionalidad").easyAutocomplete(nac_options);

var cen_estudios_options = 
	{
	url: "../datosweb/centros_general.json",
	getValue: "centros",
		list: 
		{
			maxNumberOfElements: 20,
			match: 
			{
			enabled: true
			},
			onKeyEnterEvent: function() 
			{
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
			},
			onClickEvent: function() 
			{
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
			}
		}
	};
$("#cen_estudios_origen").easyAutocomplete(cen_estudios_options);


//////////////////////////////////////////////
//DATOS TRIBUTARIOS
$('body').on('change', 'input[type=checkbox][id*=oponenautorizar]', function(e){




});

//////////////////////////////////////////////
//FUNCIONES DE EXPORTACION DATOS EXCEL CSV

$('body').on('click', '.exportcsv', function(e)
{
  var vrol=$('#rol').attr("value");
var vid=$(this).attr("id");
var vidcentro=$('#id_centro').text();
var vsubtipo=$(this).attr("data-subtipo");
	$.ajax({
	method: "POST",
	data: {id_centro:vidcentro,tipolistado:vsubtipo,rol:vrol},
	url:'../scripts/ajax/gen_csvs.php',
	success: function(data) {
			window.open(data,'_blank');
	},
	error: function() {
	alert('Problemas imprimiendo solicitud!');
	}
	});

});
//FUNCIONES DE EXPORTACION DATOS PDF

$('body').on('click', '.exportpdf', function(e)
{
  	var vrol=$('#rol').attr("value");
	var vid=$(this).attr("id");
	var vidcentro=$('#id_centro').text();
  var vsubtipo=$(this).attr("data-subtipo");
	$.ajax({
	  method: "POST",
	  data: {id_centro:vidcentro,tipolistado:vsubtipo,rol:vrol},
	  url:'../scripts/ajax/gen_pdfs.php',
	      success: function(data) {
				window.open(data,'_blank');
		},
	      error: function() {
		alert('Problemas generando pdf');
	      }
	});

});

//////////////////////////////////////////////
//FUNCIONES DE IMPRESION

$('body').on('click', '.printsol', function(e){
var vid=$(this).attr("id");
vid=vid.replace("print",'');
	$.ajax({
	  method: "POST",
	  data: {id_alumno:vid},
	  url:'../scripts/ajax/print_solicitud.php',
	      success: function(data) {
				window.open('scripts/datos/'+data,'_blank');
		},
	      error: function() {
		alert('Problemas imprimiendo solicitud!');
	      }
	});

});

//////////////////////////////////////////////
//FUCNIONES DE AYUDA
function calcEdad(dateString) {
  var birthday = +new Date(dateString);
  return ~~((Date.now() - birthday) / (31557600000));
}
});

function comprobar_nif(dni){
       var numero;
       var let;
       var letra;
       var expresion_regular_dni;
      	//de momento marcamos q tenga 9 caracteres por incluir el nie
      	if(dni.length==9) return 1;
	else return 0; 
       expresion_regular_dni = /^\d{8}[a-zA-Z]$/;
       
       if(expresion_regular_dni.test (dni) == true){
          numero = dni.substr(0,dni.length-1);
          let = dni.substr(dni.length-1,1);
          let=let.toUpperCase();
          numero = numero % 23;
          letra='TRWAGMYFPDXBNJZSQVHLCKET';
          letra=letra.substring(numero,numero+1);
          if (letra!=let) {
						return 0;
          }else{
            return 1;
          }
       }else{
          return 0;
       }
       return 0;
}
