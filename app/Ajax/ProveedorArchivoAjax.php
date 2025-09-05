<?php

namespace App\Ajax;

session_start();

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/control-costos/php_error_log');

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/ProveedorArchivos.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\ProveedorArchivos;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ProveedorArchivoAjax
{

	/*=============================================
	FUNCION PARA SUBIR LOS ARCHIVOS DEL PROVEEDOR
	=============================================*/
	public function subirArchivo()
	{
        try {
            $proveedorArchivos = New ProveedorArchivos;
            $proveedorArchivos->tipo = $this->tipo;
            $proveedorArchivos->proveedorId = $this->proveedorId;

            if(!$proveedorArchivos->insertarArchivos($_FILES["archivo"])){
                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'respuestaMessage' => "Error desconocido al agregar el archivo"
                ];
                echo json_encode($respuesta);
                die;
            }

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;

        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'respuestaMessage' => "Error al eliminar el archivo: " . $e->getMessage()
            ];
        }

        echo json_encode($respuesta);
	}

	/*=============================================
	FUNCION PARA ELIMINAR ARCHIVOS DEL PROVEEDOR
	=============================================*/
    public function eliminarArchivo()
    {
        try {

            $proveedorArchivos = new ProveedorArchivos();
            $proveedorArchivos->consultar(null,$this->archivoId);

            if(!$proveedorArchivos->eliminarArchivo()){
                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'respuestaMessage' => "Error desconocido al eliminar el archivo"
                ];
                echo json_encode($respuesta);
                die;
            }

            $respuesta = [
                'codigo' => 200,
                'error' => false,
                'respuestaMessage' => "El archivo fue eliminado correctamente."
            ];
        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'respuestaMessage' => "Error al eliminar el archivo: " . $e->getMessage()
            ];
        }

        echo json_encode($respuesta);
    }

    /*=============================================
	FUNCION AUTORIZAR ARCHIVO
	=============================================*/
    public function autorizarArchivo()
    {
        try {
            $proveedorArchivos = new ProveedorArchivos();
            $proveedorArchivos->id = $this->archivoId;
            $proveedorArchivos->observacion = $this->observacion;
            $proveedorArchivos->proveedorId = $this->proveedorId;

            if(!$proveedorArchivos->autorizarArchivo()){
                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'respuestaMessage' => "Error desconocido al autorizar el archivo"
                ];
                echo json_encode($respuesta);
                die;
            }

            $respuesta = [
                'codigo' => 200,
                'error' => false,
                'respuestaMessage' => "El archivo fue autorizar correctamente."
            ];
        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'respuestaMessage' => "Error al eliminar el archivo: " . $e->getMessage()
            ];
        }

        echo json_encode($respuesta);

    }

    public function rechazarArchivo()
    {
        try {
            
            $proveedorArchivos = new ProveedorArchivos();
            $proveedorArchivos->id = $this->archivoId;
            $proveedorArchivos->observacion = $this->observacion;
            $proveedorArchivos->proveedorId = $this->proveedorId;

            if(!$proveedorArchivos->rechazarArchivo()){
                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'respuestaMessage' => "Error desconocido al eliminar el archivo"
                ];
                echo json_encode($respuesta);
                die;
            }

            $respuesta = [
                'codigo' => 200,
                'error' => false,
                'respuestaMessage' => "El archivo fue rechazado correctamente."
            ];
        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'respuestaMessage' => "Error al eliminar el archivo: " . $e->getMessage()
            ];
        }

        echo json_encode($respuesta);

    }
}

$proveedorArchivosAjax = new ProveedorArchivoAjax;
if ( isset($_POST["accion"] ) ) {
    if ( $_POST["accion"] == "subirArchivos" ) {
        $proveedorArchivosAjax->proveedorId = $_POST["proveedorId"] ?? null;;
        $proveedorArchivosAjax->tipo = $_POST["tipo"];
        $proveedorArchivosAjax->subirArchivo();
    }else if ( $_POST["accion"] == "eliminarArchivo" ) {
        $proveedorArchivosAjax->archivoId = $_POST["archivoId"];
        $proveedorArchivosAjax->eliminarArchivo();
    }else if ( $_POST["accion"] == "autorizarArchivo" ) {
        $proveedorArchivosAjax->archivoId = $_POST["archivoId"];
        $proveedorArchivosAjax->observacion = $_POST["observacion"];
        $proveedorArchivosAjax->proveedorId = $_POST["proveedorId"];

        $proveedorArchivosAjax->autorizarArchivo();
    }else if ( $_POST["accion"] == "rechazarArchivo" ) {
        $proveedorArchivosAjax->archivoId = $_POST["archivoId"];
        $proveedorArchivosAjax->observacion = $_POST["observacion"];
        $proveedorArchivosAjax->proveedorId = $_POST["proveedorId"];
        $proveedorArchivosAjax->rechazarArchivo();
    }
}else {
    $respuesta = array();
    $respuesta['codigo'] = 400;
    $respuesta['error'] = true;
    $respuesta['mensaje'] = "Acción no válida.";

    echo json_encode($respuesta);
    die();
}

