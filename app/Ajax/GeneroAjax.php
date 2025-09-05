<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Genero.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveGenerosRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Genero;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveGenerosRequest;

class GeneroAjax
{

	/*=============================================
	TABLA DE INVENTARIOS
	=============================================*/
	public function mostrarTabla()
	{
		$genero = New Genero;
        $generos = $genero->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($generos as $key => $value) {
        	$rutaEdit = Route::names('generos.edit', $value['id']);
        	$rutaDestroy = Route::names('generos.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "descripcion" => mb_strtoupper(fString($value["descripcion"])),
        							  "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"])),
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

	/*=============================================
	AGREGAR GENERO
	=============================================*/
	public function agregar()
	{
		try {

			// Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generos", "crear") ) throw new \Exception("No está autorizado a crear nuevos subgeneros.");

            $request = SaveGenerosRequest::validated();

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }

			$genero = New Genero;

			$datos["descripcion"] = $this->descripcion;
			$datos["nombreCorto"] = '';

			// Validar campo (Descripcion, tamaño)

			$respuesta["respuesta"] = false;

			// Validar campo (que no exista en la BD)
			if ( $genero->consultar('descripcion', $this->descripcion) ) {

				$respuesta["error"] = true;
				$respuesta["errorMessage"] = "Esta descripcion ya ha sido registrada.";

			} else {

				// Crear el nuevo registro
				if ( $genero->crear($datos) ) {

					$respuesta["respuestaMessage"] = "El genero fue creada correctamente.";

					// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
					$respuesta["respuesta"] = $genero->consultar('descripcion', $this->descripcion);

					if ( !$respuesta["respuesta"] ) {

						$respuesta["error"] = true;
						$respuesta["errorMessage"] = "De favor refresque la pantalla para ver el nuevo registro.";

					}
					
				} else {

					$respuesta["error"] = true;
					$respuesta["errorMessage"] = "Hubo un error al intentar grabar el registro, intente de nuevo.";

				}

			}

		}  catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }
		echo json_encode($respuesta);
	}

}

try {

    $generoAjax = New GeneroAjax;

    if ( isset($_POST["descripcion"]) ) {

			$generoAjax->descripcion = $_POST["descripcion"];
            $generoAjax->agregar();

    } else {
        /*=============================================
		TABLA DE GENEROS
		=============================================*/
        $generoAjax->mostrarTabla();
    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}