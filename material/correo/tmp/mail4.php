<?php

// If necessary, modify the path in the require statement below to refer to the 
// location of your Composer autoload.php file.
require './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

define('GUSER', 'huesoluis@gmail.com'); // GMail username
define('GPWD', 'pelavaras'); // GMail password

function smtpmailer($to, $from, $from_name, $subject, $body) { 
 global $error;
 $mail = new PHPMailer();  // create a new object
 $mail->IsSMTP(); // enable SMTP
 $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
 $mail->SMTPAuth = true;  // authentication enabled
 $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
 $mail->Host = 'smtp.gmail.com';
 $mail->Port = 587; 
 $mail->Username = GUSER;  
 $mail->Password = GPWD;           
 $mail->SetFrom($from, $from_name);
 $mail->Subject = $subject;
 $mail->Body = $body;
 $mail->AddAddress($to);
 if(!$mail->Send()) {
 $error = 'Mail error: '.$mail->ErrorInfo; 
 return false;
 } else {
 $error = 'Message sent!';
 return true;
 }
}

if(smtpmailer('obabakoak@gmail.com', 'huesoluis@gmail.com', 'yourName', 'test mail message', 'Hello World!'))
echo "ok";
elseif (!empty($error)) echo $error;



?>
