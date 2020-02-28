<?php
    // Pear Mail Library
    require_once "Mail.php";
    $from = '<***@motelavigna.co>'; //change this to your email address
    $to = '<***@gmail.com>'; // change to address
    $subject = 'Insert subject here'; // subject of mail
    $body = "Hello world! this is the content of the email"; //content of mail

    $headers = array(
        'From' => $from,
        'To' => $to,
        'Subject' => $subject
    );

    $smtp = Mail::factory('smtp', array(
            'host' => 'auth.smtp.1and1.fr',
            'port' => '465',
            'auth' => true,
            'username' => '***@***.co', //co is not an error
            'password' => '***' // your password
        ));

    // Send the mail
    $mail = $smtp->send($to, $headers, $body);
?>
