<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Requests/SaveInventariosRequest.php";
require_once "../Requests/SaveInventarioSalidasRequest.php";
require_once "../Requests/SaveResguardoRequest.php";

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
use App\Requests\SaveResguardoRequest;
use App\Controllers\Validacion;
use App\Controllers\Autorizacion;

class InventarioAjax
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
											"folio" => mb_strtoupper(fString($value["inventario"])),
											"descripcion" => mb_strtoupper(fString($value["descripcion"])),
											"ordenCompra" => !empty($value["ordenCompra"]) ? mb_strtoupper(fString($value["ordenCompra"])) : "SIN ORDEN DE COMPRA",
											"requisicion" => !empty($value["requisicion"]) ? mb_strtoupper(fString($value["requisicion"])) : "SIN REQUISICIÓN",

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
										"folio" => mb_strtoupper(fString($value["id"])),
										"almacen" => mb_strtoupper(fString($value["almacen.descripcion"])),
										"entrega" => mb_strtoupper(fString($value["entrega"])),
										"ordenCompra" => !empty($value["ordenCompra"]) ? mb_strtoupper(fString($value["ordenCompra"])) : "SIN ORDEN DE COMPRA",
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

			// OBTENER SALIDAS
			$salida = new InventarioSalida;
			$salidas = $salida->consultar();

			$columnasSalidas = array();
			array_push($columnasSalidas, [ "data" => "consecutivo" ]);
			array_push($columnasSalidas, [ "data" => "folio" ]);
			array_push($columnasSalidas, [ "data" => "almacen" ]);
			array_push($columnasSalidas, [ "data" => "entrega" ]);
			array_push($columnasSalidas, [ "data" => "fechaEntrega" ]);
			array_push($columnasSalidas, [ "data" => "creo" ]);
			array_push($columnasSalidas, [ "data" => "acciones" ]);
						
			$registrosSalidas = array();
			foreach ($salidas as $key => $value) {
				$rutaEdit = Route::names('inventario-salidas.edit', $value['id']);
				$rutaDestroy = Route::names('inventario-salidas.destroy', $value['id']);
				$rutaPrint = Route::names('inventario-salidas.print', $value['id']);
				$rutaEntrada = Route::names('inventarios.edit', $value['entradaId']);

				$folio = mb_strtoupper(fString($value['ordenCompra']));

				array_push( $registrosSalidas, [ 
										"consecutivo" => ($key + 1),
										"folio" => mb_strtoupper(fString($value["entradaId"])),
										"almacen" => mb_strtoupper(fString($value["nombreAlmacen"])),
										"entrega" => mb_strtoupper(fString($value["nombreEntrega"])),
										"fechaEntrega" => fFechaLargaHora($value["fechaEntrega"]),
										"creo" => mb_strtoupper(fString($value["nombreRecibe"])),
										"acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
														<a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
														<a href='{$rutaEntrada}' target='_blank' class='btn btn-xs btn-success'><i class='fas fa-eye'></i></a>" 														
														]);
														
			}
			
		
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['datos']['columnas'] = $columnas;
			$respuesta['datos']['registros'] = $registros;
			$respuesta['datos']['columnasInventario'] = $columnasInventario;
			$respuesta['datos']['registroAlmacen'] = $registroAlmacen;
			$respuesta['datos']['columnasSalidas'] = $columnasSalidas;
			$respuesta['datos']['registrosSalidas'] = $registrosSalidas;




		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta);
	}

	public function consultarFiltros()
	{
		$arrayFiltros = array();

        if ( $this->almacenId > 0 ) array_push($arrayFiltros, [ "campo" => "A.id", "operador" => "=", "valor" => $this->almacenId ]);    
        if ( $this->descripcion !== '' ) array_push($arrayFiltros, [ "campo" => "lower(IV.descripcion)", "operador" => "like", "valor" => "'%".$this->descripcion."%'" ]);

		$inventario = New Inventario;
        $inventarios = $inventario->consultarFiltros($arrayFiltros);

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

        echo json_encode($respuesta);
	}

	/*=============================================
	CONSULTAR DETALLES DE INVENTARIO
	=============================================*/
	public function consultarDetalles()
	{
		$inventario = New InventarioDetalles;
		$inventario->inventario = $this->inventarioId;
        $inventarios = $inventario->consultarDetalles();
		$registrosDisponibles = $inventario->consultarDisponibles();

		$inventarioSalidas = New InventarioSalida;
        $salidas = $inventarioSalidas->consultarSalidas($this->inventarioId);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "cantidadDisponible" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "numeroParte" ]);
        array_push($columnas, [ "data" => "descripcion" ]);   
        array_push($columnas, [ "data" => "acciones" ]);   
        
        $registros = array();
        foreach ($inventarios as $key => $value) {
        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "cantidad" => floatval($value["cantidad"]),
        							  "cantidadDisponible" => $value["cantidad"] - ($value["cantidadSalidas"] ?? 0),
        							  "unidad" => mb_strtoupper(fString($value["unidad.descripcion"])),
        							  "numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
        							  "descripcion" => mb_strtoupper($value["descripcion"].' | '.$value["concepto"]),
									  "acciones" => "<button type='button' class='btn btn-xs btn-info btn-subirArchivo' id='{$value['id']}'><i class='fas fa-file-upload'></i></button> <button type='button' class='btn btn-xs btn-info verImagenes' data-toggle='modal' data-target='#modalVerImagenes' partida='{$value['id']}'><i class='fas fa-eye'></i></button>"
									  ] );
        }

		$disponibles = array();
		foreach ($registrosDisponibles as $key => $value) {
        	array_push( $disponibles, [ "consecutivo" => ($key + 1),
									  "partida" => $value["id"],
        							  "cantidad" => floatval($value["cantidad"]),
									  "cantidadDisponible" => $value["cantidad"] - ($value["cantidadSalidas"] ?? 0),
        							  "unidad" => mb_strtoupper(fString($value["unidad.descripcion"])),
        							  "numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
        							  "numeroParteDos" => mb_strtoupper(fString($value["numeroParte"])),

        							  "descripcion" => mb_strtoupper($value["descripcion"].' | '.$value["concepto"]),
									  "resguardo" => $value["resguardo"] ] );
        }

		$columnasSalidas = array();
		array_push($columnasSalidas, [ "data" => "consecutivo" ]);
		array_push($columnasSalidas, [ "data" => "folio" ]);
		array_push($columnasSalidas, [ "data" => "fecha_salida" ]);
		array_push($columnasSalidas, [ "data" => "recibe" ]);
		array_push($columnasSalidas, [ "data" => "estatus" ]);
		array_push($columnasSalidas, [ "data" => "acciones" ]);

		$registrosSalidas = array();
		foreach ($salidas as $key => $value) {
			$estatus = 'Entregado';
			if ( is_null($value["usuarioIdRecibe"]) ) $estatus = 'PENDIENTE DE RECEPCION';
			if ( is_null($value["usuarioIdAutoriza"]) ) $estatus = 'pendiente de autorizacion';
			
			$usuario = New Usuario;
            $usuario->consultar(null, usuarioAutenticado()["id"]);

			
			$partidas = $inventarioSalidas->consultarDetalles($value["id"]);
			$folio = $value["id"];
			
			$rutaEdit = Route::names('inventario-salidas.edit', $value['id']);
        	$rutaPrint = Route::names('inventario-salidas.print', $value['id']);

			$firmarBoton = "";
			if (is_null($value["usuarioIdRecibe"]) && !is_null($value["usuarioIdAutoriza"])) {
				$firmarBoton = "<button folio='{$value['id']}' type='button' class='btn btn-xs btn-success btn-firmar-salida' data-toggle='modal' data-target='#modalFirmarSalida'><i class='fas fa-file-signature'></i></button> ";
			}
			
        	array_push( $registrosSalidas, [ "consecutivo" => ($key + 1),
									  "folio" => $value["folio"],
									  "usuarioIdRecibe" => $value["usuarioIdRecibe"],
									  "usuarioIdAutoriza" => $value["usuarioIdAutoriza"],
									  "fecha_salida" => fFechaLarga($value["fechaCreacion"]) ,
									  "recibe" => mb_strtoupper(fString($value["recibe"])),	
									  "estatus" => mb_strtoupper(fString($estatus)),
									  "partidas" => $partidas,
									"acciones" => "
									<a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
									<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
									<button salidaId='{$folio}'  type='button' class='btn btn-xs btn-success resguardo' > 
									 	<i class='fas fa-angle-double-right'></i>
									</button>
									{$firmarBoton}"
									] ); 
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;
        $respuesta['salidas']['columnas'] = $columnasSalidas;
        $respuesta['salidas']['registros'] = $registrosSalidas;

		$respuesta['data'] = $disponibles;

        echo json_encode($respuesta);
	}

	/*=============================================
	CONSULTAR DETALLES DE LA TABLA INVENTARIO_SALIDAS
	=============================================*/
	public function consultarDetallesSalidas()
	{
		$inventario = New InventarioSalida;
		$inventario->id = $this->inventarioId;
        $inventarios = $inventario->consultarDetalles($this->inventarioId);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "partida" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "cantidadDisponible" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "numeroParte" ]);
        array_push($columnas, [ "data" => "descripcion" ]);   
     
        
        $registros = array();
        foreach ($inventarios as $key => $value) {
        	array_push( $registros, [ 
			"consecutivo" => "",
			"partida" => $value["partida"],
			"cantidad" => floatval($value["cantidad"]),
			"cantidadDisponible" => $value["cantidad"] - ($value["cantidadSalidas"] ?? 0),
			"unidad" => mb_strtoupper(fString($value["unidad.descripcion"])),
			"numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
			"descripcion" => mb_strtoupper($value["descripcion"].' | '.$value["concepto"]),
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
	CONSULTAR PARTIDAS PARA CREAR INVENTARIO
	=============================================*/
	public function consultarPartidas()
	{

		try {
			$inventarioDetalles = New InventarioDetalles;
			$partida = New Partida;

			$requisicion = New Requisicion;
			$requisicion->id = $this->requisicionId;
			$requisicion->consultarDetallesPartidasRequisicion();

			$columnas = array();
			array_push($columnas, [ "data" => "id" ]);
			array_push($columnas, [ "data" => "consecutivo" ]);
			array_push($columnas, [ "data" => "cantidad" ]);

			array_push($columnas, [ "data" => "unidad" ]);
			array_push($columnas, [ "data" => "numeroParte" ]);
			array_push($columnas, [ "data" => "descripcion" ]);
			$registros = array();
			foreach ($requisicion->detalles as $key => $value) {
				$partida->consultar(null,$value["id"]);
				array_push( $registros, [ "id" => $value["id"],
										"consecutivo" => ($key + 1),
										"directo" => $value["insumo.id"],
										"indirecto" => $value["indirecto.id"],
										"cantidad_disponible" => abs($value["cantidadInventario"] - $value["cantidadPartida"]),
										"cantidad" => abs($value["cantidadInventario"] - $value["cantidadPartida"]),
										"unidad" => mb_strtoupper(fString($value["unidad"])),
										"numeroParte" => "NA",
										"descripcion" => mb_strtoupper(fString($value["descripcion"].' | '.$value["concepto"])),
										] );
			}


			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['datos']['columnas'] = $columnas;
			$respuesta['datos']['registros'] = $registros;
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
	CREAR INVENTARIO
	=============================================*/
	public function guardar()
	{
		try {
			// Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "inventarios", "crear") ) throw new \Exception("No está autorizado a crear inventarios.");

			$request = SaveInventariosRequest::validated();

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

			$inventario = new Inventario;
			$inventarioDetalles = new InventarioDetalles;
			$partida = New Partida;

			$firma = substr($request["firma"], strpos($request["firma"], ',') + 1);
			// Decodificar los datos base64
			$firma = base64_decode($firma);
			// Nombre del archivo
			$directorio ='../../vistas/img/almacenes/';
			do {
				$filename =  fRandomNameFile($directorio, '.png');;
			} while ( file_exists($filename) );

			if (!file_exists($directorio)) {
				mkdir($directorio, 0777, true);
			}
			// Guardar el archivo
			file_put_contents($filename, $firma);
			$request["firma"]= substr($filename,6);
			// Crear el nuevo registro
			$request["fechaCreacion"] = fFechaSQL($request["fechaEntrega"]);

			$detalles = json_decode($request["detalles"],true);
			

            if ( !$inventario->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			$inventarioDetalles->inventario = $inventario->id;

			foreach ($detalles as $detalle) {
				$newValues = array();
				
				$detalle["partida"] = is_numeric($detalle["id"]) ? $detalle["id"] : null;

				$respuesta = $inventarioDetalles->crear($detalle);
			}

			// ACTUALIZAR EL ESTATUS SEGUN EL LA ENTRADA DEL INVENTARIO
			$requisicion = New Requisicion;
			$requisicion->id = $request["requisicionId"];

			$totalCantidadPartidas = $requisicion->totalCantidadPartidas();
			
			if($totalCantidadPartidas == 0 ){
				$requisicion->actualizarEstatusPorEntrada(true);
			}else {
				$requisicion->actualizarEstatusPorEntrada(false);
			}
			
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['ruta'] = Route::names('inventarios.index');
			$respuesta['respuestaMessage'] = 'Se creó con exito la entrada';

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
	CREAR SALIDA
	=============================================*/

	public function crearSalida()
	{
		try {
			
			$request = SaveInventarioSalidasRequest::validated();

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

			$inventario = new InventarioSalida;

			// CREAR SALIDA
			if ( !$inventario->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			// OBJETO RESGUARDO
			$resguardo = new Resguardo;

			// OBETENR ID DEL INVENTARIO PARA MANDAR AL RESGUARDO
			$request["inventario"] = $inventario->id;	

			// CREACION DEL RESGUARDO
			if ( !$resguardo->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
				
			// OBTENER DETALLES
			$detalles = json_decode($request["detalles"],true);

			// ARREGLO DATOS DE LOS RESGUARDADOS			
			$generaResguardo = [];
			
			// VALIDACION SI VA A RESGUARDO
			foreach ($detalles as $detalle) {

				// OBTIENE ID DEL INVENTARIO
				$detalle["inventario"] = $inventario->id;

				// INSERTA LOS CAMPOS
				$respuesta = $inventario->insertarDetalles($detalle);

				// VALIDACION SI LOS DIRECTOS O INDIRECTOS LLEVAN RESGUARDO
				if ($detalle["resguardo"] == 1) {

					// OBTENER DIRECTOS O INDIRECTOS QUE VAN A RESGUARDO
					$generaResguardo[] = [ 
						"partida" => $detalle["partida"],
						"descripcion" => $detalle["descripcion"]
					];
					
					// OBTENER EL ID DEL RESGUARDO Y ASIGNARLO A LOS DETALLES
					$detalle["inventario"] = $resguardo->id;
					
					// AGREGAR LOS DATOS A LA TABLA RESGUARDO_DETALLE
					$respuesta = $resguardo->insertarDetalles($detalle);

				}

			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['ruta'] = Route::names('inventarios.index');
			$respuesta['respuestaMessage'] = 'Se creó con exito la salida';
			$respuesta['generaResguardo'] = $generaResguardo;


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
	SUBIR ARCHIVOS
	=============================================*/

	public function subirArchivo()
	{
		try {
			$inventarioDetalles = New InventarioDetalles;
			$inventarioDetalles->insertarImagen($_POST["inventario_detalle"], $_FILES["archivos"]);

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['mensaje'] = 'Se subió con exito el archivo';

		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}

		echo json_encode($respuesta);
	}

	public function verImagenes()
	{
		try {
			$inventarioDetalles = New InventarioDetalles;
			$imagenes = $inventarioDetalles->consultarImagenes($_POST["partida"]);

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['imagenes'] = $imagenes;

		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}

		echo json_encode($respuesta);
	}

	public function crearEntrada()
	{
		try {
			$existencias = $_POST["existencias"];
			$entradas = array();

			foreach ($existencias as $existencia) {
				if ( !isset($entradas[$existencia["almacenId"]]) ) {
					$entradas[$existencia["almacenId"]] = array();
				}
				array_push($entradas[$existencia["almacenId"]], $existencia);
			}

			$inventario = new Inventario;
			$inventarioDetalles = new InventarioDetalles;

			foreach ($entradas as $key => $detalles) {

				$datos = [
					"observaciones" => $_POST["observaciones"],
					"almacen" => $key,
					"entrega" => "",
					"firma" => "",
					"requisicionId" => $_POST["requisicionId"],
					"fechaCreacion" => date("Y-m-d H:i:s")
				];

				if ( !$inventario->crear($datos) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

				foreach ($detalles as $key => $detalle) {
					$newValues = array();
					
					$detalle["indirecto"] = is_numeric($detalle["indirecto"]) ? $detalle["indirecto"] : null;
					$detalle["directo"] = is_numeric($detalle["directo"]) ? $detalle["directo"] : null;
					
					$inventarioDetalles->inventario = $inventario->id;
					
					$respuesta = $inventarioDetalles->crear($detalle);
				}
			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['mensaje'] = 'Se creó con exito la entrada';

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
	CREAR RESGUARDO
	=============================================*/
	public function crearResguardo()
	{
		try {

			$request = SaveResguardoRequest::validated();
			
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

			$resguardo = new Resguardo;

			$firma = substr($request["firma"], strpos($request["firma"], ',') + 1);
			// Decodificar los datos base64
			$firma = base64_decode($firma);
			// Nombre del archivo
			$directorio ='../../vistas/img/almacenes/recibe/';
			$filename =  fRandomNameFile($directorio, '.png');
			if (!file_exists($directorio)) {
				mkdir($directorio, 0777, true);
			}
			// Guardar el archivo
			file_put_contents($filename, $firma);
			$request["firma"]= substr($filename,6);

			// Crear el nuevo registro
			if ( !$resguardo->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			foreach ($this->detalles as $detalle) {

				$detalle["inventario"] = $resguardo->id;
				
				$respuesta = $resguardo->insertarDetalles($detalle);
			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['ruta'] = Route::names('inventarios.index');
			$respuesta['respuestaMessage'] = 'Se creó con exito el resguardo';

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

    $inventarioAjax = New InventarioAjax;

    if ( isset($_POST["accion"]) ) {

		if ( $_POST["accion"] == "guardar" ) {
            $inventarioAjax->guardar();
		} elseif ( $_POST["accion"] == "crearSalida" ) {
			$inventarioAjax->crearSalida();
		} elseif ( $_POST["accion"] == "subir-archivo" ) {
			$inventarioAjax->subirArchivo();
		} elseif ( $_POST["accion"] == "verImagenes" ) {
			$inventarioAjax->verImagenes();
		} elseif ( $_POST["accion"] == "crearEntrada" ) {
			$inventarioAjax->crearEntrada();
		} elseif ( $_POST["accion"] == "crearSalidaResguardo" ) {
			$detalles = json_decode($_POST["detalles"], true);
			$inventarioAjax->detalles = $detalles;
			$inventarioAjax->crearResguardo();
		} else {
			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => 'Acción no encontrada'
			];
		}
    
    } elseif (isset($_GET["descripcion"])) {
        /*=============================================
        CONSULTAR FILTROS
        =============================================*/
        $inventarioAjax->almacenId = $_GET["almacenId"];
        $inventarioAjax->descripcion = $_GET["descripcion"];
        $inventarioAjax->consultarFiltros();
    } elseif (isset($_GET["disponibles"])) {
		/*=============================================
		TABLA DE INVENTARIOS DISPONIBLES
		=============================================*/
        $inventarioAjax->consultarDisponibles();
	} elseif ( isset($_GET["requisicionId"]) ) {
		/*=============================================
		TABLA DE INVENTARIOS DISPONIBLES
		=============================================*/
		$inventarioAjax->requisicionId = $_GET["requisicionId"];
        $inventarioAjax->consultarPartidas();
	} elseif ( isset($_GET["inventarioId"]) ) {
		/*=============================================
		TABLA DETALLES DE INVENTARIO
		=============================================*/
		$inventarioAjax->inventarioId = $_GET["inventarioId"];
        $inventarioAjax->consultarDetalles();
	} elseif ( isset($_GET["inventarioSalidaId"]) ) {
		/*=============================================
		TABLA DETALLES DE INVENTARIO
		=============================================*/
		$inventarioAjax->inventarioId = $_GET["inventarioSalidaId"];
        $inventarioAjax->consultarDetallesSalidas();
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