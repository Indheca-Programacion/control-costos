<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Obra.php";
require_once "../Models/ObraDetalles.php";
require_once "../Requests/SaveObraDetallesRequest.php";
require_once "../Requests/SaveObrasRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Obra;
use App\Models\ObraDetalles;
use App\Requests\SaveObraDetallesRequest;
use App\Requests\SaveObrasRequest;
use App\Controllers\Autorizacion;
// use App\Controllers\Validacion;

class ObraAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
		$obra = New Obra;
        $obras = $obra->consultarObraActivas();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "periodos" ]);
        array_push($columnas, [ "data" => "fechaInicio" ]);
        array_push($columnas, [ "data" => "fechaFinalizacion" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($obras as $key => $value) {
        	$rutaEdit = Route::names('obras.edit', $value['id']);
        	$rutaDestroy = Route::names('obras.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));
        	$creo = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            if ( !is_null($value['usuarios.apellidoMaterno']) ) $creo .= ' ' . $value['usuarios.apellidoMaterno'];

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
				"descripcion" => mb_strtoupper(fString($value["descripcion"])),
				"nombreCorto" => mb_strtoupper(fString($value["nombreCorto"])),
				"estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
                "periodos" => $value["periodos"],
				"colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                "fechaInicio" => ( is_null($value["fechaInicio"]) ? '' : fFechaLarga($value["fechaInicio"]) ),
				"fechaFinalizacion" => ( is_null($value["fechaFinalizacion"]) ? '' : fFechaLarga($value["fechaFinalizacion"]) ),
				"creo" => mb_strtoupper(fString($creo)),
				"acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
								<form method='POST' action='{$rutaDestroy}' style='display: inline'>
									<input type='hidden' name='_method' value='DELETE'>
									<input type='hidden' name='_token' value='{$token}'>
									<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
										<i class='far fa-times-circle'></i>
									</button>
								</form>"
			] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
	/*=============================================
	AGREGAR DIRECTO/INDIRECTO
	=============================================*/
	public function crear()
	{
		try {
			// Validar Autorizacion
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "crear") ) throw new \Exception("No está autorizado a añadir Directos/Indirectos.");

			$request = SaveObraDetallesRequest::validated();
			
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

			$obra = new ObraDetalles;

			// Crear el nuevo registro
			if ( !$obra->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

			$respuesta = [
				'error' => false,
				'respuesta' => $obra,
				'respuestaMessage' => "Se ha agregado con exito."
			];
		}  catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }
		echo json_encode($respuesta);
		
	}
	public function addSemana()
	{
		try {
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "obras", "crear") ) throw new \Exception("No está autorizado a añadir Directos/Indirectos.");

			$request = SaveObrasRequest::validated();
			
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

			$obra = new Obra;

			// Crear el nuevo registro
			if ( !$obra->actualizarSemana($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

			$respuesta = [
				'error' => false,
				'respuesta' => $obra,
				'respuestaMessage' => "Se ha agregado con exito."
			];
		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }
		echo json_encode($respuesta);
	}
	public function crearPresupuesto()
	{
		try {
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "PROFORMAS", "crear") ) throw new \Exception("No está autorizado a crear Presupuestos.");

			$request = SaveObrasRequest::validated();
			
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

			$datos = array();

			$datos["descripcion"] = $_POST["descripcion"];
			$datos["obraId"] = $_POST["obraId"];

			$obra = new Obra;

			// Crear el nuevo registro
			if ( !$obra->crearPresupuesto($datos) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

			$respuesta = [
				'error' => false,
				'codigo' => 200,
				'respuestaMessage' => "Se ha agregado con exito."
			];
		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}
		echo json_encode($respuesta);
		
	}
	public function crearAnuncio()
	{
		try {
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "anuncios", "crear") ) throw new \Exception("No está autorizado a crear Anuncios.");

			$datos = array();
			$datos["mensaje"] = $_POST["mensaje"];
			$datos["obraId"] = $_POST["obraId"];
			$datos["fechaHora"] = date("Y-m-d H:i:s", strtotime($_POST["fechaHora"]));

			$obra = new Obra;

			// Crear el nuevo registro
			if ( !$obra->crearAnuncio($datos) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

			$respuesta = [
				'error' => false,
				'codigo' => 200,
				'respuestaMessage' => "Se ha agregado con exito.",
				'publicadoPor' => $usuario->nombreCompleto
			];
		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}
		echo json_encode($respuesta);
		
	}
	public function getPresupuestos()
	{
		try {
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$obraId = $_GET["obraId"] ?? null;
			if ( !$obraId ) throw new \Exception("Obra no válida.");

			$obra = new Obra;
			$obra->id = $obraId;
			$presupuestos = $obra->consultarLotes();

			$respuesta = [
				'error' => false,
				'codigo' => 200,
				'presupuestos' => $presupuestos
			];
		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}
		echo json_encode($respuesta);
	}
}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $obraAjax = New ObraAjax();

	if ( isset($_GET["accion"])) {
		if ( $_GET["accion"] == "getPresupuestos" ) {
			$obraAjax->getPresupuestos();
		}
	} else if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "agregar" ) {

            /*=============================================
            CREAR DETALLE DE OBRA
            =============================================*/
            $obraAjax->crear();

        } else if ( $_POST["accion"] == "agregarSemana" ) {
			/*=============================================
            AGREGAR SEMANA
            =============================================*/
            $obraAjax->addSemana();
		} else if ( $_POST["accion"] == "crearPresupuesto" ) {
			/*=============================================
			CREAR PRESUPUESTO
			=============================================*/
			$obraAjax->crearPresupuesto();
		} else if ( $_POST["accion"] == "crearAnuncio" ) {
			$obraAjax->crearAnuncio();
		} else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }
    }else{

        /*=============================================
        TABLA DE Obra
        =============================================*/
		$obraAjax->mostrarTabla();

    }


} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}


