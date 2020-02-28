<?php
$script="<script>
var centro_estudios_options = 
	{
	url: '../datosweb/centros_general.json',
	getValue: 'centros',
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
			},			
      onSelectItemEvent: function() 
			{
				var centro=$('input[id*=id_centro_estudios_origen]').getSelectedItemData().centros;
				if(centro.indexOf('*')!=-1){ $('div[class*=freserva]').show('slow');}
				else { $('div[class*=freserva]').hide('slow');}

    	}
		}  
	};
$('input[id*=id_centro_estudios_origen').easyAutocomplete(centro_estudios_options);


var cen_options = 
	{
	url: '../datosweb/centros_especial.json',
	getValue: 'nombre_centro',
		list: 
		{
			maxNumberOfElements: 30,
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
$('#buscar_centros').easyAutocomplete(cen_options);
$('input[id*=id_centro_destino]').easyAutocomplete(cen_options);

var loc_options = 
	{
	url: '../datosweb/localidades.json',
	getValue: 'name',
		list: 
		{
			maxNumberOfElements: 10,
			match: 
			{
			enabled: true
			}
		}
	};
$('.localidad').easyAutocomplete(loc_options);
$('.loc').easyAutocomplete(loc_options);
$('#localidad_origen').easyAutocomplete(loc_options);
$('input[id*=loc_dfamiliar]').easyAutocomplete(loc_options);
$('input[id*=loc_centro_origen]').easyAutocomplete(loc_options);
$('input[id*=loc_centro_destino]').easyAutocomplete(loc_options);

var nac_options = 
	{
	url: '../datosweb/nacionalidades.json',
	getValue: 'nome_pais_int',
		list: 
		{
			maxNumberOfElements: 10,
			match: 
			{
			enabled: true
			}
		}
	};
$('.nacionalidad').easyAutocomplete(nac_options);
</script>";
?>
