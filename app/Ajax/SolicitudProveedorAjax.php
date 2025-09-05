<?php

namespace App\Ajax;

session_start();
// Configuración de Errores
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/control-costos/php_error_log');

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/SolicitudProveedor.php";
require_once "../Models/Mensaje.php";
require_once "../Models/Proveedor.php";

require_once "../Controllers/Autorizacion.php";
require_once "../Controllers/MailController.php";

use App\Route;
use App\Models\SolicitudProveedor;
use App\Models\Proveedor;

use App\Models\Mensaje;

use App\Models\ConfiguracionCorreoElectronico;

use App\Controllers\MailController;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class SolicitudProveedorAjax
{

	/*=============================================
	TABLA DE SOLICITUDES DE PROVEEDORES
	=============================================*/
	public function mostrarTabla()
	{
		$solicitudProveedor = New SolicitudProveedor;
        $solicitudesProveedores = $solicitudProveedor->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "razonSocial" ]);
        array_push($columnas, [ "data" => "rfc"]);
        array_push($columnas, [ "data" => "correoElectronico" ]);
        array_push($columnas, [ "data" => "nombre"]);
        array_push($columnas, [ "data" => "telefono"]);
        array_push($columnas, [ "data" => "origenProveedor"]);
        array_push($columnas, [ "data" => "estatus"]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($solicitudesProveedores as $key => $value) {

        	$rutaEdit = Route::names('solicitud-proveedor.edit', $value['id']);
        	$rutaDestroy = Route::names('solicitud-proveedor.destroy', $value['id']);
            $solicitudId = $value["id"];

        	array_push( $registros, [ 
                "consecutivo" => $value["id"],
                "razonSocial" => $value["razonSocial"],
                "rfc" => $value["rfc"],
                "correoElectronico" => $value["correoElectronico"],
                "nombre" => $value["nombreApellido"],
                "telefono" =>  $value["telefono"],
                "origenProveedor" => $value["origenProveedor"],
                "estatus" => $value["esp.descripcion"],
                "acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									<input type='hidden' name='_method' value='DELETE'>
									<input type='hidden' name='_token' value='{$token}'>
										<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$solicitudId}'>
											<i class='far fa-times-circle'></i>
										</button>
									</form>" 
                    ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
    /*=============================================
	FUNCION PARA AUTORIZAR SOLICITUDES
	=============================================*/
    public $detallesSolicitud;

    public function autorizarSolicitud() {
        try {

            $idSolicitud = $this->detallesSolicitud["idSolicitudProveedor"];
            $observacionSolicitudProveedor = $this->detallesSolicitud["observacionSolicitudProveedor"];

            $solicitudProveedor = new SolicitudProveedor;
            $solicitudProveedor->id = $idSolicitud;
            $solicitudProveedor->observacionSolicitudProveedor = $observacionSolicitudProveedor;

            // FUNCION ENCARGADA DE VERIFICAR QUE LOS 
            // ARCHIVOS DE LA SOLICITUD ESTEN AUTORIZADOS
            //  O RECHAZADOS
            $archivosActualizados = $solicitudProveedor->consultarArchivosActualizados(true);

            if (!empty($archivosActualizados)) {
                echo json_encode([
                    'codigo' => 400,
                    'error' => true,
                    'mensaje' => 'Se requiere revisar los documentos',
                    'data' => $archivosActualizados
                ]);
                exit;
            }

            // FUNCION AUTORIZAR
            $respuestaAutorizacion = $solicitudProveedor->autorizarSolicitudProveedor();

            if (!$respuestaAutorizacion[0]["status"]) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            if ($respuestaAutorizacion[0]["status"]) 
            {
                $proveedor = new Proveedor;
                try {


                    // CREAR SESION PROVEEDOR
                    $respuestaAltaProveedor = $proveedor->crearSesionProveedor($this->detallesSolicitud);

                    /*******************************
                     * MOVER ARCHIVOS DE SOLICITUD A ARCHIVOS PROVEEDORES
                     * Y ACTULIZAR NUEVA RUTA EN REGISTROS SOLICITUD ARCHIVOS
                     *******************************/ 
                    $archivosDeLaSolicitud = $solicitudProveedor->consultarArchivosActualizados();

                    $archivosMovidos = $proveedor->moverArchivosSolicitudAProveedores($archivosDeLaSolicitud);

                    // DATOS PARA EL MENSAJE DEL CORREO
                    $DETALLE_AUTORIZACION = $observacionSolicitudProveedor !== "" ? $observacionSolicitudProveedor : "Todo en orden, proveedor verificado exitosamente.";
                    $USUARIO = $respuestaAltaProveedor["usuario"];
                    $CONTRASEÑA = $respuestaAltaProveedor["contrasena"];
                    $LINK_PORTAL = CONST_RUTA_SERVIDOR_PROVEEDOR;

                    //TODO CAMBIAR POR EL CORREO DEL PROVEEDOR
                    $userSendMessageArray = [
                        "usuarioId" => 43,
                        "correo" => "josuepe03@hotmail.com"
                    ];

                    $datosCorreo = [];

                    if($respuestaAutorizacion[0]["estatusSolicitud"] != 21 ){

                        $archivosRechazados = $solicitudProveedor->consultarArchivosRechazados();


                        $resultado = "";
                        if (!empty($archivosRechazados)) {
                            foreach ($archivosRechazados as $archivo) {
                                $tipo = $archivo['tipo_descripcion'] ?? 'Sin tipo';
                                $titulo = $archivo['titulo'] ?? 'Sin nombre';
                                $observacion = $archivo['observacion'] ?? 'Sin observación';

                                $resultado .= "<li><strong>Tipo:</strong> $tipo<br><strong>Nombre:</strong> $titulo<br><strong>Observación:</strong> $observacion</li><br>";
                            }
                        }

                        $datosCorreo = [
                            'mensajeHTML' => "
                                <body style='margin: 0; padding: 40px 0; background: #eee; font-family: sans-serif;'>
                                    <div style='max-width: 600px; margin: auto; background: #fff; padding: 30px; text-align: center;'>
                                        <h3 style='font-weight: 100; color: #999;'>
                                            Solicitud de Proveedor – Estado: <span style='color: #E67E22;'>Pendiente de Archivos</span>
                                        </h3>
                                        <hr style='border: 1px solid #ccc; width: 80%; margin: 20px auto;'>
                                        <div style='background: #E67E22; color: white; padding: 15px; font-size: 18px; font-weight: bold;'>
                                            Tu solicitud está pendiente debido a archivos rechazados
                                        </div>
                                        <div style='text-align: left; margin-top: 30px; font-size: 16px; color: #333;'>
                                            <p><strong>Folio de Solicitud:</strong> ${idSolicitud}</p>
                                            <p><strong>Observación General:</strong> ${DETALLE_AUTORIZACION}</p>
                                            <p><strong>Usuario:</strong> ${USUARIO}</p>
                                            <p><strong>Contraseña:</strong> ${CONTRASEÑA}</p>
                                            <p><strong>Accede al sistema aquí:</strong> 
                                                <a href='${LINK_PORTAL}' style='color: #00913F; text-decoration: none;'>${LINK_PORTAL}</a>
                                            </p>
                                        </div>

                                        <div style='text-align: left; margin-top: 30px; font-size: 16px; color: #333;'>
                                            <p><strong>Archivos Rechazados:</strong></p>
                                            <ul style='padding-left: 20px;'>
                                                $resultado
                                            </ul>
                                        </div>

                                        <hr style='border: 1px solid #ccc; width: 80%; margin: 30px auto;'>
                                        <h5 style='font-weight: 100; color: #999;'>
                                            Este correo ha sido enviado para informarte sobre el estado de tu solicitud. Por favor revisa y corrige los archivos rechazados.
                                        </h5>
                                    </div>
                                </body>
                            ",
                            'asunto' => "Solicitud de Proveedor – Pendiente de Archivos",
                            'mensaje' => "Tu solicitud tiene archivos rechazados, revisa los detalles."
                        ];

                    }else{
                        $datosCorreo = [
                            'mensajeHTML' => "<body style='margin: 0; padding: 40px 0; background: #eee; font-family: sans-serif;'>
                                                <div style='max-width: 600px; margin: auto; background: #fff; padding: 30px; text-align: center;'>
                                                    <h3 style='font-weight: 100; color: #999;'>
                                                        Solicitud de Proveedor Autorizada
                                                    </h3>
                                                    <hr style='border: 1px solid #ccc; width: 80%; margin: 20px auto;'>
                                                    <div style='background: #00913F; color: white; padding: 15px; font-size: 18px; font-weight: bold;'>
                                                        ¡Tu solicitud ha sido aprobada!
                                                    </div>
                                                    <div style='text-align: left; margin-top: 30px; font-size: 16px; color: #333;'>
                                                        <p><strong>Folio de Solicitud:</strong> ${idSolicitud} </p>
                                                        <p><strong>Observación:</strong> ${DETALLE_AUTORIZACION}</p>
                                                        <p><strong>Usuario:</strong> ${USUARIO} </p>
                                                        <p><strong>Contraseña:</strong> ${CONTRASEÑA} </p>
                                                        <p><strong>Accede al sistema aquí:</strong> 
                                                            <a href='${LINK_PORTAL}' style='color: #00913F; text-decoration: none;'>${LINK_PORTAL}</a>
                                                        </p>
                                                    </div>
                                                    <hr style='border: 1px solid #ccc; width: 80%; margin: 30px auto;'>
                                                    <h5 style='font-weight: 100; color: #999;'>
                                                        Este correo ha sido enviado para informar al personal autorizado sobre la autorización de una solicitud. Si no solicitó esta información, por favor ignore y elimine este mensaje.
                                                    </h5>
                                                </div>
                                            </body>
                                            ",
                            'asunto' => "Solicitud de Proveedor",
                            'mensaje' => "Mensaje de solicitud de proveedor"
                        ];
                    }

                    //ENVIAR CORREO
                    $respuestaEnvioCorreo = enviarCorreo($userSendMessageArray,$datosCorreo);

                } catch (\Exception $e) {
                    error_log('Error al crear la sesión del proveedor: ' . $e->getMessage());
                    echo json_encode([
                        'codigo' => 400,
                        'error' => $e->getMessage(),
                        'mensaje' => 'El Proveedor con esos datos ya existe.'
                    ]);
                    return; 
                }
            }
        // Éxito
        echo json_encode([
            'codigo' => 200,
            'error' => false
        ]);

        } catch (\Exception $e) {
            echo json_encode([
                'codigo' => 500,
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
	/*=============================================
	FUNCION PARA RECHAZAR SOLICITUDES
	=============================================*/
    public function rechazarSolicitud() {
        try {
            $idSolicitud = $this->detallesSolicitud["idSolicitudProveedor"];
            $observacionSolicitudProveedor = $this->detallesSolicitud["observacionSolicitudProveedor"];

            $solicitudProveedor = new SolicitudProveedor;
            $solicitudProveedor->id = $idSolicitud;
            $solicitudProveedor->observacionSolicitudProveedor = $observacionSolicitudProveedor;

            // FUNCION ENCARGADA DE VERIFICAR QUE LOS 
            // ARCHIVOS DE LA SOLICITUD ESTEN AUTORIZADOS
            //  O RECHAZADOS
            $archivosActualizados = $solicitudProveedor->consultarArchivosActualizados(true);
            if (!empty($archivosActualizados)) {
                echo json_encode([
                    'codigo' => 400,
                    'error' => true,
                    'mensaje' => 'Se requiere revisar los documentos',
                    'data' => $archivosActualizados
                ]);
                exit;
            }

            // FUNCION RECHAZAR
            $respuestaAutorizacion = $solicitudProveedor->rechazarSolicitudProveedor();
            if (!$respuestaAutorizacion[0]["status"]) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            if ($respuestaAutorizacion[0]["status"]) 
            {
                $proveedor = new Proveedor;
                try {
                    // CREAR PROVEEDOR
                    $respuestaAltaProveedor = $proveedor->crearSesionProveedor($this->detallesSolicitud);   
                    
                    /*******************************
                     * MOVER ARCHIVOS DE SOLICITUD A ARCHIVOS PROVEEDORES
                     * Y ACTULIZAR NUEVA RUTA EN REGISTROS SOLICITUD ARCHIVOS
                     *******************************/ 
                    $archivosDeLaSolicitud = $solicitudProveedor->consultarArchivosActualizados();
                    $archivosMovidos = $proveedor->moverArchivosSolicitudAProveedores($archivosDeLaSolicitud);

                    // DATOS PARA EL MENSAJE DEL CORREO
                    $DETALLE_CANCELACION = $observacionSolicitudProveedor !== "" ? $observacionSolicitudProveedor : "RECTIFIQUE SU INFORMACIÓN";
                    $USUARIO = $respuestaAltaProveedor["usuario"];
                    $CONTRASEÑA = $respuestaAltaProveedor["contrasena"];
                    $LINK_PORTAL = CONST_RUTA_SERVIDOR_PROVEEDOR;
                    $NOMBRE_SOLICITANTE = $this->detallesSolicitud["nombreApellido"];

                    $userSendMessageArray = [
                        "usuarioId" => 43,
                        "correo" => "josuepe03@hotmail.com"
                    ];

                    $datosCorreo = [];
                    if($respuestaAutorizacion[0]["estatusSolicitud"] != 22 ){

                        $archivosRechazados = $solicitudProveedor->consultarArchivosRechazados();
                        $resultado = "";
                        if (!empty($archivosRechazados)) {
                            foreach ($archivosRechazados as $archivo) {
                                $tipo = $archivo['tipo_descripcion'] ?? 'Sin tipo';
                                $titulo = $archivo['titulo'] ?? 'Sin nombre';
                                $observacion = $archivo['observacion'] ?? 'Sin observación';

                                $resultado .= "<li><strong>Tipo:</strong> $tipo<br><strong>Nombre:</strong> $titulo<br><strong>Observación:</strong> $observacion</li><br>";
                            }
                        }

                        $datosCorreo = [
                            'mensajeHTML' => "
                                <body style='margin: 0; padding: 40px 0; background: #eee; font-family: sans-serif;'>
                                    <div style='max-width: 600px; margin: auto; background: #fff; padding: 30px; text-align: center;'>
                                        <h3 style='font-weight: 100; color: #999;'>
                                            Solicitud de Proveedor – Estado: <span style='color: #E67E22;'>Pendiente de Archivos</span>
                                        </h3>
                                        <hr style='border: 1px solid #ccc; width: 80%; margin: 20px auto;'>
                                        <div style='background: #E67E22; color: white; padding: 15px; font-size: 18px; font-weight: bold;'>
                                            Tu solicitud está pendiente debido a archivos rechazados
                                        </div>
                                        <div style='text-align: left; margin-top: 30px; font-size: 16px; color: #333;'>
                                            <p><strong>Folio de Solicitud:</strong> ${idSolicitud}</p>
                                            <p><strong>Observación General:</strong> ${DETALLE_CANCELACION}</p>
                                            <p><strong>Usuario:</strong> ${USUARIO}</p>
                                            <p><strong>Contraseña:</strong> ${CONTRASEÑA}</p>
                                            <p><strong>Accede al sistema aquí:</strong> 
                                                <a href='${LINK_PORTAL}' style='color: #00913F; text-decoration: none;'>${LINK_PORTAL}</a>
                                            </p>
                                        </div>

                                        <div style='text-align: left; margin-top: 30px; font-size: 16px; color: #333;'>
                                            <p><strong>Archivos Rechazados:</strong></p>
                                            <ul style='padding-left: 20px;'>
                                                $resultado
                                            </ul>
                                        </div>

                                        <hr style='border: 1px solid #ccc; width: 80%; margin: 30px auto;'>
                                        <h5 style='font-weight: 100; color: #999;'>
                                            Este correo ha sido enviado para informarte sobre el estado de tu solicitud. Por favor revisa y corrige los archivos rechazados.
                                        </h5>
                                    </div>
                                </body>
                            ",
                            'asunto' => "Solicitud de Proveedor – Pendiente de Archivos",
                            'mensaje' => "Tu solicitud tiene archivos rechazados, revisa los detalles."
                        ];

                    }else{
                        $datosCorreo = [
                            'mensajeHTML' => "<body style='margin:0; padding:0; background:#eee; font-family:sans-serif;'>
                                                <div style='width:100%; padding-top:40px; padding-bottom:40px; background:#eee;'>
                                                    <div style='max-width:600px; margin:auto; background:white; padding:20px; border-radius:6px;'>

                                                    <div style='text-align:center'>
                                                        <h2 style='color:#b00020; font-weight:normal;'>Solicitud rechazada</h2>
                                                        <hr style='border:1px solid #ccc; width:80%;'>

                                                        <div style='background:#b00020; color:white; padding:15px 0; margin:20px 0; width:80%; border-radius:4px; display:inline-block;'>
                                                        Tu solicitud ha sido rechazada por el equipo revisor.
                                                        </div>

                                                        <p style='color:#444; font-size:15px; margin:20px 0;'>Estimado/a <strong>${NOMBRE_SOLICITANTE}</strong>,</p>

                                                        <p style='color:#666; font-size:15px; margin:10px 0 20px;'>
                                                        Se detectaron inconsistencias o archivos faltantes. Por favor, ingresa al portal para revisar el detalle de la cancelación y rectificar los archivos requeridos.
                                                        </p>

                                                        <div style='text-align:left; margin:0 auto; width:80%; background:#f8f8f8; padding:15px; border-radius:5px;'>
                                                        <p style='margin:5px 0; color:#333;'><strong>Usuario:</strong> ${USUARIO}</p>
                                                        <p style='margin:5px 0; color:#333;'><strong>Contraseña:</strong> ${CONTRASEÑA}</p>
                                                        <p style='margin:5px 0; color:#333;'><strong>Portal:</strong> <a href='${LINK_PORTAL}' target='_blank' style='color:#007bff;'>${LINK_PORTAL}</a></p>
                                                        <p style='margin:10px 0; color:#333;'><strong>Detalle de la cancelación:</strong><br>${DETALLE_CANCELACION}</p>
                                                        </div>

                                                        <hr style='border:1px solid #ccc; width:80%; margin-top:30px;'>

                                                        <p style='color:#999; font-size:13px;'>
                                                        Este mensaje ha sido enviado automáticamente. Si no realizaste esta solicitud, puedes ignorar este correo.
                                                        </p>
                                                    </div>

                                                    </div>
                                                </div>
                                            </body>
                                            ",
                            'asunto' => "Solicitud de Proveedor",
                            'mensaje' => "Mensaje de solicitud de proveedor"
                        ];
                    }

                    $respuestaEnvioCorreo = enviarCorreo($userSendMessageArray,$datosCorreo);

                } catch (\Exception $e) {
                    error_log('Error al crear la sesión del proveedor: ' . $e->getMessage());
                    echo json_encode([
                        'codigo' => 400,
                        'error' => true,
                        'mensaje' => 'El Proveedor con esos datos ya existe.'
                    ]);
                    return; 
                }
            }

            // Éxito
            echo json_encode([
                'codigo' => 200,
                'error' => false
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'codigo' => 500,
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /*=============================================
	FUNCION PARA CAMBIAR LOS ESTADOS DE LOS ARCHIVOS (AUTORIZAO - RECHAZADO)
	=============================================*/
    public function estadoArchivo() {
        try {

            $solicitudProveedor = new SolicitudProveedor;
            $solicitudProveedor->id = $this->idSolicitudProveedor;
            $solicitudProveedor->archivoId = $this->archivoId;
            $solicitudProveedor->estadoArchivo = $this->estadoArchivo;

            $solicitudProveedor->observacionSolicitudProveedor = $this->observacionEstadoArchivo;

            // FUNCION AUTORIZAR
            $respuesta = $solicitudProveedor->estadoArchivo();
            if (!$respuesta) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            // Éxito
            echo json_encode([
                'codigo' => 200,
                'error' => false
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'codigo' => 500,
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}

$solicitudProveedorAjax = new SolicitudProveedorAjax();

try {
    if (isset($_POST["accion"])){
        if ( $_POST["accion"] == "rechazarSolicitud") 
        {
            /*=============================================
            RECHAZAR SOLICITUD
            =============================================*/
            foreach (json_decode($_POST['detallesSolicitud'], true) as $item) {
                 $solicitudProveedorAjax->detallesSolicitud[$item['key']] = $item['value'];
            }

            $solicitudProveedorAjax->rechazarSolicitud();
        }else if ( $_POST["accion"] == "autorizarSolicitud") 
        {
            /*=============================================
            AUTORIZAR SOLICITUD
            =============================================*/
            foreach (json_decode($_POST['detallesSolicitud'], true) as $item) {
                 $solicitudProveedorAjax->detallesSolicitud[$item['key']] = $item['value'];
            }
            
            $solicitudProveedorAjax->autorizarSolicitud();
        } 
        else if ( $_POST["accion"] == "estadoArchivo") 
        {
            /*=============================================
            ESTADO ARCHIVO
            =============================================*/
            $solicitudProveedorAjax->estadoArchivo = $_POST["estadoArchivo"];
            $solicitudProveedorAjax->archivoId = $_POST["archivoId"];
            $solicitudProveedorAjax->idSolicitudProveedor = $_POST["idSolicitudProveedor"];
            $solicitudProveedorAjax->observacionEstadoArchivo = $_POST["observacionEstadoArchivo"];

            $solicitudProveedorAjax->estadoArchivo();
        } 
    }
    else 
    {
        $solicitudProveedorAjax->mostrarTabla();
    }
} catch (\Exception $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
