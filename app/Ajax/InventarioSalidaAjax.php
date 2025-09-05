<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Requests/SaveInventariosRequest.php";
require_once "../Requests/SaveInventarioSalidasRequest.php";
require_once "../Models/Requisicion.php";
require_once "../Models/Partida.php";
require_once "../Models/Resguardo.php";

require_once "../Models/Usuario.php";
require_once "../Models/Inventario.php";
require_once "../Models/InventarioSalida.php";
require_once "../Models/InventarioDetalles.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Requisicion;
use App\Models\Partida;
use App\Models\Resguardo;
use App\Models\Inventario;
use App\Models\InventarioSalida;
use App\Models\InventarioDetalles;
use App\Requests\SaveInventariosRequest;
use App\Requests\SaveInventarioSalidasRequest;
use App\Controllers\Validacion;
use App\Controllers\Autorizacion;

class InventarioSalidaAjax
{

	/*=============================================
	TABLA DE INVENTARIOS
	=============================================*/
	public function mostrarTabla()
	{
		try {

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

			$inventario = New Inventario;
			
			$invenarioDetalles = new InventarioDetalles;
			$permiso = false;
			if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::perfil($usuario, 'supervisor-almacen') ) {
				$permiso = true;
			}
			$inventarios = $inventario->consultar(null,null,$permiso);
			$almacen = $invenarioDetalles->consultarInventarios(usuarioAutenticado()["id"], $permiso);

			$columnasInventario = array();
			array_push($columnasInventario, [ "data" => "consecutivo" ]);
			array_push($columnasInventario, [ "data" => "almacen" ]);
			array_push($columnasInventario, [ "data" => "cantidad" ]);
			array_push($columnasInventario, [ "data" => "unidad" ]);
			array_push($columnasInventario, [ "data" => "folio" ]);
			array_push($columnasInventario, [ "data" => "descripcion" ]);
			array_push($columnasInventario, [ "data" => "ordenCompra" ]);
			array_push($columnasInventario, [ "data" => "requisicion" ]);
			array_push($columnasInventario, [ "data" => "acciones" ]);

			$registroAlmacen = array();

			foreach ($almacen as $key => $value) {
				$rutaEdit = Route::names('inventarios.edit', $value['id']);
				$folio = mb_strtoupper(fString($value['ordenCompra']));

				array_push($registroAlmacen, [ "consecutivo" => ($key + 1),
											"almacen" => mb_strtoupper(fString($value["almacen.descripcion"])),
											"cantidad" => $value["cantidad"],
											"unidad" => mb_strtoupper(fString($value["unidad.descripcion"])),
											"folio" => mb_strtoupper(fString($value["folio"])),
											"descripcion" => mb_strtoupper(fString($value["descripcion"])),
											"ordenCompra" => mb_strtoupper(fString($value["ordenCompra"])),
											"requisicion" => mb_strtoupper(fString($value["requisicion"])),
											"acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"
											]);
			}

			$columnas = array();
			array_push($columnas, [ "data" => "consecutivo" ]);
			array_push($columnas, [ "data" => "folio" ]);
			array_push($columnas, [ "data" => "almacen" ]);
			array_push($columnas, [ "data" => "entrega" ]);
			array_push($columnas, [ "data" => "ordenCompra" ]);
			array_push($columnas, [ "data" => "creo" ]);
			array_push($columnas, [ "data" => "acciones" ]);
			
			$token = createToken();
			
			$registros = array();
			foreach ($inventarios as $key => $value) {
				$rutaEdit = Route::names('inventarios.edit', $value['id']);
				$rutaDestroy = Route::names('inventarios.destroy', $value['id']);
				$rutaPrint = Route::names('inventarios.print', $value['id']);
				$folio = mb_strtoupper(fString($value['ordenCompra']));

				array_push( $registros, [ "consecutivo" => ($key + 1),
										"folio" => mb_strtoupper(fString($value["folio"])),
										"almacen" => mb_strtoupper(fString($value["almacen.descripcion"])),
										"entrega" => mb_strtoupper(fString($value["entrega"])),
										"ordenCompra" => mb_strtoupper(fString($value["ordenCompra"])),
										"creo" => mb_strtoupper(fString($value["nombreCompleto"])),
										"acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
														<form method='POST' action='{$rutaDestroy}' style='display: inline'>
															<input type='hidden' name='_method' value='DELETE'>
															<input type='hidden' name='_token' value='{$token}'>
															<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
																<i class='far fa-times-circle'></i>
															</button>
														</form>
														<a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['datos']['columnas'] = $columnas;
			$respuesta['datos']['registros'] = $registros;
			$respuesta['datos']['columnasInventario'] = $columnasInventario;
			$respuesta['datos']['registroAlmacen'] = $registroAlmacen;

		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta);
	}

	/*=============================================
	CONSULTAR DETALLES DE INVENTARIO
	=============================================*/
	public function consultarDetalles()
	{
		$inventario = New InventarioSalida;
        $salidas = $inventario->consultarDetalles($this->inventarioId);


		$registrosSalidas = array();
		foreach ($salidas as $key => $value) {

        	array_push( $registrosSalidas, [ "consecutivo" => ($key + 1),
        							  "cantidad" => floatval($value["cantidad"]),
        							  "unidad" => mb_strtoupper(fString($value["unidad.descripcion"])),
        							  "numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
        							  "descripcion" => mb_strtoupper($value["descripcion"].' | '.$value["concepto"]) ] );
        }

		$columnas = array();
		array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "numeroParte" ]);
        array_push($columnas, [ "data" => "descripcion" ]);  
        
		
        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registrosSalidas;

        echo json_encode($respuesta);
	}

	/*=============================================
	FIRMAR SALIDA DE INVENTARIO
	=============================================*/
	public function firmarSalida()
	{
		try {
			
			$request = SaveInventarioSalidasRequest::validated();

			$inventarioSalida = New InventarioSalida;
			$resguardo = New Resguardo;

			$firma = substr($_POST["firma"], strpos($_POST["firma"], ',') + 1);
			// Decodificar los datos base64
			$firma = base64_decode($firma);
			// Nombre del archivo
			$directorio ='../../vistas/img/almacenes/recibe/';
			$filename =  fRandomNameFile($directorio, '.png');;
			if (!file_exists($directorio)) {
				mkdir($directorio, 0777, true);
			}
			// Guardar el archivo
			file_put_contents($filename, $firma);
			$request['firma'] = substr($filename,6);
			
			$inventarioSalida->id = $_POST["salida"];
			$resguardo->id = $_POST["salida"];


			$inventarioSalida->actualizar($request);
			$resguardo->actualizarConFirma($request);

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['mensaje'] = 'Se firmó con exito la salida';
			$respuesta['ruta'] = Route::names('inventarios.index');

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

try {

    $inventarioAjax = New InventarioSalidaAjax;

    if ( isset($_POST["accion"]) ) {

		if ( $_POST["accion"] == "firmarSalida" ) {
			$inventarioAjax->firmarSalida();
		} else {
			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => 'Acción no encontrada'
			];
		}
    
    } elseif ( isset($_GET["inventarioId"]) ) {
		/*=============================================
		TABLA DETALLES DE INVENTARIO
		=============================================*/
		$inventarioAjax->inventarioId = $_GET["inventarioId"];
        $inventarioAjax->consultarDetalles();
	} else {
        /*=============================================
		TABLA DE INVENTARIOS
		=============================================*/
        $inventarioAjax->mostrarTabla();
    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}