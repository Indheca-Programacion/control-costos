<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Empresa.php";
require_once "../Controllers/Autorizacion.php";
// require_once "../Controllers/Validacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class EmpresaAjax
{

	/*=============================================
	TABLA DE EMPRESAS
	=============================================*/
	public function mostrarTabla()
	{
		$empresa = New Empresa;
        $empresas = $empresa->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "razonSocial" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "rfc" ]);
        array_push($columnas, [ "data" => "municipio" ]);
        array_push($columnas, [ "data" => "estado" ]);
        array_push($columnas, [ "data" => "pais" ]);
        array_push($columnas, [ "data" => "nomenclaturaOT" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($empresas as $key => $value) {
        	$rutaEdit = Route::names('empresas.edit', $value['id']);
        	$rutaDestroy = Route::names('empresas.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['razonSocial']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "razonSocial" => fString($value["razonSocial"]),
        							  "nombreCorto" => fString($value["nombreCorto"]),
        							  "rfc" => fString($value["rfc"]),
        							  "municipio" => fString($value["municipio"]),
        							  "estado" => fString($value["estado"]),
        							  "pais" => fString($value["pais"]),
        							  "nomenclaturaOT" => fString($value["nomenclaturaOT"]),
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
	AGREGAR MARCA
	=============================================*/	

	public $token;
	public $nombre;

	public function agregar(){

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "marcas", "crear") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a crear nuevas Marcas.";

	        }
        
        } else {

    	    $respuesta["error"] = true;
			$respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

		// Validar Token
		if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        // Validar Tamaño del campo
		if ( !Validacion::validar("nombre", $this->nombre, ['max', '30']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "El nombre debe ser máximo de 30 caracteres.";

        }

        if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$marca = New Marca;

		$datos["nombre"] = $this->nombre;

		// Validar campo (Marca, tamaño)

		$respuesta["respuesta"] = false;

		// Validar campo (que no exista en la BD)
		if ( $marca->consultar("nombre", $this->nombre) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Este nombre ya ha sido registrado.";

		} else {

			// Crear el nuevo registro
	        if ( $marca->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "La marca fue creada correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
	        	$respuesta["respuesta"] = $marca->consultar("nombre", $this->nombre);

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

/*=============================================
TABLA DE EMPRESAS
=============================================*/
// $activar = new TablaProductos();
// $activar -> mostrarTabla();
$activar = new EmpresaAjax();
$activar -> mostrarTabla();

/*=============================================
AGREGAR MARCA
=============================================*/
// if ( isset($_POST["nombreMarca"]) ) {

// 	$agregar = new EmpresaAjax();
// 	$agregar -> token = $_POST["_token"];
// 	$agregar -> nombre = $_POST["nombreMarca"];
// 	$agregar -> agregar();

// }
