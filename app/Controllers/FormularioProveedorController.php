<?php

namespace App\Controllers;

require_once "app/Controllers/Autorizacion.php";
require_once "app/Controllers/MailController.php";

require_once "app/Models/Mensaje.php";
require_once "app/Models/ConfiguracionCorreoElectronico.php";

require_once "app/Requests/SaveSolicitudProveedorRequest.php";

use App\Route;
use App\Conexion;

use App\Models\SolicitudProveedor;
use App\Models\Mensaje;
use App\Models\ConfiguracionCorreoElectronico;

use App\Controllers\MailController;
use App\Requests\SaveSolicitudProveedorRequest;

class FormularioProveedorController
{
    public function index()
    {
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/formulario-proveedores/index.php');
        include "vistas/modulos/formulario-proveedores/plantilla/plantilla.php";
    }

    public function store()
    {   
        $camposArchivo = [
            "constanciaFiscal",
            "opinionCumplimiento",
            "comprobanteDomicilio",
            "datosBancarios",
        ];

        if (isset($_SESSION['archivos_subidos']))
        {
            foreach ($camposArchivo as $key => $item) {
                $_FILES[$item]['name'] = $_SESSION['archivos_subidos'][$item]["nombre_archivo_original"];
                $_FILES[$item]['nombreGenerado'] = $_SESSION['archivos_subidos'][$item]["nombre_archivo_generado"];
                $_FILES[$item]['rutaOriginal'] = $_SESSION['archivos_subidos'][$item]["ruta"];
            }
        }


        $request = SaveSolicitudProveedorRequest::validated();
        $solicitudProveedor = New SolicitudProveedor;
    
        $respuesta = $solicitudProveedor->crear($request);

        //TODO CAMBIAR CORREO POR EL DEL FORMULARIO
        $userSendMessageArray = [
            "usuarioId" => 1,
            "correo" => $request["correoElectronico"]
        ];

        $datosCorreo = [
            'mensajeHTML' => "<body style='margin:0; padding:0; background:#eee; font-family:sans-serif;'>
                                <div style='width:100%; padding-top:40px; padding-bottom:40px; background:#eee;'>
                                    <div style='max-width:600px; margin:auto; background:white; padding:20px; border-radius:6px;'>

                                    <div style='text-align:center'>
                                        <h2 style='color:#333; font-weight:normal;'>¡Solicitud enviada!</h2>
                                        <hr style='border:1px solid #ccc; width:80%;'>

                                        <div style='background:#28a745; color:white; padding:15px 0; margin:20px 0; width:80%; border-radius:4px; display:inline-block;'>
                                        Tu solicitud ha sido enviada exitosamente.
                                        </div>

                                        <p style='color:#666; font-size:15px; margin:20px 0 30px;'>Por favor, espera la respuesta del equipo encargado. Te notificaremos en cuanto haya una actualización.</p>

                                        <hr style='border:1px solid #ccc; width:80%;'>

                                        <p style='color:#999; font-size:13px;'>Este mensaje ha sido enviado automáticamente. Si no realizaste esta solicitud, puedes ignorar este correo.</p>
                                    </div>

                                    </div>
                                </div>
                                </body>
                            ",
            'asunto' => "Solicitud de Proveedor",
            'mensaje' => "Mensaje de solicitud de proveedor"
        ];

        if ( $respuesta ) {

            $respuestaEnvioCorreo = enviarCorreo($userSendMessageArray,$datosCorreo);

            session_unset();
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Enviar Formulario',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El formulario fue enviado correctamente, espere instrucciones.' );
            header("Location:" . Route::names('formulario-proveedor.index'));
        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Enviar Formulario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('formulario-proveedor.index'));
        }
        die();

    }
}
