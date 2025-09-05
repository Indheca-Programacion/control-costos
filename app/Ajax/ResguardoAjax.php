<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Resguardo.php";
require_once "../Models/ResguardoArchivo.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Resguardo;
use App\Models\ResguardoArchivo;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ResguardoAjax
{

	/*=============================================
	TABLA DE PERFILES
	=============================================*/
	public function mostrarTabla()
	{   
        try {

		$resguardo = New Resguardo;
        $resguardos = $resguardo->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "asignado" ]);
        array_push($columnas, [ "data" => "entrego" ]);
        array_push($columnas, [ "data" => "fechaAsignacion" ]);
        array_push($columnas, [ "data" => "observaciones" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();

        $registros = array();

        foreach ($resguardos as $key => $value) {

        	$rutaEdit = Route::names('resguardos.edit', $value['id']);
        	$rutaDestroy = Route::names('resguardos.destroy', $value['id']);
            $rutaPrint = Route::names('resguardos.print', $value['id']);
			$folio = $value['id'];

            array_push($registros, [
                "consecutivo" => ($key + 1),
                "asignado" => !empty($value["nombre.recibio"]) ? mb_strtoupper(fString($value["nombre.recibio"])) : "Sin usuario asignado",
                "entrego" => !empty($value["nombre.entrego"]) ? mb_strtoupper(fString($value["nombre.entrego"])) : "Sin usuario asignado",
                "fechaAsignacion" => !empty($value["fechaAsignacion"]) ? fFechaLargaHora($value["fechaAsignacion"]) : "Fecha no asignada",
                "observaciones" => !empty($value["observaciones"]) ? mb_strtoupper(fString($value["observaciones"])) : "Sin observaciones",
                "obra" => !empty($value["obra.descripcion"]) ? mb_strtoupper(fString($value["obra.descripcion"])) : "Sin obra asignada",
                "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                               <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
                               <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                               </form>"
            ]);
        
        }
        
        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        } catch (Exception $e) {

            $respuesta = array();
            $respuesta['codigo'] = 403;
            $respuesta['error'] = true;
            $respuesta['mensaje'] = $e->getMessage();
        }

        echo json_encode($respuesta);
	}
	public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "tarea-observaciones", "eliminar") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a eliminar Archivos.";

            }
        
        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

        // Validar Token
        if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        $resguardoArchivo = New ResguardoArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $resguardoArchivo->id = $this->archivoId;
        $resguardoArchivo->resguardo = $this->resguardoId;
        
        if ( !$resguardoArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $resguardoArchivo->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);

    }

    /*=============================================
	TABLA PARTIDAS DE LOS RESGUARDOS
	=============================================*/

    public $resguardoId;

	public function mostrarTablaPartidaResguardos()
	{
		$resguardo = New Resguardo;
        $resguardo->id = $this->resguardoId;
        $resguardos = $resguardo->partidasResguardo();

		$columnas = array();
        array_push($columnas, [ "data" => "id" ]);
        array_push($columnas, [ "data" => "concepto" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "numeroParte" ]);
        array_push($columnas, [ "data" => "partida" ]);
        

        $registros = array();
        foreach ($resguardos as $key => $value) {

        	array_push( $registros, [ "id" =>  mb_strtoupper(fString($value["id"])),
        							  "concepto" => mb_strtoupper(fString($value["descripcion"])),
        							  "cantidad" => mb_strtoupper(fString($value["cantidad"])),
        							  "unidad" => mb_strtoupper(fString($value["unidad"])),
        							  "numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
        							  "partida" => mb_strtoupper(fString($value["partida"]))
                                        ]);
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

}

/*=============================================
TABLA DE PERFILES
=============================================*/
$resguardo = new ResguardoAjax();

if (isset($_POST["accion"])){
	if ( $_POST["accion"] == "eliminarArchivo" && isset($_POST["archivoId"]) ) {

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $resguardo->token = $_POST["_token"];
        $resguardo->archivoId = $_POST["archivoId"];
        $resguardo->resguardoId = $_POST["resguardoId"];
        $resguardo->eliminarArchivo();

    }
} else if (isset($_GET["resguardoId"])){

    /*=============================================
    ELIMINAR ARCHIVO
    =============================================*/
    $resguardo->resguardoId = $_GET["resguardoId"];
    $resguardo->mostrarTablaPartidaResguardos();

}else {
	$resguardo->mostrarTabla();
}