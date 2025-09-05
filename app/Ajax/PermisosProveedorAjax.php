<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/PermisoProveedor.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\PermisoProveedor;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class PermisosProveedorAjax
{

    /*=============================================
    Agregar Permisos
    =============================================*/
	public function agregarPermiso()
	{

        $permiso = new PermisoProveedor();
        $datos = [
            'titulo' => $_POST["tituloPermiso"],
            'proveedorId' => $_POST["proveedorId"],
        ];

        $archivos = $_FILES["archivos"];
        
        if(!$permiso->crear($datos, $archivos)) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Error al crear el permiso."
            ];
            echo json_encode($respuesta);
            return;
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;

        echo json_encode($respuesta);
	}

    /*=============================================
    Eliminar Permisos
    =============================================*/
    public function eliminarPermiso()
    {
        $permiso = new PermisoProveedor();
        $permiso->id = $_POST["permisoId"];
        $permiso->eliminar();

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;

        echo json_encode($respuesta);
    }

    /*=============================================
    Ver Archivos
    =============================================*/
    public function verArchivos()
    {
        $permiso = new PermisoProveedor();
        $permiso->id = $_POST["permisoId"];
        $archivos = $permiso->verArchivos();

        if ( count($archivos) == 0 ) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "No se encontraron archivos."
            ];
            echo json_encode($respuesta);
            return;
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['archivos'] = $archivos;

        echo json_encode($respuesta);
    }

    /*=============================================
    Guardar Archivos
    =============================================*/

    public function guardarArchivos()
    {
        $permiso = new PermisoProveedor();
        $permiso->id = $_POST["permisoId"];
        $archivos = $_FILES["archivos"];

        if ( count($archivos) == 0 ) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "No se encontraron archivos."
            ];
            echo json_encode($respuesta);
            return;
        }

        // Guardar los archivos
        $permiso->insertarArchivos($_FILES["archivos"]);

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;

        echo json_encode($respuesta);
    }

    /*=============================================
    Eliminar Archivos
    =============================================*/
    public function eliminarArchivos()
    {
        $permiso = new PermisoProveedor();
        $permiso->eliminarArchivos($_POST["archivoId"]);

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;

        echo json_encode($respuesta);
    }
    
    /*=============================================
    Autorizar Permiso
    =============================================*/
    public function autorizarPermiso()
    {
        $permiso = new PermisoProveedor();
        $permiso->id = $_POST["permisoId"];
        if(!$permiso->autorizar()) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Error al crear el permiso."
            ];
            echo json_encode($respuesta);
            return;
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['respuestaMessage'] = "El permiso fue autorizado correctamente.";

        echo json_encode($respuesta);
    }

    /*=============================================
    Rechazar Permiso
    =============================================*/
    public function rechazarPermiso()
    {
        $permiso = new PermisoProveedor();
        $permiso->id = $_POST["permisoId"];
        if(!$permiso->rechazar()) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Error al crear el permiso."
            ];
            echo json_encode($respuesta);
            return;
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['respuestaMessage'] = "El permiso fue rechazado correctamente.";

        echo json_encode($respuesta);
    }
}

/*=============================================
Agregar Permisos
=============================================*/
$permiso = new PermisosProveedorAjax();
if ( isset($_POST["accion"]) ) {

    if ( $_POST["accion"] == "agregarPermiso" ){
        /*=============================================
        Agregar Permiso
        =============================================*/
        $permiso->agregarPermiso();
    
    } else if ( $_POST["accion"] == "eliminarPermiso" ){
        /*=============================================
        Eliminar Permiso
        =============================================*/
        $permiso->eliminarPermiso();

    } else if ( $_POST["accion"] == "verArchivos" ){
        /*=============================================
        Ver Archivos
        =============================================*/
        $permiso->verArchivos();
    } else if ( $_POST["accion"] == "subirArchivos" ){
        /*=============================================
        Guardar Archivos
        =============================================*/
        $permiso->guardarArchivos();
    } else if ( $_POST["accion"] == "eliminarArchivo" ){
        /*=============================================
        Eliminar Archivos
        =============================================*/
        $permiso->eliminarArchivos();
    } else if ( $_POST["accion"] == "autorizarPermiso"){
        /*=============================================
        Autorizar Permiso
        =============================================*/
        $permiso->autorizarPermiso();
    } else if ( $_POST["accion"] == "rechazarPermiso"){
        /*=============================================
        Rechazar Permiso
        =============================================*/
        $permiso->rechazarPermiso();
    } else {

        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => "Realizó una petición desconocida."
        ];

        echo json_encode($respuesta);

    }
    
} 
