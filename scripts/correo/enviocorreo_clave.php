<?php
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
		$mail=1;
		$mail = $smtp->send($to, $headers, $body);
		print($mail);
		if($mail) return 1;
		else return 0;
}
    // Pear Mail Library
    require_once "Mail.php";
  $from = '<obabakoak@gmail.com>'; //change this to your email address
  $subject = 'Encuesta Inserción FP'; // subject of mail
  $body = "Hola, te escribe Luis Hueso, asesor del Servicio de Formación Profesional del Gobierno de Aragón.<br> 
Como ya probablemente sabrás, cada 6 y 12 meses meses recabamos datos de la inserción laboral de nuestros alumnos.<br> 
Estos datos son muy útlies para mejorar la formación que se imparte en los centros públicos de FP, asi que te pedimos que nos des <b>UN MINUTO</b> de tu tiempo para completar la siguiente encuesta de <b>4 preguntas</b>.<br>
Para ello accede a esta web con las credenciales que usas en la aplicacicón de FCTs (normalmente tu dni, con letra, como usario y clave):<br>
<a href='http://insercionfp.aragon.es'>ACCESO ENCUESTA</a><br>
Para cualquier cuestión o inciendica no dudes en llamar o escribir (lhueso@aragon.es 976715444)<br>
Un saludo y gracias de antemano por tu colaboración"; 

$body=utf8_decode($body);
$smtp = Mail::factory('smtp', array(
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'auth' => true,
            'username' => 'obabakoak@gmail.com', //co is not an error
            'password' => 'Suricato1.fp' // your password
            #'username' => 'serviciofparagon@gmail.com', //co is not an error
            #'password' => 'Sfp$2016' // your password
        ));
$nc=0;
$m='huesoluis@gmail.com';
if(enviar_correo($m,$from,$subject,$body,$smtp)) print("TOTAL CORREOS ENVIADOS: ");

?>
