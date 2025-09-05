<?php

namespace App;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

class Route{

	/**
	 * FUNCIÓN ENCARGADA DE RETORNAR LA VARIABLE GLOBAL DE LA RUTA
	 * 
	 * @return string  LA VARIABLE GLOBAL DE LA RUTA
	 */
	static public function ruta()
	{
		return CONST_RUTA;
	}

	/**
	 * FUNCIÓN ENCARGADA DE RETORNAR LA VARIABLE GLOBAL DEL SERVIDOR
	 * Y SI LA RUTA ES PROVEEDOR RETORNA LA RUTA DEL SERVIDOR DEL PROVEEDOR 
	 *
	 * @return string  LA VARIABLE GLOBAL DEL SERVIDOR
	 */
	static public function rutaServidor()
	{
		if (strpos($_SERVER['HTTP_HOST'], 'proveedor.') === 0) {
			return CONST_RUTA_SERVIDOR_PROVEEDOR;
		}else {
			return CONST_RUTA_SERVIDOR;
		}
	}

	/**
	 * FUNCIÓN ENCARGADA DE RETORNAL LA VARIABLE GLOBAL DE LA RUTA DEL SERVIDOR
	 * DEL PROVEEDOR
	 *
	 * @return string   RUTA DEL SERVIDOR PROVEEDOR
	 */
	static public function rutaServidorProveedor()
	{
		return CONST_RUTA_SERVIDOR_PROVEEDOR;
	}

	/**
	 * Genera una URL basada en una ruta con formato "controlador.metodo" y un parámetro opcional.
	 *
	 * Este método se utiliza para construir rutas del lado del servidor que siguen una convención
	 *
	 * @param string $valorRuta   Ruta en formato "controlador.metodo" (por ejemplo: "usuarios.edit").
	 * @param mixed  $parametro   Parámetro adicional (por ejemplo, un ID) utilizado en algunas rutas.
	 *
	 * @return string   La URL generada según el método especificado o null si no se reconoce el método.
	 */
	static public function names($valorRuta = null, $parametro = null)
	{
		$rutaArray = explode(".", $valorRuta);

		$controlador = $rutaArray[0];
		$metodo = $rutaArray[1];

		switch ($metodo) {

		    case "index":
		        return self::rutaServidor() . $controlador;
		        break;
		    case 'create':
		    	return self::rutaServidor() . $controlador. "/crear";
		        break;
		    case 'store':
		    	return self::rutaServidor() . $controlador;
		        break;
		    case 'edit':
		    	return self::rutaServidor() . $controlador ."/". $parametro ."/editar";
		        break;
			case 'update':
		    	return self::rutaServidor() . $controlador ."/". $parametro;
		        break;
		    case 'destroy':
		    	return self::rutaServidor() . $controlador . "/". $parametro;
		        break;
		    case 'changeStatus':
		    	return self::rutaServidor() . $controlador . "/". $parametro ."/estatus";
		        break;
		    case 'print':
		    	return self::rutaServidor() . $controlador . "/". $parametro ."/imprimir";
		        break;
			case 'upload':
		    	return self::rutaServidor() . $controlador . "/". $parametro ."/upload";
		        break;
		    default:
				return null;

		}
	}

