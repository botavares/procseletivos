<?php

namespace App\Helpers;

use CodeIgniter\Config\Services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    protected $email;

    public function __construct()
    {
       
    }

    public static function sendEmail($dados){
       	//Import PHPMailer classes into the global namespace
	//These must be at the top of your script, not inside a function


	//Load Composer's autoloader
	require 'vendor/autoload.php';

	//Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);

	try {
		//Server settings
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = 'smtp-relay.brevo.com';                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->Username   = '9f05aa001@smtp-brevo.com';                     //SMTP username
		$mail->Password   = 'xsmtpsib-6f618d2cb0e870067cc7c47669cdfe9600f2bb1f1db1ed7bb8254b95ec8cfeb4-HZ9as3kEQgyH4QBQ';                               //SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
		$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		//Recipients
		$mail->setFrom('pmdivinopolis.contatos@gmail.com', 'Prefeitura Municipal de Divinópolis');
		$mail->addAddress($dados['email'],$dados['nome']);     //Add a recipient
		//$mail->addReplyTo('info@example.com', 'Information');
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');

		//Attachments
		//$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

		//Content
        $mail->CharSet = "UTF-8";
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = $dados['assunto'];
		$mail->Body    = $dados['mensagem'];
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		$mail->send();
		//echo 'Message has been sent';
	} catch (Exception $e) {
		var_dump($mail->ErrorInfo.'email:'.$dados['email']);
		die();
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
}
}
