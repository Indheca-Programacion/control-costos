<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Permiso.php";
require_once "../Controllers/Autorizacion.php";
// require_once "../Controllers/Validacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Permiso;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class PermisoAjax
{

        /*=============================================
        TABLA DE PERMISOS
        =============================================*/
	public function mostrarTabla()
	{
        	$permiso = New Permiso;
                $permisos = $permiso->consultar();

        	$columnas = array();
                array_push($columnas, [ "data" => "consecutivo" ]);
                array_push($columnas, [ "data" => "codigo" ]);
                array_push($columnas, [ "data" => "nombre" ]);
                array_push($columnas, [ "data" => "descripcion" ]);
                array_push($columnas, [ "data" => "acciones" ]);
                
                $token = createToken();
                
                $registros = array();
                foreach ($permisos as $key => $value) {
                	$rutaEdit = Route::names('permisos.edit', $value['id']);
                	$rutaDestroy = Route::names('permisos.destroy', $value['id']);
                	$folio = mb_strtoupper(fString($value['nombre']));

                        array_push( $registros, [ "consecutivo" => ($key + 1),
        					  "codigo" => fString($value["codigo"]),
        					  "nombre" => fString($value["nombre"]),
        					  "descripcion" => fString($value["descripcion"]),
        					  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
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
TABLA DE PERMISOS
=============================================*/
$activar = new PermisoAjax();
$activar -> mostrarTabla();