	/**
	 * Genera una URL completa basada en una ruta semántica definida por el sistema.
	 *
	 * Este método permite construir URLs para secciones específicas de la aplicación,
	 * como autenticación, perfil, configuraciones, y acciones con parámetros dinámicos.
	 *
	 * @param string|null $valorRuta   Nombre de la ruta (por ejemplo: "perfil", "resset-password.validation-code").
	 * @param mixed|null  $parametro   Parámetro opcional que se inserta en rutas dinámicas (por ejemplo, un token o ID).
	 *
	 * @return string|null  URL completa generada con base en la ruta proporcionada, o null si la ruta no existe.
	 */
	static public function routes($valorRuta = null, $parametro = null)
	{
		switch ($valorRuta) {
		    case "inicio":
		        return self::rutaServidor() . "inicio";
		        break;
			case "ingreso":
		        return self::rutaServidor() . "ingreso";
		        break;
			case "resset-password":
				return self::rutaServidor() . "resset-password";
				break;
			case "resset-password.validation-code":
				return self::rutaServidor() . "resset-password/{$parametro}/validation-code";
			break;
			case "resset-password.change-password":
				return self::rutaServidor() . "resset-password/{$parametro}/change-password";
			break;
			case "perfil":
		        return self::rutaServidor() . "perfil";
		        break;
		    case "salir":
		    	return self::rutaServidor() . "salir";
		        break;
		    case "requisiciones.crear-orden-compra":
		    	return self::rutaServidor() . "requisiciones/{$parametro}/crear-orden-compra";
		        break;
		    case "requisiciones.crear-cotizacion":
		    	return self::rutaServidor() . "requisiciones/{$parametro}/crear-cotizacion";
		        break;
			case 'informacion-tecnica.download':
				return self::rutaServidor() . "informacion-tecnica/{$parametro}/download";
				break;
	
			case 'requisiciones.downloadComprobantes':
				return self::rutaServidor() . "requisiciones/{$parametro}/download/comprobantes";
				break;
			case 'requisiciones.downloadOrdenes':
				return self::rutaServidor() . "requisiciones/{$parametro}/download/ordenes";
				break;

			case 'inventarios.crear':
				return self::rutaServidor(). "inventarios/{$parametro}/crear";
				break;

			case "configuracion-requisiciones":
				return self::rutaServidor() . "configuracion-requisiciones";
				break;

			case "configuracion-ordenes-compra":
				return self::rutaServidor() . "configuracion-ordenes-compra";
				break;
	

			case "configuracion-correo-electronico":
				return self::rutaServidor() . "configuracion-correo-electronico";
				break;
			default:
				return null;
		}
	}

	// Funcion que permite ejecutar los metodos del controlador
	static function execute($rutas, $controlador)
	{
		switch ( count($rutas) ) {
	
		    case 1:
	
		    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
	
		    		$controlador -> index();
	
		        } elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
	
		        	$controlador -> store();
	
		        }
	
		        break;
	
		    case 2:
	
		    	$param1 = $rutas[1];
	
		    	if ( $param1 == "crear" ) {
	
			        $controlador -> create();
	
		    	} elseif ( Requests\Request::method() === "PUT" ) {
	
		    		$controlador -> update($param1);
	
		    	} elseif ( Requests\Request::method() === "DELETE" ) {
	
		    		$controlador -> destroy($param1);
	
		    	} else {
	
		    		$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            		include "vistas/modulos/plantilla.php";
	
		    	}
	
		        break;
	
		    case 3:
	
		    	$param1 = $rutas[1];
		    	$param2 = $rutas[2];
	
		    	if ( $param2 == "crear" ) {

					$controlador -> create($param1);

				} else
				if ( $param2 == "editar" ) {
	
			        $controlador -> edit($param1);
	
		    	} elseif ( $param2 == "estatus" && $_SERVER['REQUEST_METHOD'] === "POST" ) {

		    		$controlador -> changeStatus($param1);

		    	} elseif ( $param2 == "imprimir" ) {

		    		$controlador -> print($param1);

		    	} elseif ( $param2 == "download" ) {

		    		$controlador -> download($param1);

		    	} elseif ( $param2 == "upload" ) {

		    		$controlador -> upload($param1);

		    	} else {
	
		    		$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            		include "vistas/modulos/plantilla.php";
	
		    	}
	
		    	break;
	
			default:
	
