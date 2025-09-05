<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Models/Mensaje.php";
require_once "../Models/Usuario.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Controllers/MailController.php";

use App\Route;
use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;
use App\Models\Usuario;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Controllers\MailController;

class ConfiguracionCorreoElectronicoAjax
{
	/*=============================================
	TABLA DE MENSAJES
	=============================================*/
	public function mostrarTabla()
	{
		$mensaje = New Mensaje;
        $mensajes = $mensaje->consultar();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "error" ]);
        array_push($columnas, [ "data" => "usuario" ]);
        array_push($columnas, [ "data" => "asunto" ]);
        array_push($columnas, [ "data" => "mensaje" ]);
        array_push($columnas, [ "data" => "destinatarios" ]);
        // array_push($columnas, [ "data" => "acciones" ]);

        $registros = array();
        foreach ($mensajes as $key => $value) {

        	if ( $value["mensaje_tipos.nombreCorto"] != 'correo-electronico' || $value["mensaje_estatus.envio"] != 1 ) continue;

        	array_push( $registros, [
                "consecutivo" => ($key + 1),
        		"fecha" => $value["fechaCreacion"],
        		"estatus" => mb_strtoupper(fString($value["mensaje_estatus.descripcion"])),
        		"error" => fString($value["error"]),
        		// "fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
                "usuario" => mb_strtoupper(fString($value["usuarios.nombreCompleto"])),
        		"asunto" => fString($value["asunto"]),
        		"mensaje" => fString($value["mensaje"]),
        		"destinatarios" => mb_strtolower(fString($value["correos_destinos"])) ] );

        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;
        $respuesta['mensajes'] = $mensajes;

        echo json_encode($respuesta);
	}

	/*=============================================
	COMPROBAR CONFIGURACIÓN
	=============================================*/
	public $token;

	public function comprobar()
	{
		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "conf-correo", "ver") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a comprobar la Configuración.";

	        }
        
        } else {

    	    $respuesta["error"] = true;
			$respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

        if ( $respuesta["error"] ) {
        	echo json_encode($respuesta);
        	return;
        }

		// Validar Token
		if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        if ( $respuesta["error"] ) {
        	echo json_encode($respuesta);
        	return;
        }

		// $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

		$respuesta["respuesta"] = false;

		$enviar = MailController::test();

		if ( $enviar['error'] ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = $enviar['errorMessage'];

		} else {

			$respuesta["respuesta"] = true;
			$respuesta["respuestaMessage"] = "El correo fue enviado correctamente.";

		}

		echo json_encode($respuesta);
	}

	public $perfilesCrear;
	public $estatusModificarUsuarioCreacion;
	public $estatusModificarPerfiles;
    public $uploadDocumentos;
    public $usuarioUploadDocumento;
    public $perfilesUploadDocumento;

	/*=============================================
	ACTUALIZAR AVISOS
	=============================================*/
	public function actualizarAvisos()
	{
		$respuesta["error"] = false;		

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "conf-correo", "actualizar") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado para Actualizar avisos.";

	        }
        
        } else {

    	    $respuesta["error"] = true;
			$respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

        if ( $respuesta["error"] ) {
        	echo json_encode($respuesta);
        	return;
        }

        // Validar Token
		if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        if ( $respuesta["error"] ) {
        	echo json_encode($respuesta);
        	return;
        }

        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

        $datos["perfilesCrear"] = $this->perfilesCrear;
        $datos["estatusModificarUsuarioCreacion"] = $this->estatusModificarUsuarioCreacion;
        $datos["estatusModificarPerfiles"] = $this->estatusModificarPerfiles;
        $datos["uploadDocumentos"] = $this->uploadDocumentos;
        $datos["usuarioUploadDocumento"] = $this->usuarioUploadDocumento;
        $datos["perfilesUploadDocumento"] = $this->perfilesUploadDocumento;

        $respuesta["respuesta"] = false;

        $configuracionCorreoElectronico->id = 1;
        // Actualizar avisos
        if ( $configuracionCorreoElectronico->actualizarAvisos($datos) ) {

        	$respuesta["respuesta"] = true;
        	$respuesta["respuestaMessage"] = "Los avisos fueron actualizados correctamente.";
        	
        } else {

        	$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Hubo un error al intentar actualizar los avisos, intente de nuevo.";

        }

		echo json_encode($respuesta);
	}
}

$configuracionCorreoElectronicoAjax = New ConfiguracionCorreoElectronicoAjax;

if ( isset($_POST["accion"]) ) {

	$configuracionCorreoElectronicoAjax->token = $_POST["_token"];

	if ( $_POST["accion"] == "comprobar" ) {

		/*=============================================
		COMPROBAR CONFIGURACIÓN
		=============================================*/
		$configuracionCorreoElectronicoAjax->comprobar();

	} elseif ( $_POST["accion"] == "actualizarAvisos" ) {

		/*=============================================
		ACTUALIZAR AVISOS
		=============================================*/
		$configuracionCorreoElectronicoAjax->perfilesCrear = $_POST["perfilesCrear"];
		$configuracionCorreoElectronicoAjax->estatusModificarUsuarioCreacion = $_POST["estatusModificarUsuarioCreacion"];
		$configuracionCorreoElectronicoAjax->estatusModificarPerfiles = $_POST["estatusModificarPerfiles"];
        $configuracionCorreoElectronicoAjax->uploadDocumentos = $_POST["uploadDocumentos"];
        $configuracionCorreoElectronicoAjax->usuarioUploadDocumento = $_POST["usuarioUploadDocumento"];
        $configuracionCorreoElectronicoAjax->perfilesUploadDocumento = $_POST["perfilesUploadDocumento"];
		$configuracionCorreoElectronicoAjax->actualizarAvisos();
		
	}

} else {

	/*=============================================
	TABLA DE MENSAJES
    =============================================*/
	$configuracionCorreoElectronicoAjax->mostrarTabla();

}
