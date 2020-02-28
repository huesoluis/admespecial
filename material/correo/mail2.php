<?php
require  '../../../php/configuracion.php';
require  '../../../php/parametros.php';
require  '../../../php/db.php';
function enviar_correo($m,$from,$subject,$body,$smtp)
{
		$to=$m;
		$headers = array
			(
        		'From' => $from,
		        'To' => $to,
		        'Subject' => $subject,
			'Content-Type'=>'text/html'
   			 );
		print("\n No respondido: ".$to);
		$mail=1;
		$mail = $smtp->send($to, $headers, $body);
		if($mail) return 1;
		else return 0;
}
$helper=new DataBase($mysqli,DB_NAME);
    // Pear Mail Library
    require_once "Mail.php";
  $from = '<serviciofparagon@gmail.com>'; //change this to your email address
  $subject = 'Encuesta Inserción FP'; // subject of mail
  $msginicial = "Hola, te escribe Luis Hueso, asesor del Servicio de Formación Profesional del Gobierno de Aragón.<br> 
Como ya probablemente sabrás, cada 6 y 12 meses meses recabamos datos de la inserción laboral de nuestros alumnos.<br> 
Estos datos son muy útlies para mejorar la formación que se imparte en los centros públicos de FP, asi que te pedimos que nos des <b>UN MINUTO</b> de tu tiempo para completar la siguiente encuesta de <b>4 preguntas</b>.<br>
Para ello accede a esta web con las credenciales que usas en la aplicacicón de FCTs (normalmente tu dni, con letra, como usario y clave):<br>
<a href='http://insercionfp.aragon.es'>ACCESO ENCUESTA</a><br>
Para cualquier cuestión o inciendica no dudes en llamar o escribir (lhueso@aragon.es 976715444)<br>
Un saludo y gracias de antemano por tu colaboración"; 
  $recordatorio = "Hola, quedan pocos días para finalizar la encuesta de inserción laboral en FP que te llevará solamente<b> 1 minuto</b>
Esta información es muy útil para nosotros, anímate y participa!<br>
Para cualquier cuestión o inciendica no dudes en llamar o escribir (lhueso@aragon.es 976715444)<br>
Un saludo y gracias de antemano por tu colaboración"; 

$body=utf8_decode($recordatorio);
$smtp = Mail::factory('smtp', array(
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'auth' => true,
            'username' => 'serviciofparagon@gmail.com', //co is not an error
            'password' => 'Sfp$2016' // your password
        ));
$nc=0;
$fichero='alumnos_jun18_12meses.csv';
$tipoenvio='basedatos';
$ncorreos=0;
if($tipoenvio=='basedatos')	
{
	$r=$helper->get_correoalumnos();
	foreach($r as $k=>$correo)
		{
		$m=$correo['email'];
		if(strlen($m)==0) continue;
		$respondido=$helper->check_respondido($m);
		if($respondido==1){ print("\nrespondido: ".$m);continue;}
		if(enviar_correo($m,$from,$subject,$body,$smtp))
		{ 
			echo "\nCorreo correctamente enviado a ".$m."\n";
			$ncorreos++;
		}
		else echo "correo fallido";
		sleep(1);
		}
}
else
{
$fn = fopen($fichero,"r");
while(!feof($fn))
	{
	$linea = fgets($fn);
	$correo=explode(";",$linea);
	if(array_key_exists('13',$correo))
	{	
		if(strlen($correo['13'])!=0 and strpos($correo['13'], '@') !== false)
		{
		$to=$correo[13];
		$headers = array
			(
        		'From' => $from,
		        'To' => $to,
		        'Subject' => $subject,
			'Content-Type'=>'text/html'
   			 );
		#$mail = $smtp->send($to, $headers, $body);
		if($mail) echo "\nCorreo correctamente enviado a ".$to." ".$correo['15']."\n";
		$nc++;
		sleep(1);
		}
		else
			echo "\nCorreo no enviado a  ".$correo['15']."\n";
	}
  	}
  fclose($fn);
}
print("TOTAL CORREOS ENVIADOS: ".$ncorreos);

?>
