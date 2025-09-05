<?php

namespace App\Ajax;

session_start();

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

class DatosFiscalesAjax
{

	/*=============================================
	TABLA DE EMPLEADOS
	=============================================*/
	public function subirArchivo()
	{
        $proveedorArchivos = New ProveedorArchivos;
        $proveedorArchivos->tipo = $this->tipo;

        $response = $proveedorArchivos->insertarArchivos($_FILES["archivos"]);

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;

        echo json_encode($respuesta);
	}

    public function eliminarArchivo()
    {
        $proveedorArchivos = New ProveedorArchivos;
        $proveedorArchivos->id = $_POST["archivoId"];

        $response = $proveedorArchivos->eliminarArchivo();

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
        $respuesta["respuesta"] = true;

        echo json_encode($respuesta);
    }

    public function autorizarArchivo()
    {
        $proveedorArchivos = New ProveedorArchivos;
        $proveedorArchivos->id = $_POST["archivoId"];

        $response = $proveedorArchivos->autorizarArchivo();

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta["respuestaMessage"] = "El archivo fue autorizado correctamente.";
        $respuesta["respuesta"] = true;

        echo json_encode($respuesta);
    }

    public function rechazarArchivo()
    {
        $proveedorArchivos = New ProveedorArchivos;
        $proveedorArchivos->id = $_POST["archivoId"];

        $response = $proveedorArchivos->rechazarArchivo();

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta["respuestaMessage"] = "El archivo fue autorizado correctamente.";
        $respuesta["respuesta"] = true;

        echo json_encode($respuesta);
    }

}

/*=============================================
SUBIR ARCHIVOS
=============================================*/
$datosFiscalesAjax = new DatosFiscalesAjax;
if ( isset($_POST["accion"] ) ) {
    if ( $_POST["accion"] == "subirArchivos" ) {
        $datosFiscalesAjax->tipo = $_POST["tipo"];
        $datosFiscalesAjax->subirArchivo();
    }else if ( $_POST["accion"] == "eliminarArchivo" ) {
        $datosFiscalesAjax->eliminarArchivo();
    }else if ( $_POST["accion"] == "autorizarArchivo" ) {
        $datosFiscalesAjax->archivoId = $_POST["archivoId"];
        $datosFiscalesAjax->autorizarArchivo();
    }else if ( $_POST["accion"] == "rechazarArchivo" ) {
        $datosFiscalesAjax->archivoId = $_POST["archivoId"];
        $datosFiscalesAjax->rechazarArchivo();
    }
}else {
    $respuesta = array();
    $respuesta['codigo'] = 400;
    $respuesta['error'] = true;
    $respuesta['mensaje'] = "Acción no válida.";

    echo json_encode($respuesta);
    die();
}
