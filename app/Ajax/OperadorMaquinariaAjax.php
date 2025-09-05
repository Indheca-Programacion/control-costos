<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/OperadorMaquinaria.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\OperadorMaquinaria;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class OperadorMaquinariaAjax
{

	/*=============================================
	TABLA DE QR
	=============================================*/
	public function mostrarTabla()
	{
		$operador = New OperadorMaquinaria;
        $operadores = $operador->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "id" ]);
        array_push($columnas, [ "data" => "nombreOperador" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($operadores as $key => $value) {
        	$rutaEdit = Route::names('operadores.edit', $value['id']);
        	$rutaDestroy = Route::names('operadores.destroy', $value['id']);

            $folio = $value["id"];

        	array_push( $registros, [ 
                "id" =>  $value["id"],
                "nombreOperador" => $value["nombreOperador"],
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

    /*=============================================
	AGREGAR 
	=============================================*/	
	public $token;
	public $operadorMaquinaria;

	public function agregar(){

		$operadorMaquinaria = New OperadorMaquinaria;
		$datos["sNombre"] = $this->operadorMaquinaria;

		// Validar campo (que no exista en la BD)
		if ( $operadorMaquinaria->consultar("sNombre", $this->operadorMaquinaria)) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Este nombre ya ha sido registrada.";

		} else {

			// Crear el nuevo registro
	        if ( $operadorMaquinaria->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "El operador fue creada correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
				$operadorId = $operadorMaquinaria->consultar("sNombre", $this->operadorMaquinaria);
	        	$respuesta["respuesta"] = $operadorId["id"];
				
	        	if ( !$respuesta["respuesta"] ) {
	        		$respuesta["error"] = true;
					$respuesta["errorMessage"] = "De favor refresque la pantalla para ver el nuevo registro.";
	        	}
	        	
	        } else {

	        	$respuesta["error"] = true;
				$respuesta["errorMessage"] = "Hubo un error al intentar grabar el registro, intente de nuevo.";
	        }
		}
		echo json_encode($respuesta);

	}
}

$operadorMaquinariaAjax = new OperadorMaquinariaAjax;

	/*=============================================
	AGREGAR OPERADOR
	=============================================*/

if ( isset($_POST["operadorMaquinaria"]) ) {
	$operadorMaquinariaAjax->token = $_POST["_token"];
	$operadorMaquinariaAjax->operadorMaquinaria = $_POST["operadorMaquinaria"];
	$operadorMaquinariaAjax->agregar();

}else {
    
    $operadorMaquinariaAjax->mostrarTabla();
    
}

