<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Empleado.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class EmpleadoAjax
{

	/*=============================================
	TABLA DE EMPLEADOS
	=============================================*/
	public function mostrarTabla()
	{
		$empleado = New Empleado;
        $empleados = $empleado->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "activo" ]);
        array_push($columnas, [ "data" => "nombre" ]);
        array_push($columnas, [ "data" => "apellidoPaterno" ]);
        array_push($columnas, [ "data" => "apellidoMaterno" ]);
        array_push($columnas, [ "data" => "correo" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($empleados as $key => $value) {
        	$rutaEdit = Route::names('empleados.edit', $value['id']);
        	$rutaDestroy = Route::names('empleados.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['apellidoPaterno']));
            if ( !is_null($value['apellidoMaterno']) ) $folio .= ' ' . mb_strtoupper(fString($value['apellidoMaterno']));
            $folio .= ' ' . mb_strtoupper(fString($value['nombre']));

        	array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "activo" => ( $value["activo"] ) ? 'Si' : 'No',
                "nombre" => mb_strtoupper(fString($value["nombre"])),
                "apellidoPaterno" => mb_strtoupper(fString($value["apellidoPaterno"])),
                "apellidoMaterno" => mb_strtoupper(fString($value["apellidoMaterno"])),
                "correo" => fString($value["correo"]),
                "acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>" ] );
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
TABLA DE EMPLEADOS
=============================================*/
$empleadoAjax = new EmpleadoAjax;
$empleadoAjax->mostrarTabla();