				$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            	include "vistas/modulos/plantilla.php";
	
		}
	}

	static public function index()
	{ 	
		$rutas = array();
		$ruta = null;

	    if ( isset($_GET["ruta"]) ) {

			$rutas = explode("/", $_GET["ruta"]);
			
			$rutasFinal = "";
			foreach ($rutas as $indice => $valor) {
			    
			    if ( $valor != "" ) {

			    	if ( $indice != 0 ) {

			    		$rutasFinal .= "/";

			    	}

			    	$rutasFinal .= $valor;

				} 

			}

			$rutas = explode("/", $rutasFinal);

	    	$ruta = $rutas[0];

			if ( $ruta == "registro-proveedor") {
				// $contenido = array('modulo' => 'vistas/modulos/proveedores/registro.php');
            	include "vistas/modulos/proveedores/registro.php";
				exit;
			}

			if ($ruta == "inicio" ||

				$ruta == "costos-resumen" ||
				$ruta == "empleados" ||
				$ruta == "estatus" ||
				$ruta == "unidades" ||
				$ruta == "insumo-tipos" ||
				$ruta == "insumos" ||
				$ruta == "indirecto-tipos" ||
				$ruta == "indirectos" ||
				$ruta == "insumos-indirectos" ||
				$ruta == "obras" ||
				$ruta == "obras-detalles" ||

				$ruta == "requisiciones" ||
				$ruta == "partidas" ||
				$ruta == "configuracion-requisiciones" ||
				$ruta == "configuracion-ordenes-compra" ||
				$ruta == "configuracion-correo-electronico" ||

				$ruta == "empresas" ||
				$ruta == "sucursales" ||
		        $ruta == "usuarios" ||
		        $ruta == "perfiles" ||
		        $ruta == "permisos" ||
		        $ruta == "ubicaciones" ||

				$ruta == "puestos" ||

		        $ruta == "asistencias" ||
				$ruta == "requisicion-personal" ||
				$ruta == "nominas" ||
				$ruta == "nom35" ||

		        $ruta == "ingreso" ||
		        $ruta == "resset-password" ||

		        $ruta == "perfil" ||

		        $ruta == "resguardos" ||
		        $ruta == "inventarios" ||
				$ruta == "inventario-salidas" ||
		        $ruta == "almacenes" ||
				

				$ruta == "tareas" ||
				$ruta == "tarea-observaciones" ||

		        $ruta == "gastos" ||
		        $ruta == "gasto-detalles" ||
		        $ruta == "gastos-tipos" ||

		        $ruta == "plantillas" ||
		        $ruta == "plantilla-detalles" ||

				$ruta == "informacion-tecnica-tags" ||
				$ruta == "informacion-tecnica-tags" ||
				$ruta == "informacion-tecnica" ||
				$ruta == "sgi" ||
				$ruta == "proveedores" ||
				$ruta == "tags-proveedores" ||

				$ruta == "orden-compra" ||

				/*------------------------------
				| RUTAS CATEGORIA PROVEEDOR
				------------------------------*/

				$ruta == "categoria-proveedores" ||
				$ruta == "categoria-permiso-proveedor" ||
				$ruta == "permiso-proveedor" ||
				$ruta == "solicitud-proveedor" ||

				/*------------------------------
				| RUTAS INGRESO CARGAS
				------------------------------*/
				
				$ruta == "qr-cargas" ||
				$ruta == "maquinaria-traslados" ||
				$ruta == "maquinaria" ||
				$ruta == "operadores" ||
				$ruta == "cargas" ||
				$ruta == "materiales" ||
				$ruta == "movimientos" ||
				$ruta == "nota-informativa" ||
				$ruta == "programacion-pagos" ||

				/*------------------------------
				| ORDENES COMPRA GLOBALES
				------------------------------*/
				$ruta == "requisiciones-orden-compra-globales" ||
				$ruta == "orden-compra-globales" ||
				$ruta == "categoria-ordenes" ||

				/*------------------------------
				| ORDENES DE COMPRA DE CENTRO DE SERVICIOS
				------------------------------*/
				$ruta == "orden-compra-centro-servicios" ||

		    	$ruta == "salir" || $ruta == "politicas") {

				switch ( $ruta ) {
					/*
					| RUTAS ORDENES DE COMPRA DE CENTRO DE SERVICIOS
					*/
					case "orden-compra-centro-servicios":
						require_once "app/Controllers/OrdenCompraCentroServiciosController.php";
						self::execute($rutas, new \App\Controllers\OrdenCompraCentroServiciosController);
						break;
					case "categoria-ordenes":
						require_once "app/Controllers/CategoriaOrdenesController.php";
						self::execute($rutas, new \App\Controllers\CategoriaOrdenesController);
						break;
					case "programacion-pagos":
						require_once "app/Controllers/ProgramacionPagosController.php";
						self::execute($rutas, new \App\Controllers\ProgramacionPagosController);
						break;
					case "nota-informativa":
						require_once "app/Controllers/NotaInformativaController.php";
						self::execute($rutas, new \App\Controllers\NotaInformativaController);
						break;
					case "partidas":
						require_once "app/Controllers/PartidasController.php";
						self::execute($rutas, new \App\Controllers\PartidasController);
						break;

					case "qr-cargas":
						require_once "app/Controllers/QrCargasController.php";
						self::execute($rutas, new \App\Controllers\QrCargasController);
						break;
					case "maquinaria-traslados":
						require_once "app/Controllers/MaquinariaTrasladosController.php";
						self::execute($rutas, new \App\Controllers\MaquinariaTrasladosController);
						break;
					case "maquinaria":
						require_once "app/Controllers/MaquinariaController.php";
						self::execute($rutas, new \App\Controllers\MaquinariaController);
						break;
					case "operadores":
						require_once "app/Controllers/OperadoresMaquinariasController.php";
						self::execute($rutas, new \App\Controllers\OperadoresMaquinariasController);
						break;
					case "cargas":	
						require_once "app/Controllers/CargasController.php";
						self::execute($rutas, new \App\Controllers\CargasController);
						break;
					case "materiales":
						require_once "app/Controllers/MaterialesCargasController.php";
						self::execute($rutas, new \App\Controllers\MaterialesCargasController);
						break;
					case "movimientos":
						require_once "app/Controllers/MovimientosController.php";
						self::execute($rutas, new \App\Controllers\MovimientosController);
						break;
					case "nom35":
						require_once "app/Controllers/Nom35Controller.php";
						self::execute($rutas, new \App\Controllers\Nom35Controller);
						break;
					case "orden-compra":
						require_once "app/Controllers/OrdenCompraController.php";
						self::execute($rutas, new \App\Controllers\OrdenCompraController);
						break;
					case "inventario-salidas":
						require_once "app/Controllers/InventarioSalidasController.php";
						self::execute($rutas, new \App\Controllers\InventarioSalidasController);
						break;
					case "proveedores":
						require_once "app/Controllers/ProveedoresController.php";
						self::execute($rutas, new \App\Controllers\ProveedoresController);
						break;
					case "sgi":
						require_once "app/Controllers/SgiController.php";
						self::execute($rutas, new \App\Controllers\SgiController);
						break;
					case "politicas":
						require_once "app/Controllers/PoliticasController.php";
						self::execute($rutas, new \App\Controllers\PoliticasController);
						break;
					case "informacion-tecnica-tags":
						require_once "app/Controllers/InformacionTecnicaTagsController.php";
						self::execute($rutas, new \App\Controllers\InformacionTecnicaTagsController);
						break;
					case "informacion-tecnica":
						require_once "app/Controllers/InformacionTecnicaController.php";
						self::execute($rutas, new \App\Controllers\InformacionTecnicaController);
						break;
					case "generos":
						require_once "app/Controllers/GenerosController.php";
						self::execute($rutas, new \App\Controllers\GenerosController);
						break;
					case "subgeneros":
						require_once "app/Controllers/SubGenerosController.php";
						self::execute($rutas, new \App\Controllers\SubGenerosController);
						break;
					case "almacenes":
						require_once "app/Controllers/AlmacenesController.php";
						self::execute($rutas, new \App\Controllers\AlmacenesController);
						break;
					case "plantillas":
						require_once "app/Controllers/PlantillasController.php";
						self::execute($rutas, new \App\Controllers\PlantillasController);
						break;
					case "plantilla-detalles":
						require_once "app/Controllers/PlantillaDetallesController.php";
						self::execute($rutas, new \App\Controllers\PlantillaDetallesController);
						break;
					case "tarea-observaciones":
						require_once "app/Controllers/TareaObservacionesController.php";
						self::execute($rutas, new \App\Controllers\TareaObservacionesController);
						break;
					case "tareas":
						require_once "app/Controllers/TareasController.php";
						self::execute($rutas, new \App\Controllers\TareasController);
						break;
					case "inventarios":
						require_once "app/Controllers/InventariosController.php";
						self::execute($rutas, new \App\Controllers\InventariosController);
					break;
					case "gastos-tipos":
						require_once "app/Controllers/GastosTiposController.php";
						self::execute($rutas, new \App\Controllers\GastosTiposController);
					break;
					case "gastos":
						require_once "app/Controllers/GastosController.php";
						self::execute($rutas, new \App\Controllers\GastosController);
						
					break;
					case "gasto-detalles":
						require_once "app/Controllers/GastoDetallesController.php";
						self::execute($rutas, new \App\Controllers\GastoDetallesController);
						
					break;
					case "resguardos":
						require_once "app/Controllers/ResguardosController.php";
						self::execute($rutas, new \App\Controllers\ResguardosController);
						
					break;
					case "asistencias":
						require_once "app/Controllers/AsistenciasController.php";
						self::execute($rutas, new \App\Controllers\AsistenciasController);
					break;
					case "requisicion-personal":
						require_once "app/Controllers/RequisicionPersonalController.php";
						self::execute($rutas, new \App\Controllers\RequisicionPersonalController);
	
					break;
					case "empleados":

						require_once "app/Controllers/EmpleadosController.php";
						self::execute($rutas, new \App\Controllers\EmpleadosController);
						break;

					case "ubicaciones":
						require_once "app/Controllers/UbicacionesController.php";
						self::execute($rutas, new \App\Controllers\UbicacionesController);
						break;
					case "tags-proveedores":
						require_once "app/Controllers/TagsProveedoresController.php";
	
						self::execute($rutas, new \App\Controllers\TagsProveedoresController);
	
					break;
					case "nominas":
						require_once "app/Controllers/NominasController.php";

						self::execute($rutas, new \App\Controllers\NominasController);

					break;
					case "configuracion-requisiciones":

						require_once "app/Controllers/ConfiguracionRequisicionesController.php";

						$controlador = new \App\Controllers\ConfiguracionRequisicionesController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> edit(1);

						    	} elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						    		$controlador -> update(1);
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}


					break;

					case "configuracion-ordenes-compra":

						require_once "app/Controllers/ConfiguracionOrdenesCompraController.php";

						$controlador = new \App\Controllers\ConfiguracionOrdenesCompraController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> edit(1);

						    	} elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						    		$controlador -> update(1);
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}


					break;

					case "puestos":

						require_once "app/Controllers/PuestosController.php";	
						self::execute($rutas, new \App\Controllers\PuestosController);
	
					break;	

					case "configuracion-correo-electronico":

						require_once "app/Controllers/ConfiguracionCorreoElectronicoController.php";

						$controlador = new \App\Controllers\ConfiguracionCorreoElectronicoController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> edit(1);

						    	} elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						    		$controlador -> update(1);
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}

						break;
					case "obras-detalles":
						require_once "app/Controllers/ObrasDetallesController.php";

						self::execute($rutas, new \App\Controllers\ObrasDetallesController);
						break;

					case "ingreso":

						require_once "app/Controllers/LoginController.php";

						$controlador = new \App\Controllers\LoginController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> index();
					
						        } elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						        	$controlador -> login();
						        	
						        } else {

						        	header("Location:" . Route::routes('inicio'));
                    				die();

						        }
					
						        break;
					
							default:

								header("Location:" . Route::routes('inicio'));
                    			die();
					
						}

						break;

					case "resset-password":

						require_once "app/Controllers/RessetPasswordController.php";
						$controlador = new \App\Controllers\RessetPasswordController();

				

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> index();

						    	} elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						    		$controlador -> ressetPassword();
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
								
							case 3:						    	
								$param1 = $rutas[1];
								$param2 = $rutas[2];
								
								if ($param2 === "validation-code") {
									if ($_SERVER['REQUEST_METHOD'] === "GET") {
										$controlador->validationCodeView($param1);
									} elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
										$controlador->validationCode();
									} else {
										$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
										include "vistas/modulos/plantilla.php";
									}
								
								} elseif ($param2 === "change-password") {
									if ($_SERVER['REQUEST_METHOD'] === "GET") {
										$controlador->changePasswordView($param1);
									} elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
										$controlador->changePassword($param1);
									} else {
										$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
										include "vistas/modulos/plantilla.php";
									}
								
								} else {
									$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
									include "vistas/modulos/plantilla.php";
								}
							break;

							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
						}

					break;						
					case "perfil":

						require_once "app/Controllers/UsuariosController.php";

						$controlador = new \App\Controllers\UsuariosController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> editPerfil();
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}

						break;

					case "inicio":

						require_once "app/Controllers/HomeController.php";

						$ejecuta = new \App\Controllers\HomeController();

						$ejecuta -> index();

						break;

					case "costos-resumen":

						require_once "app/Controllers/CostosResumenController.php";

						self::execute($rutas, new \App\Controllers\CostosResumenController);

						break;
					case "requisiciones":
						if ( count($rutas) == 3 && $rutas[2] == 'crear-orden-compra' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/OrdenCompraController.php";

							$controlador = new \App\Controllers\OrdenCompraController();

							$controlador -> create($param1);

							break;

						}
						if ( count($rutas) == 3 && $rutas[2] == 'crear-cotizacion' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/CotizacionesController.php";

							$controlador = new \App\Controllers\CotizacionesController();

							$controlador -> store($param1);

							break;

						}
						if ( count($rutas) == 4 && $rutas[2] == 'download' ) {

							$param1 = $rutas[1];
							// $tipo = ( $rutas[3] == 'comprobantes' ) ? 1 : 2;
							if ( $rutas[3] == 'comprobantes' ) $tipo = 1;
							elseif ( $rutas[3] == 'ordenes' ) $tipo = 2;
							elseif ( $rutas[3] == 'facturas' ) $tipo = 3;
							elseif ( $rutas[3] == 'cotizaciones' ) $tipo = 4;
							elseif ( $rutas[3] == 'resguardos' ) $tipo = 6;
							else $tipo = 5;

							require_once "app/Controllers/RequisicionesController.php";

							$controlador = new \App\Controllers\RequisicionesController();

							$controlador -> download($param1, $tipo);

							break;

						} else {

							require_once "app/Controllers/RequisicionesController.php";

							self::execute($rutas, new \App\Controllers\RequisicionesController);

							break;

						}
						break;
					case "estatus":

						require_once "app/Controllers/EstatusController.php";

						self::execute($rutas, new \App\Controllers\EstatusController);

						break;

					case "unidades":

						require_once "app/Controllers/UnidadesController.php";

						self::execute($rutas, new \App\Controllers\UnidadesController);

						break;

					case "insumo-tipos":

						require_once "app/Controllers/InsumoTiposController.php";

						self::execute($rutas, new \App\Controllers\InsumoTiposController);

						break;

					case "insumos":

						require_once "app/Controllers/InsumosController.php";

						self::execute($rutas, new \App\Controllers\InsumosController);

						break;

					case "indirecto-tipos":

						require_once "app/Controllers/IndirectoTiposController.php";

						self::execute($rutas, new \App\Controllers\IndirectoTiposController);

						break;

					case "indirectos":

						require_once "app/Controllers/IndirectosController.php";

						self::execute($rutas, new \App\Controllers\IndirectosController);

						break;

					case "insumos-indirectos":

						require_once "app/Controllers/InsumosIndirectosController.php";

						self::execute($rutas, new \App\Controllers\InsumosIndirectosController);

						break;

					case "obras":

						require_once "app/Controllers/ObrasController.php";

						self::execute($rutas, new \App\Controllers\ObrasController);

						break;
					
					/*------------------------------
					| RUTAS - PROVEEDOR
					------------------------------*/
					
					case "categoria-proveedores":
						require_once "app/Controllers/CategoriaProveedorController.php";
						self::execute($rutas, new \App\Controllers\CategoriaProveedorController);
					break;
					case "categoria-permiso-proveedor":
						require_once "app/Controllers/CategoriasPermisosController.php";
						self::execute($rutas, new \App\Controllers\CategoriasPermisosController);
					break;
					case "permiso-proveedor":
						require_once "app/Controllers/PermisoCategoriaProveedorController.php";
						self::execute($rutas, new \App\Controllers\PermisoCategoriaProveedorController);
					break;

					case "solicitud-proveedor":
						require_once "app/Controllers/SolicitudProveedorController.php";
						self::execute($rutas, new \App\Controllers\SolicitudProveedorController);
					break;

					/*------------------------------
					| RUTAS - EMPRESAS
					------------------------------*/

					case "empresas":

						require_once "app/Controllers/EmpresasController.php";

						self::execute($rutas, new \App\Controllers\EmpresasController);

						break;

					case "sucursales":

						require_once "app/Controllers/SucursalesController.php";

						self::execute($rutas, new \App\Controllers\SucursalesController);

						break;

					/*------------------------------
					| RUTAS - ORDENES COMPRA GLOBALES
					------------------------------*/
					case "requisiciones-orden-compra-globales":
						require_once "app/Controllers/RequisicionOrdenCompraGlobalesController.php";
						self::execute($rutas, new \App\Controllers\RequisicionOrdenCompraGlobalesController);
						break;
					case "orden-compra-globales":
						require_once "app/Controllers/OrdenCompraGlobalesController.php";
						self::execute($rutas, new \App\Controllers\OrdenCompraGlobalesController);
						break;

					/*------------------------------
					| RUTAS - USUARIOS
					------------------------------*/
					
					case "usuarios":

						require_once "app/Controllers/UsuariosController.php";

						self::execute($rutas, new \App\Controllers\UsuariosController);

						break;

					case "perfiles":

						require_once "app/Controllers/PerfilesController.php";

						self::execute($rutas, new \App\Controllers\PerfilesController);

						break;

					case "permisos":

						require_once "app/Controllers/PermisosController.php";

						self::execute($rutas, new \App\Controllers\PermisosController);

						break;

					case "salir":

						include "vistas/modulos/salir.php";

						break;

				}

			} else {
				
				$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            	include "vistas/modulos/plantilla.php";
			}

	    } else {

			require_once "app/Controllers/HomeController.php";

			$ejecuta = new \App\Controllers\HomeController();

			$ejecuta -> index();

	    }
	}

	static public function proveedor()
	{
		$rutas = array();
		$ruta = null;

	    if ( isset($_GET["ruta"]) ) {

			$rutas = explode("/", $_GET["ruta"]);
			
	    	$ruta = $rutas[0];

			if (
				$ruta == "inicio" || 
				$ruta == "formulario-proveedor" || 
				$ruta == "proveedor" || 
				$ruta == "ingreso" || 
				$ruta == "ordenes-compra" || 
				$ruta == "datos-fiscales" || 

				$ruta == "datos-generales" || 
				$ruta == "datos-legales" || 
				$ruta == "datos-financieros" || 
				$ruta == "calidad-producto" || 

				$ruta == "estados-cuenta" || 
				$ruta == "debida-deligencia" || 
				$ruta == "salir" || 
				$ruta == "cotizaciones") {

				switch($ruta) {
					case "inicio":
						require_once "app/Controllers/HomeProveedorController.php";
						$ejecuta = new \App\Controllers\HomeProveedorController();
						$ejecuta -> index();
						break;
					case "formulario-proveedor":
						require_once "app/Controllers/FormularioProveedorController.php";
						self::execute($rutas, new \App\Controllers\FormularioProveedorController);
						break;
					case "debida-deligencia":
						require_once "app/Controllers/DebidaDiligenciaController.php";
						self::execute($rutas, new \App\Controllers\DebidaDiligenciaController);
						break;
					case "estados-cuenta":
						require_once "app/Controllers/EstadoCuentaController.php";
						self::execute($rutas, new \App\Controllers\EstadoCuentaController);
						break;

					//**A CAMBIO**/
					case "datos-fiscales":
						require_once "app/Controllers/DatosFiscalesController.php";
						self::execute($rutas, new \App\Controllers\DatosFiscalesController);
						break;
					//**A CAMBIO**/

					case "datos-generales":
						require_once "app/Controllers/DatosGeneralesProveedorController.php";
						self::execute($rutas, new \App\Controllers\DatosGeneralesProveedorController);
						break;
					case "datos-legales":
						require_once "app/Controllers/DatosLegalesProveedorController.php";
						self::execute($rutas, new \App\Controllers\DatosLegalesProveedorController);
						break;
					case "datos-financieros":
						require_once "app/Controllers/DatosFinancierosProveedorController.php";
						self::execute($rutas, new \App\Controllers\DatosFinancierosProveedorController);
						break;
					case "calidad-producto":
						require_once "app/Controllers/CalidadProductoProveedorController.php";
						self::execute($rutas, new \App\Controllers\CalidadProductoProveedorController);
						break;
					
					case "ordenes-compra":
						require_once "app/Controllers/OrdenCompraProveedorController.php";
						self::execute($rutas, new \App\Controllers\OrdenCompraProveedorController);
						break;
					case "cotizaciones":
						require_once "app/Controllers/CotizacionesController.php";
						self::execute($rutas, new \App\Controllers\CotizacionesController);
						break;
					case "ingreso":
						require_once "app/Controllers/LoginProveedorController.php";

						$controlador = new \App\Controllers\LoginProveedorController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> index();
					
						        } elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						        	$controlador -> login();
						        	
						        } else {

						        	header("Location:" . Route::routes('inicio'));
									die();

						        }
					
						        break;
					
							default:

								header("Location:" . Route::routes('inicio'));
								die();
					
						}
						break;
					case "salir":

						include "vistas/modulos/salir.php";

						break;
				}

			} else {
				
				$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
				include "vistas/modulos/plantilla_proveedores.php";
			}

	    } else {

			require_once "app/Controllers/HomeProveedorController.php";

			$ejecuta = new \App\Controllers\HomeProveedorController();

			$ejecuta -> index();

	    }
	}

	/**
	 * FUNCION QUE OBTIENE EL PRIMER SEGMENTO DE LA RUTA
	 * LA RETORNA COMO CADENA SI NO RETORNA INICIO
	 *
	 * @return string  CADENA DE LA RUTA.
	 */
	static public function getRoute()
	{
		$rutas = array();
		$ruta = "inicio";

	    if ( isset($_GET["ruta"]) ) {

			$rutas = explode("/", $_GET["ruta"]);
			
	    	$ruta = $rutas[0];

	    }

	    return $ruta;
	}
}

