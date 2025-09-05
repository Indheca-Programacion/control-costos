<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Puesto.php";
require_once "../Models/PuestoUsuario.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SavePuestoUsuarioRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Puesto;
use App\Models\PuestoUsuario;

use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SavePuestoUsuarioRequest;

class PuestoAjax
{

	/*=============================================
	TABLA DE PUESTOS
	=============================================*/
	public function mostrarTabla()
	{
		try {
			
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "puesto", "ver") ) throw new \Exception("No está autorizado a agregar puestos");

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

			$puesto = New Puesto;
			$puestos = $puesto->consultar();

			$columnas = array();
			array_push($columnas, [ "data" => "consecutivo" ]);
			array_push($columnas, [ "data" => "nombreCorto" ]);
			array_push($columnas, [ "data" => "descripcion" ]);
			array_push($columnas, [ "data" => "acciones" ]);
			
			$token = createToken();
			
			$registros = array();

	
			foreach ($puestos as $key => $value) {
				

				$id_puesto = $value['id'];
				$rutaEdit = Route::names('puestos.edit', $value['id']);
				$rutaDestroy = Route::names('puestos.destroy', $value['id']);

				array_push( $registros, [ "consecutivo" => ($key + 1),
										"nombreCorto" => fString($value["nombreCorto"]),
										"descripcion" => fString($value["descripcion"]),
										"acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
														<form method='POST' action='{$rutaDestroy}' style='display: inline'>
															<input type='hidden' name='_method' value='DELETE'>
															<input type='hidden' name='_token' value='{$token}'>
																<button type='button' class='btn btn-xs btn-danger eliminar' puesto='{$id_puesto}'>
																	<i class='far fa-times-circle'></i>
																</button>
														</form>" ] );
			}

			
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['datos']['columnas'] = $columnas;
			$respuesta['datos']['registros'] = $registros;

			} catch (\Exception $e) {
				$respuesta = [
					'codigo' => 500, // Código de error para problemas del servidor
					'error' => true,
					'errorMessage' => $e->getMessage(), // El mensaje del error
					'errorCode' => $e->getCode() // Código específico de la excepción, si existe
				];
			}
			echo json_encode($respuesta);
	}

	/*=============================================
	TABLA DE PUESTOS USUARIOS:

	Muestra todos los registros donde se relacion puestos,
	 usuarios y ubicaciones.
	=============================================*/
	public function mostrarTablaPuestoUsuario(){

		try {
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "puesto", "ver") ) throw new \Exception("No está autorizado a agregar puestos");
	
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

			$puestoUsuario = New PuestoUsuario;
			$puestosUsuario = $puestoUsuario->consultar("idUsuario",$this->idUsuario);

			$columnas = array();
			array_push($columnas, [ "data" => "consecutivo" ]);
			array_push($columnas, [ "data" => "nombrePuesto" ]);
			array_push($columnas, [ "data" => "nombreObra" ]);
			array_push($columnas, [ "data" => "acciones" ]);

			$token = createToken();

			$registros = array();
			
			foreach ($puestosUsuario as $key => $value) {

				$idPuestoAsignado = $value['id'];
				$nombrePuesto = $value["nombrePuesto"];
				$nombreObra = $value["nombreObra"];

				$rutaEdit = Route::names('puestos.edit', $value['id']);
				$rutaDestroy = Route::names('puestos.destroy', $value['id']);

				array_push( $registros, [ "consecutivo" => ($key + 1),
										"nombrePuesto" => fString($nombrePuesto),
										"nombreObra" => fString($nombreObra),
										"acciones" => "
														<form method='POST' action='{$rutaDestroy}' style='display: inline'>
															<input type='hidden' name='_method' value='DELETE'>
															<input type='hidden' name='_token' value='{$token}'>
																<button type='button' class='btn btn-xs btn-danger eliminar'  nombrePuesto='{$nombrePuesto}' nombreUbicacion='{$nombreObra}'  idPuestoAsignado='{$idPuestoAsignado}'>
																	<i class='far fa-times-circle'></i>
																</button>
														</form>" ] );
			}

			
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['datos']['columnas'] = $columnas;
			$respuesta['datos']['registros'] = $registros;

		}catch (\Exception $e) {
			http_response_code(500); // Muy importante
            $respuesta = [
                'codigo' => 500, // Código de error para problemas del servidor
                'error' => true,
                'errorMessage' => $e->getMessage(), // El mensaje del error
                'errorCode' => $e->getCode() // Código específico de la excepción, si existe
            ];
        }

        echo json_encode($respuesta);

	}
	public function agregar(){
        try{

		if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

		$request = SavePuestoUsuarioRequest::validated();

		$usuario = New Usuario;
		$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
		if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "puesto", "crear") ) throw new \Exception("No está autorizado a agregar puestos");

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

