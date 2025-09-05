<?php

namespace App\Controllers;

// require_once "app/Models/ConfiguracionCorreoElectronico.php";
if ( file_exists ( "app/Models/ConfiguracionCorreoElectronico.php" ) ) {
    require_once "app/Models/ConfiguracionCorreoElectronico.php";
} else {
    require_once "../Models/ConfiguracionCorreoElectronico.php";
}

if ( file_exists ( "app/Models/Mensaje.php" ) ) {
    require_once "app/Models/Mensaje.php";
} else {
    require_once "../Models/Mensaje.php";
}

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;

// Load Composer's autoloader
// require 'vendor/autoload.php';
if ( file_exists ( "vendor/autoload.php" ) ) {
    require_once "vendor/autoload.php";
} else {
    require_once "../../vendor/autoload.php";
}

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailController
{
    /*=============================================
    ENVIAR CORREO ELECTRONICO
    =============================================*/
	static public function send(Mensaje $mensaje, $debug = false) :array
    {
    	$configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

    	if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

			//Create an instance; passing `true` enables exceptions
			$mail = new PHPMailer(true);
			// $mail->CharSet = "UTF-8";
			$mail->CharSet = PHPMailer::CHARSET_UTF8;

			try {

				if ( $debug ) $mail->SMTPDebug = SMTP::DEBUG_SERVER;
			    $mail->isSMTP();
				// $mail->Timeout = 10;
	            $mail->Host = $configuracionCorreoElectronico->servidor;
	            $mail->SMTPAuth = true;
	            $mail->Username = $configuracionCorreoElectronico->usuario;
	            $mail->Password = $configuracionCorreoElectronico->contrasena;
	            if ( $configuracionCorreoElectronico->puertoSSL ) $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	            $mail->Port = $configuracionCorreoElectronico->puerto;

	            // $mail->isMail();

	            $mail->setFrom($configuracionCorreoElectronico->visualizacionCorreo, $configuracionCorreoElectronico->visualizacionNombre);
	            // $mail->addAddress($configuracionCorreoElectronico->comprobacionCorreo, 'Test User');
	            foreach ($mensaje->destinatarios as $key => $value) {
	            	$correo = mb_strtolower($value["correo"]);
	            	$nombreCompleto = mb_strtoupper($value["usuarios.nombreCompleto"]);

	            	$mail->addAddress($correo, $nombreCompleto);
	            }

	            if ( !is_null($configuracionCorreoElectronico->respuestaCorreo) ) {

	            	if ( is_null($configuracionCorreoElectronico->respuestaNombre) ) {
	            		$mail->addReplyTo($configuracionCorreoElectronico->respuestaCorreo);
	            	} else {
	            		$mail->addReplyTo($configuracionCorreoElectronico->respuestaCorreo, $configuracionCorreoElectronico->respuestaNombre);
	            	}

	            }
	            // $mail->addCC('cesarmtassinari@hotmail.com');

	            $mail->isHTML(true);
	            $mail->Subject = $mensaje->asunto;
	            $mail->Body = $mensaje->mensajeHTML;
	            $mail->AltBody = $mensaje->mensaje;

	            $mail->send();

			    $respuesta = [
    				'codigo' => 204,
    				'error' => false
    			];

			} catch (\Exception $e) {
			    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

			    $respuesta = [
    				'codigo' => 500,
    				'error' => true,
    				'errorMessage' => "{$mail->ErrorInfo}"
    			];
			}

    	} else {

			$respuesta = [
    			'codigo' => 500,
    			'error' => true,
    			'errorMessage' => 'No se lograron obtener las credenciales'
    		];

    	}

    	return $respuesta;
    }

	/*=============================================
    ENVIAR CORREO ELECTRONICO PROVEEDOR
    =============================================*/
	static public function sendProveedor(Mensaje $mensaje, $debug = false,$correo) :array
    {
    	$configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

    	if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

			//Create an instance; passing `true` enables exceptions
			$mail = new PHPMailer(true);
			// $mail->CharSet = "UTF-8";
			$mail->CharSet = PHPMailer::CHARSET_UTF8;

			try {

				if ( $debug ) $mail->SMTPDebug = SMTP::DEBUG_SERVER;
			    $mail->isSMTP();
				// $mail->Timeout = 10;
	            $mail->Host = $configuracionCorreoElectronico->servidor;
	            $mail->SMTPAuth = true;
	            $mail->Username = $configuracionCorreoElectronico->usuario;
	            $mail->Password = $configuracionCorreoElectronico->contrasena;
	            if ( $configuracionCorreoElectronico->puertoSSL ) $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	            $mail->Port = $configuracionCorreoElectronico->puerto;

	            // $mail->isMail();

	            $mail->setFrom($configuracionCorreoElectronico->visualizacionCorreo, $configuracionCorreoElectronico->visualizacionNombre);
	            // $mail->addAddress($configuracionCorreoElectronico->comprobacionCorreo, 'Test User');

				$mail->addAddress($correo, 'Proveedor');

	            if ( !is_null($configuracionCorreoElectronico->respuestaCorreo) ) {

	            	if ( is_null($configuracionCorreoElectronico->respuestaNombre) ) {
	            		$mail->addReplyTo($configuracionCorreoElectronico->respuestaCorreo);
	            	} else {
	            		$mail->addReplyTo($configuracionCorreoElectronico->respuestaCorreo, $configuracionCorreoElectronico->respuestaNombre);
	            	}

	            }
	            // $mail->addCC('cesarmtassinari@hotmail.com');

	            $mail->isHTML(true);
	            $mail->Subject = $mensaje->asunto;
	            $mail->Body = $mensaje->mensajeHTML;
	            $mail->AltBody = $mensaje->mensaje;

	            $mail->send();

			    $respuesta = [
    				'codigo' => 204,
    				'error' => false
    			];

			} catch (\Exception $e) {
			    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

			    $respuesta = [
    				'codigo' => 500,
    				'error' => true,
    				'errorMessage' => "{$mail->ErrorInfo}"
    			];
			}

    	} else {

			$respuesta = [
    			'codigo' => 500,
    			'error' => true,
    			'errorMessage' => 'No se lograron obtener las credenciales'
    		];

    	}

    	return $respuesta;
    }

    /*=============================================
    ENVIAR CORREO ELECTRONICO (TEST)
    =============================================*/
    static public function test($debug = false) :array
    {
    	$configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

    	if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

			//Create an instance; passing `true` enables exceptions
			$mail = new PHPMailer(true);
			// $mail->CharSet = "UTF-8";
			$mail->CharSet = PHPMailer::CHARSET_UTF8;

			try {

			   	// Server settings
			    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                // Enable verbose debug output
			    // $mail->isSMTP();                                      // Send using SMTP
			    // $mail->Host       = 'sandbox.smtp.mailtrap.io';       // Set the SMTP server to send through
			    // $mail->SMTPAuth   = true;                             // Enable SMTP authentication
			    // $mail->Username   = '2361cc715512d2';                 // SMTP username
			    // $mail->Password   = '35b287044460f7';                 // SMTP password
			    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      // Enable implicit TLS encryption
			    // $mail->Port       = 2525;                             // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			    // Recipients
			    // $mail->setFrom('from@example.com', 'Mailer');
			    // $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
			    // $mail->addAddress('ellen@example.com');               // Name is optional
			    // $mail->addReplyTo('info@example.com', 'Information');
			    // $mail->addCC('cc@example.com');
			    // $mail->addBCC('bcc@example.com');

			    // Attachments
			    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			    // Content
			    // $mail->isHTML(true);                                  // Set email format to HTML
			    // $mail->Subject = 'Here is the subject';
			    // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			    // $mail->send();
			    // echo 'Message has been sent';

				// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
				if ( $debug ) $mail->SMTPDebug = SMTP::DEBUG_SERVER;
			    $mail->isSMTP();
				// $mail->Timeout = 10;
	            $mail->Host = $configuracionCorreoElectronico->servidor;
	            $mail->SMTPAuth = true;
	            $mail->Username = $configuracionCorreoElectronico->usuario;
	            $mail->Password =$configuracionCorreoElectronico->contrasena;
	            if ( $configuracionCorreoElectronico->puertoSSL ) $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	            $mail->Port = $configuracionCorreoElectronico->puerto;

	            // $mail->isMail();

	            $mail->setFrom($configuracionCorreoElectronico->visualizacionCorreo, $configuracionCorreoElectronico->visualizacionNombre);
	            // $mail->addAddress('cesarmtassinari@hotmail.com', 'Joe User');
	            $mail->addAddress($configuracionCorreoElectronico->comprobacionCorreo, 'Test User');
	            // $mail->addReplyTo('info@example.com', 'Information');

	            if ( !is_null($configuracionCorreoElectronico->respuestaCorreo) ) {

	            	if ( is_null($configuracionCorreoElectronico->respuestaNombre) ) {
	            		$mail->addReplyTo($configuracionCorreoElectronico->respuestaCorreo);
	            	} else {
	            		$mail->addReplyTo($configuracionCorreoElectronico->respuestaCorreo, $configuracionCorreoElectronico->respuestaNombre);
	            	}

	            } 
	            // $mail->addCC('cesarmtassinari@hotmail.com');

	            $mail->isHTML(true);
	            $mail->Subject = 'Configuración de Correo Electrónico OK!';
	            // $mail->Body = 'This is the HTML message body <b>in bold!</b>';
	            $mail->Body = '<div style="width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-bottom: 40px">

							<div style="position: relative; margin: auto; width: 600px; background: white; padding: 20px">

								<center>

									<h3 style="font-weight: 100; color: #999">CONFIGURACIÓN DE CORREO ELECTRÓNICO</h3>

									<hr style="border: 1px solid #ccc; width: 80%">

									<br>

									<div style="line-height: 60px; background: #0aa; width: 60%; color: white">Comprobado</div>

									<br>

									<hr style="border: 1px solid #ccc; width: 80%">

									<h5 style="font-weight: 100; color: #999">Este correo ha sido enviado para verificar que la configuración de correo electrónico fue realizada correctamente, si no solicitó esta comprobación favor de ignorar y eliminar este correo.</h5>

								</center>

							</div>
							
						</div>';
	            $mail->AltBody = 'La configuración del correo electrónico ha sido comprobada.';

	            $mail->send();

			    $respuesta = [
    				'codigo' => 204,
    				'error' => false
    			];

			} catch (\Exception $e) {
			    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

			    $respuesta = [
    				'codigo' => 500,
    				'error' => true,
    				'errorMessage' => "{$mail->ErrorInfo}"
    			];
			}

    	} else {

			$respuesta = [
    			'codigo' => 500,
    			'error' => true,
    			'errorMessage' => 'No se lograron obtener las credenciales'
    		];

    	}

    	return $respuesta;
    }
}
