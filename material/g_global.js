$(document).ready(function() 
{
$( "#datacentro" ).tooltip();
 	var dir_encuesta='datos_junio19';	
	var periodo='6m';	
	var centro = $('#idcentro').attr('value');
        get_datos_global(centro,periodo,dir_encuesta);
	var options = 
	{
	url: "../js/datos/centros.json",
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
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
        		get_datos_global(vcentro,periodo,dir_encuesta);
			},
			onClickEvent: function() 
			{
			var vcentro = $('#buscar_centros').getSelectedItemData().name;
        		get_datos_global(vcentro,periodo,dir_encuesta);
			}
		}
	};
	$("#buscar_centros").easyAutocomplete(options);
	
	$("#12m").click(function()
	{
		if($('#buscar_centros').length!=0) centro = $('#buscar_centros').val();
		if(centro.length==0) centro='global';
		periodo='12m';
		get_datos_global(centro,periodo,dir_encuesta);
	});
	
	$("#6m").click(function()
	{
		if($('#buscar_centros').length!=0) centro = $('#buscar_centros').val();
		if(centro.length==0) centro='global';
		periodo='6m';
		get_datos_global(centro,periodo,dir_encuesta);
	});
});

function get_datos_global(centro,periodo,dir_encuesta){
                 $.ajax({
                           type: "POST",
                           url: "../php/get_datos_global.php",
			   data : {cen:centro,per:periodo},
                           cache: false,    
                           success: function(result){
                                    var res = result.split(',');
                                    show_datos_global(res); 
                                    show_grafico_insercion(centro,'trabaja_desempleo','trabaja_desempleo','% INSERCION EN GENERAL',periodo,dir_encuesta);  
                                    show_grafico_insercion(centro,'trabaja_relacionado','trabaja_relacionado','% INSERCION LABORAL EN SECTOR RELACIONADO',periodo,dir_encuesta);  
                                    show_grafico_insercion(centro,'trabaja_por_familias','trabaja_por_familias','% INSERCION LABORAL POR FAMILIAS',periodo,dir_encuesta);  
                                    show_grafico_insercion(centro,'trabaja_relacionado_por_familias','trabaja_relacionado_por_familias','% INSERCION LABORAL RELACIONADO POR FAMILIAS',periodo,dir_encuesta);  
                                    if(centro=='global'){
				    show_grafico_insercion(centro,'trabaja_por_centros','trabaja_por_centros','% INSERCION LABORAL POR CENTROS',periodo,dir_encuesta);  
                                    show_grafico_insercion(centro,'trabaja_relacionado_por_centros','trabaja_relacionado_por_centros','% INSERCION LABORAL RELACIONADO POR CENTROS',periodo,dir_encuesta);  
					}
                                return result;
                               },
                      error: function(xhr,status,error) {
                                   console.log("nook leyendo"); 
				console.log("(" + xhr.responseText + ")");
			}
                        });
            }
function show_grafico_insercion(centro,selector,fichero,titulo,periodo='',dir_encuesta)
	{
		if(centro=='global') file='../'+dir_encuesta+'/globales/'+periodo+'/'+fichero+'.csv';
		else file='../'+dir_encuesta+'/centros/'+periodo+'/'+centro+'/'+fichero+'.csv';
		console.log(dir_encuesta);
		console.log(file);
		$.get(file)
		.done(function(csv) 
		{
		file = file.substring(0, file.length-4);
		$('#'+selector).show();
        	  $('#'+selector).highcharts({
                    credits:{
			        enabled: false
    			    },
			        chart: {
			        	type: 'column'
			               },
			        data: {
			            csv: csv
			        },
			        title: {
						text: titulo
					},
					yAxis: {
						title: {
							text: 'Porcentaje de alumnos'
						}
					}
			    });
            
            
            
        })
  	.fail(function(j,t,e) 
	{
		console.log("no get")
		$('#'+selector).hide();
		return ;
  	})
  	.always(function() {
        })
	}

        function show_datos_global(data)
        {
		var j=0;
		var k=1;
		$('.dg').each(function(i,o){
			$(this).empty();
			$(this).append(data[j]);
			j=j+2;
			});

		$('.dgp').each(function(i,o){
			$(this).empty();
			$(this).append(data[k]);
			k=k+2;
			});
	   }
                
                  
