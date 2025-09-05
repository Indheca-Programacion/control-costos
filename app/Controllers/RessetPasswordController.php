<?php

namespace App\Controllers;

require_once "app/Requests/RessetPasswordRequest.php";
require_once "app/Models/RessetPassword.php";
require_once "app/Models/Usuario.php";
require_once "app/Models/Mensaje.php";
require_once "app/Controllers/MailController.php";
require_once "app/Models/ConfiguracionCorreoElectronico.php";

use App\Requests\RessetPasswordRequest;
use App\Route;
use DataTime;
use App\Models\RessetPassword;
use App\Models\Usuario;
use App\Models\Mensaje;
use App\Models\ConfiguracionCorreoElectronico;
use App\Controllers\MailController;


use App\Conexion;
use PDO;

class RessetPasswordController
{
    protected $bdName = CONST_BD_SECURITY;

    public function index()
    {
        if ( usuarioAutenticado() ) {
            header("Location:" . Route::routes('inicio'));
            die();
        }

        include "vistas/modulos/resset_password.php";
    }

    public function ressetPassword()
    {   
        try {

            $request = RessetPasswordRequest::validated();

            // VERIFICA SI EL USUARIO EXISTE
            $usuario = new Usuario;
            $usuarios = $usuario->consultar("correo",$request["correo"]);

            if($usuarios){

                $codigo = generarCodigo();
                $token = createTemporaryToken();
                $request["token"] = $token;
                $request["codigo"] = $codigo;

                $ressetPassword = new RessetPassword;
                $emailValidation = $ressetPassword->consultar("correo",$request["correo"]);

                if($emailValidation){
                    $_SESSION[CONST_SESSION_APP]["flash"] = array(
                        'clase' => 'alert-warning',
                        'titulo' => 'Resset Password',
                        'subTitulo' => 'Warning',
                        'mensaje' => "El correo de recuperación ya fue enviado"
                    );
                    
                    header("Location:" . Route::routes('resset-password'));
                    die();
                }

                $ressetPassword->crear($request);

                // MANDAR CORREO POR MANDAR MENSAJE
                $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

                if ( $configuracionCorreoElectronico->consultar(null , 1) ) 
                {

                    $arrayDestinatarios = array();
                    
                    $arrayUsuarioRecuperacion = [
                        "usuarioId" => $usuarios["id"],
                        "correo" => $request["correo"]
                    ];
                    
                    array_push($arrayDestinatarios, $arrayUsuarioRecuperacion);    

                    $mensajeCorreo = New Mensaje;

                    $liga = Route::routes('resset-password.validation-code', $token);

                    $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>
        
                                <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>
        
                                    <center>
        
                                        <h3 style='font-weight: 100; color: #999'>Recuperación de contraseña  </h3>
        
                                        <hr style='border: 1px solid #ccc; width: 80%'>
                                        
                                        <br>

                                        <h5 style='font-weight: 400; color: #000000'>CODIGO DE RECUPERACIÓN: {$codigo}</h5>

                                        <br>
        
                                        <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                            <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Recupera tu contraseña</div>
        
                                        </a>

                                        <h5 style='font-weight: 100; color: #999'>Haga click para recuperar contraseña</h5>
        
        
                                        <hr style='border: 1px solid #ccc; width: 80%'>
        
                                        <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de un nuevo mensaje, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>
                                    </center>
        
                                </div>
                                    
                            </div>";
        
                    $datos = [ "mensajeTipoId" => 3,
                                "mensajeEstatusId" => 1,
                                "asunto" => "Nuevo mensaje de Recuperación de Contraseña ",
                                "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                                "mensaje" => "Entre a la aplicación para ver el detalle de la misma.",
                                "liga" => $liga,
                                "destinatarios" => $arrayDestinatarios                
                        ];
        
                        if ( $mensajeCorreo->crearRessetPassword($datos) ) {

                            $mensajeCorreo->consultar(null , $mensajeCorreo->id);
                            $mensajeCorreo->mensajeHTML = $mensajeHTML;

                            $enviar = MailController::send($mensajeCorreo);
                            if ( $enviar["error"] ) $mensajeCorreo->noEnviado([ "error" => $enviar["errorMessage"] ]);
                            else $mensajeCorreo->enviado();
                        }
                    }
                }

            // Procesar correo para recuperación

            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'alert-success',
                'titulo' => 'Resset Password',
                'subTitulo' => 'Success',
                'mensaje' => "Correo enviado correctamente"
            );
            
            header("Location:" . Route::routes('resset-password'));
            die();

        }
        catch (\Exception $e) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'alert-danger',
                'titulo' => 'Resset Password',
                'subTitulo' => 'Error',
                'mensaje' => "ERROR" // Aquí solo se guarda el mensaje limpio
            );
            header("Location:" . Route::routes('resset-password'));
            die();
        }
    }
    
    public function validationCodeView($token){
        if ( usuarioAutenticado() ) {
            header("Location:" . Route::routes('inicio'));
            die();
        }

        $ressetPassword = new RessetPassword;
        if($tokenValido = $ressetPassword->consultar("token",$token)){

            $fechaHoraActual = new \DateTime();
            $expiracion = new \DateTime($ressetPassword->expiracion); // Convertimos la fecha de expiración a objeto DateTime
        
            $intervalo = $fechaHoraActual->getTimestamp() - $expiracion->getTimestamp(); // Diferencia en segundos
        
            if ($intervalo > 600) { // 600 segundos = 10 minutos
                $ressetPassword->eliminar();
                $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'alert-danger',
                'titulo' => 'El restablecimiento fue expirado',
                'subTitulo' => 'Validación fallida',
                'mensaje' => "El restablecimiento fue expirado, ingresa tu correo de recuperación nueva. Inténtalo nuevamente."
                );
                header("Location:" . Route::routes('resset-password'));
            } 
            include "vistas/modulos/validation_code.php";
        }else{
            header("Location:" . Route::routes('resset-password'));
        }
    }

    public function validationCode()
    {
        $codigo = $_POST["codigo"];
        $token = $_POST["_token"];
    
        $ressetPassword = new RessetPassword;
        $ressetPassword->consultar("token", $token);
    
        // Verifica si se encontró el token y si el código coincide
        if (!$ressetPassword || $codigo !== $ressetPassword->codigo) {
    
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'alert-danger',
                'titulo' => 'Código incorrecto',
                'subTitulo' => 'Validación fallida',
                'mensaje' => "El código de validación ingresado no es correcto. Inténtalo nuevamente."
            );
    
            // Redirige con el token para volver a intentar
            header("Location: " . Route::routes('resset-password') . "/{$token}/validation-code");
            exit();
        }
        
        // Guardar el código en la sesión
        $_SESSION[CONST_SESSION_APP]["codigo"] = $codigo;
        // Si es correcto, redirige a la siguiente vista
        header("Location: " . Route::routes('resset-password') . "/{$token}/change-password");
        exit();
    }

    public function changePasswordView($token){

        if ( usuarioAutenticado() ) {
            header("Location:" . Route::routes('inicio'));
            die();
        }

        $ressetPassword = new RessetPassword;
        $ressetPassword->consultar("token", $token);

        // Verifica si existe el código en la sesión
        if (isset($_SESSION[CONST_SESSION_APP]["codigo"])) {
            $codigoSesion = $_SESSION[CONST_SESSION_APP]["codigo"];
            $codigoBD = $ressetPassword->codigo;
        
            if ($codigoSesion === $codigoBD) {
                include "vistas/modulos/change_password.php";
            } else {
                header("Location: " . Route::routes('resset-password') . "/{$token}/validation-code");
                exit();
            }
        } else {
            header("Location: " . Route::routes('resset-password') . "/{$token}/validation-code");
            exit();
        }
        die;



    }

    public function changePassword($token){

        $request = RessetPasswordRequest::validated();

        // VERIFICA SI EL USUARIO EXISTE
        $ressetPassword = new RessetPassword;
        $ressetPassword->consultar("token",$token);

        $request["correo"] = $ressetPassword->correo;

        $respuesta = $ressetPassword->ressetPassword($request);

        if($respuesta){

            $ressetPassword->eliminar();
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
            'clase' => 'alert-success',
            'titulo' => 'El restablecimiento fue exitoso',
            'subTitulo' => 'Recuperación exitosa',
            'mensaje' =>"La recuperación fue exitosa, inicia sesión."
            );
            unset($_SESSION[CONST_SESSION_APP]["codigo"]);
            header("Location:" . Route::routes('ingreso'));
        }
        die;

    }
}