		$puestoUsuario = New PuestoUsuario;

		// VERIFICAR SI YA EXISTE EL PUESTO ASIGNADO
		if(
			$puestoUsuario->consultar(
				"idUsuario",$request["idUsuario"],
				"idPuesto",$request["idPuesto"],
				"idObra",$request["idObra"]
				) 
		){
			$respuesta = array();
			$respuesta['codigo'] = 300;
			$respuesta['error'] = false;
			$respuesta['respuestaMessage'] = "Puesto asignado ya existe";

			echo json_encode($respuesta);
			return;

		}
		// CREAR PUESTO
		$respuesta = $puestoUsuario->crear($request);


		if($respuesta){
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['respuestaMessage'] = "Puesto asignado con exito";
		}

		}catch (\Exception $e) {
			http_response_code(500); // Muy importante
            $respuesta = [
                'codigo' => 500, // Código de error para problemas del servidor
                'error' => true,
                'errorMessage' => $e->getMessage(), // El mensaje del error
                'errorCode' => $e->getCode() // Código específico de la excepción, si existe
            ];
        }

        echo json_encode($respuesta);


	}

	/*=============================================
	ELIMINAR PUESTO USUARIO:

	Elimina el registro de la relación con el idPuestoUsuario
	obtenido del metodo POST.
	=============================================*/

	public function eliminarPuestoAsignado (){
		try{

			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$puestoUsuario = New PuestoUsuario;
			$puestoUsuario->id = $this->idPuestoAsignado;
			$respuesta = $puestoUsuario->eliminar();

			if($respuesta){
				$respuesta = array();
				$respuesta['codigo'] = 200;
				$respuesta['error'] = false;
				$respuesta['respuestaMessage'] = "Puesto eliminado con exito";
			}

		}catch (\Exception $e) {
			http_response_code(500); // Muy importante	
			$respuesta = [
				'codigo' => 500, // Código de error para problemas del servidor
				'error' => true,
				'errorMessage' => $e->getMessage(), // El mensaje del error
				'errorCode' => $e->getCode() // Código específico de la excepción, si existe
			];

		}
		echo json_encode($respuesta);
	}
}

$puestoAjax = new PuestoAjax();

if ( isset($_POST["accion"]) ) {

	if($_POST["accion"] == "asignarPuesto" ){
		/*=============================================
		ASIGNAR PUESTO
		=============================================*/ 
		$puestoAjax->agregar();

	}else if($_POST["accion"] == "eliminarPuestoAsignado" ){

		/*=============================================
		DESIGNAR PUESTO
		=============================================*/ 
		$puestoAjax->token = $_POST["_token"];
		$puestoAjax->idPuestoAsignado = $_POST["idPuestoAsignado"];

		$puestoAjax->eliminarPuestoAsignado();
	}

} 
else if ( isset($_GET["accion"]) ) {

    /*=============================================
    MOSTRAR TABLA DE PUESTOS DEL USUARIO
    =============================================*/ 
    $puestoAjax->idUsuario = $_GET["idUsuario"];
    $puestoAjax->mostrarTablaPuestoUsuario();
} 
else {

    /*=============================================
    TABLA DE PUESTOS
    =============================================*/
    $puestoAjax->mostrarTabla();

}