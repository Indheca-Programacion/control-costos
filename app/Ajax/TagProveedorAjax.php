<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/TagProveedor.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\TagProveedor;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class TagProveedorAjax
{

	/*=============================================
	TABLA DE TAGS DE INFORMACION TECNICA
	=============================================*/
	public function mostrarTabla()
	{
		$tagProveedor = New TagProveedor;
        $tagProveedores = $tagProveedor->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "orden" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($tagProveedores as $key => $value) {
        	$rutaEdit = Route::names('tags-proveedores.edit', $value['id']);
        	$rutaDestroy = Route::names('tags-proveedores.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "descripcion" => fString($value["descripcion"]),
        							  "nombreCorto" => fString($value["nombreCorto"]),
        							  "orden" => $value["orden"],
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

$tagProveedorAjax = New TagProveedorAjax;

$tagProveedorAjax->mostrarTabla();
