<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/GastosTipos.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\GastosTipos;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class GastosTiposAjax
{

	/*=============================================
	TABLA DE GASTOS
	=============================================*/
	public function mostrarTabla()
	{
		$gastoTipos = New GastosTipos;
        $filas = $gastoTipos->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($filas as $key => $value) {
        	$rutaEdit = Route::names('gastos-tipos.edit', $value['id']);
        	$rutaDestroy = Route::names('gastos-tipos.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "descripcion" => fString($value["descripcion"]),
        							  "nombreCorto" => fString($value["nombreCorto"]),
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

	public $token;

}

/*=============================================
TABLA DE EMPRESAS
=============================================*/
$gastos = new GastosTiposAjax();

if ( isset($_POST["costo"]) ) {

	/*=============================================
	AGREGAR TIPO DE INSUMO
	=============================================*/	
	$gastos->agregarPartidas();

} elseif ( isset($_GET["gasto"]) ) {
    /*=============================================
	AGREGAR TIPO DE INSUMO
	=============================================*/
    $gastos->gastoId = $_GET["gasto"];
	$gastos->obtenerPartidas();
} else {

	/*=============================================
	TABLA DE TIPOS DE INSUMOS
	=============================================*/
	$gastos -> mostrarTabla();

}
