<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Controllers/Autorizacion.php";
require_once "../Controllers/MailController.php";

require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Models/MensajeRequisicion.php";
require_once "../Models/Requisicion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Mensaje.php";

require_once "../Requests/SaveMensajeRequisicionRequest.php";

use App\Route;
use App\Controllers\Validacion;
use App\Controllers\Autorizacion;
use App\Controllers\MailController;

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\MensajeRequisicion;
use App\Models\Mensaje;
use App\Models\Requisicion;
use App\Models\Usuario;

use App\Requests\SaveMensajeRequisicionRequest;

class MensajeRequisicionAjax
{

     /*=============================================
    MOSTRAR CHAT

    FUNCIÓN PARA MOSTRAR LOS MENSAJE CREADOS EN FORMA
    DE LISTA.
    =============================================*/
    public function mostrarMensajes()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $messageRequisition = new MensajeRequisicion;
            // Suponiendo que consultar devuelve un arreglo, no un objeto Eloquent
            $messageRequisitions = $messageRequisition->consultar(null,$this->idRequisicion);
            
            // Si consultar devuelve un arreglo asociativo con claves duplicadas, debemos limpiarlo
            $cleanMessages = array_map(function ($mensaje) {
                // Filtrar solo las claves necesarias, por ejemplo:
                return [
                    'id' => $mensaje['id'],
                    'mensaje' => $mensaje['mensaje'],
                    'idRequisicion' => $mensaje['idRequisicion'],
                    'idUsuario' => $mensaje['idUsuario'],
                    'fechaEnviado' => fFechaLargaHora($mensaje['fechaEnviado']),
                    'nombreCompleto' => $mensaje['nombreCompleto'],
                    'idSesion' => usuarioAutenticado()["id"],
                ];
            }, $messageRequisitions);

    
            $respuesta = array();
            $respuesta['codigo'] = 200;  
            $respuesta['error'] = false; 
            $respuesta['messageResponse'] = "Mensajes obtenidos correctamente"; 
            $respuesta['mensajes'] = $cleanMessages; 
    
        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Error al obtener los mensajes, llama a un administrador"
            ];
        }
        echo json_encode($respuesta);
    }
    
    /*=============================================
    CREAR MENSAJE

    FUNCION PARA LA CREACIÓN DE MENSJAES CON RELACIÓN
    A LAS REQUISICIONES Y ENVIO DE CORREO ELECTRONICO
    AUTOMATICO AL CREAR EL MENSAJE .
    =============================================*/
    public function crearMensaje()
    {    
        try{

			$request = SaveMensajeRequisicionRequest::validated();

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $mensaje = New MensajeRequisicion;
            $mensaje->crear($request);

            // MANDAR CORREO POR MANDAR MENSAJE
            $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

            if ( $configuracionCorreoElectronico->consultar(null , 1)) 
            {

                $idRequisicion = $request["idRequisicion"];
                $arrayRecipients = array();
                
                // Obtener el usuario autenticado
                $user = new Usuario;
                $userSendMessage = $user->consultar(null,usuarioAutenticado()["id"]);


                // $usersShopping = $user->consultarCompradores();
                
                $requisition = New Requisicion;
                $userCreateRequisition = $requisition->userCreateRequisition($idRequisicion);
                
                // foreach ($userCreateRequisition as $key => $user) {
                //     $userCreateRequisitionArray = [
                //         "usuarioId" => $user["id"],
                //         "correo" => $user["correo"]
                //     ];
                //     array_push($arrayRecipients, $userCreateRequisitionArray);
                // }

                $userSendMessageArray = [
                    "usuarioId" => $userSendMessage["id"],
                    "correo" => $userSendMessage["correo"]
                ];

                array_push($arrayRecipients, $userSendMessageArray);    


                // ACTIVAR PARA USUARIOS COMPRAS
                // foreach ($usersShopping as $value) {

                //     if (in_array($value["id"], array_column($arrayRecipients, "id"))) continue;
                //     $resipientsShopping = [
                //         "usuarioId" => $value["id"],
                //         "correo" => $value["correo"]
                //     ];
                //     array_push($arrayRecipients, $resipientsShopping);
                // }
                
                $message = New Mensaje;

                $liga = Route::names('requisiciones.edit',$idRequisicion);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>
    
                            <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>
    
                                <center>
    
                                    <h3 style='font-weight: 100; color: #999'>Nuevo mensaje REQUISICION {$idRequisicion} </h3>
    
                                    <hr style='border: 1px solid #ccc; width: 80%'>
                                    
                                    <br>
    
                                    <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                        <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha sido creado un nuevo mensaje</div>
    
                                    </a>
    
                                    <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>
    
                                    <hr style='border: 1px solid #ccc; width: 80%'>
    
                                    <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de un nuevo mensaje, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>
    
                                </center>
    
                            </div>
                                
                        </div>";
    
                $datos = [ "mensajeTipoId" => 3,
                            "mensajeEstatusId" => 1,
                            "asunto" => "Nuevo mensaje de " .$userSendMessage["nombre"] ." Requisicion ".$idRequisicion,
                            "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                            "mensaje" => "Se ha cerrado el gasto , entre a la aplicación para ver el detalle de la misma.",
                            "liga" => $liga,
                            "destinatarios" => $arrayRecipients                
                    ];

                    if ( $message->crear($datos) ) {
                        $message->consultar(null , $message->id);
                        $message->mensajeHTML = $mensajeHTML;
    
                        $send = MailController::send($message);
                        if ( $send["error"] ) $message->noEnviado([ "error" => $send["errorMessage"] ]);
                        else $message->enviado();
                    }
            }

            $respuesta = [
                'error' => false,
                'mensaje' => "Mensaje enviado correctamente"
            ];
            
        }catch (\Exception $e) {
            $respuesta = [
                'error' => true,
                'errorMessage' => $e->getMessage(), // El mensaje del error
            ];
        }
        echo json_encode($respuesta);

    }
}

$mensajeRequisicionAjax = New MensajeRequisicionAjax();

if (isset($_POST["accion"])) {
    if ( $_POST["accion"] == "crearMensaje" ) {
        /*=============================================
        CREAR MENSAJES
        =============================================*/	
        $mensajeRequisicionAjax->crearMensaje();
    } else {
        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => 'Acción no encontrada'
        ];
    }
} else {
    /*=============================================
	TABLA DE INVENTARIOS
	=============================================*/
    $mensajeRequisicionAjax->idRequisicion = $_GET["idRequisicion"];
    $mensajeRequisicionAjax->mostrarMensajes();
}