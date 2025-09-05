<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Perfil.php";
require_once "../Controllers/Autorizacion.php";
// require_once "../Controllers/Validacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Perfil;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class PerfilAjax
{

	/*=============================================
	TABLA DE PERFILES
	=============================================*/
	public function mostrarTabla()
	{
		$perfil = New Perfil;
        $perfiles = $perfil->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "nombre" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();

        $registros = array();
        foreach ($perfiles as $key => $value) {
        	$rutaEdit = Route::names('perfiles.edit', $value['id']);
        	$rutaDestroy = Route::names('perfiles.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['nombre']));

			if ( mb_strtolower($value["nombre"]) != mb_strtolower(CONST_ADMIN) ) {

        	array_push( $registros, [ "consecutivo" => ($key + 1),
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

        	} else {

        		array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "nombre" => fString($value["nombre"]),
        							  "descripcion" => fString($value["descripcion"]),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>" ] );

        	}
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
$activar = new PerfilAjax();
$activar -> mostrarTabla();
