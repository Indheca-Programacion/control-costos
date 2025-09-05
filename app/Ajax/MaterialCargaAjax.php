<?php

namespace App\Ajax;
use Exception;


session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/MaterialCarga.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\MaterialCarga;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class MaterialCargaAjax
{

	/*=============================================
	TABLA DE MATERIALES
	=============================================*/
	public function mostrarTabla()
	{
		$materialCargas = New MaterialCarga;
        $materiales = $materialCargas->consultar();


		$columnas = array();
        array_push($columnas, [ "data" => "id" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($materiales as $key => $value) {

        	$rutaEdit = Route::names('materiales.edit', $value['id']);
        	$rutaDestroy = Route::names('materiales.destroy', $value['id']);

            $folio = $value["id"];

        	array_push( $registros, [ 
                "id" => $value["id"],
                "descripcion" => $value["descripcion"],
                "acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>
								"
								
			]);
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

    /*=============================================
	AGREGAR MATERIAL
	=============================================*/	
	public $token;
	public $materialCarga;

	public function agregar(){

		try {
			$materialCarga = New MaterialCarga;
			$datos["sDescripcion"] = $this->materialCarga;
	
			// Validar campo (que no exista en la BD)
			if ( $materialCarga->consultar("sDescripcion", $this->materialCarga)) {
	
				$respuesta["error"] = true;
				$respuesta["errorMessage"] = "Este nombre ya ha sido registrada.";
				throw new Exception('Este nombre ya ha sido registrada.');
	
			} else {
	
				// Crear el nuevo registro
				if ( $materialCarga->crear($datos) ) {
	
					$respuesta["respuestaMessage"] = "El material fue creada correctamente.";

					// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
					$materialId = $materialCarga->consultar("sDescripcion", $this->materialCarga);

					$respuesta["respuesta"] = $materialId["id"];
					
					if ( !$respuesta["respuesta"] ) {
						$respuesta["error"] = true;
						$respuesta["errorMessage"] = "De favor refresque la pantalla para ver el nuevo registro.";
						throw new Exception('De favor refresque la pantalla para ver el nuevo registro.');

					}
					
				} else {
	
					$respuesta["error"] = true;
					$respuesta["errorMessage"] = "Hubo un error al intentar grabar el registro, intente de nuevo.";
					throw new Exception('Hubo un error al intentar grabar el registro, intente de nuevo.');

				}
			}
		} catch (Exception $e) {
			echo "Ocurrió un error: " . $e->getMessage();
		}

		echo json_encode($respuesta);

	}
}

$materialCargaAjax = new MaterialCargaAjax;

	/*=============================================
	AGREGAR MATERIAL
	=============================================*/

if ( isset($_POST["materialCarga"]) ) {
	$materialCargaAjax->token = $_POST["_token"];
	$materialCargaAjax->materialCarga = $_POST["materialCarga"];
	$materialCargaAjax->agregar();

}else {
    
    $materialCargaAjax->mostrarTabla();
    
}

