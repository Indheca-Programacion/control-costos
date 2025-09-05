<?php

namespace App\Ajax;

session_start();

// Configuración de Errores
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/control-costos/php_error_log');

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Requisicion.php";

require_once "../Models/OrdenCompraGlobales.php";
require_once "../Requests/SaveObrasRequest.php";

require_once "./RequisicionPDFGenerator.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Requisicion;

use App\Ajax\RequisicionPDFGenerator;

use App\Models\OrdenCompraGlobales;
use App\Requests\SaveObrasRequest;

class OrdenCompraGlobalesAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
		$ordenCompraGlobal = New OrdenCompraGlobales;
        $ordenGlobalCompras = $ordenCompraGlobal->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaCreacion" ]);
        array_push($columnas, [ "data" => "requisicion" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "factura", "title" => "Factura" ]);
        array_push($columnas, [ "data" => "proveedor", "title" => "Proveedor" ]);
        array_push($columnas, [ "data" => "total", "title" => "Monto Total" ]);
        array_push($columnas, [ "data" => "banco", "title" => "Banco"]);
        array_push($columnas, [ "data" => "clabe", "title" => "CLABE" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($ordenGlobalCompras as $key => $value) {
        	$rutaEdit = Route::names('orden-compra-globales.edit', $value['id']);
        	$rutaDestroy = Route::names('orden-compra-globales.destroy', $value['id']);
            $rutaPrint = Route::names('orden-compra-globales.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

            $requisicion = new Requisicion;
            $requisicion->consultar("id", $value['REQUISICIONES_ID']);
            $requisicion->consultarFacturas();
            
            $facturas = is_array($requisicion->facturas) ? $requisicion->facturas : [];
            $estadoFactura = count($facturas) >= 1 ? "CON FACTURA" : "SIN FACTURA";

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"folio" => $value["folio"],
        		"obra" => mb_strtoupper(fString($value["OBRAS"])),
				"estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
				"colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                "requisicion" => mb_strtoupper(fString($value["PREFIJO_OBRA"]))."-".$value["REQUISICIONES"],
                "factura" => (count($requisicion->facturas) >= 1 ? "CON FACTURA" : 'SIN FACTURA'),
				"creo" => mb_strtoupper(fString($value["creo"])),
                "total" => formatMoney($value["total"]) ,
                "proveedor" => mb_strtoupper(fString($value["proveedor"])),
                "banco" => isset($value["datoBancario.nombreBanco"]) && !empty($value["datoBancario.nombreBanco"]) 
                    ? mb_strtoupper(fString($value["datoBancario.nombreBanco"])) 
                    : "SIN DATO BANCARIO",
                "clabe" => isset($value["datoBancario.clabe"]) && !empty($value["datoBancario.clabe"]) 
                    ? mb_strtoupper(fString($value["datoBancario.clabe"])) 
                    : "SIN DATO BANCARIO",
				"acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
								<form method='POST' action='{$rutaDestroy}' style='display: inline'>
									<input type='hidden' name='_method' value='DELETE'>
									<input type='hidden' name='_token' value='{$token}'>
									<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
										<i class='far fa-times-circle'></i>
									</button>
								</form> 
                                <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>"   
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
    CONSULTAR FILTROS
    =============================================*/
    public $obraId;
    public $estatusId;
    public $fechaInicial;
    public $fechaFinal;

    public function consultarFiltros()
    {
        $arrayFiltros = array();

        if ( $this->estatusId > 0 ) array_push($arrayFiltros, [ "campo" => "OC.estatusId", "operador" => "=", "valor" => $this->estatusId ]);
        if ( $this->obraId > 0 ) array_push($arrayFiltros, [ "campo" => "O.id", "operador" => "=", "valor" => $this->obraId ]);
        if ( $this->fechaInicial > 0 ) array_push($arrayFiltros, [ "campo" => "OC.fechaCreacion", "operador" => ">=", "valor" => "'".fFechaSQL($this->fechaInicial)." 00:00:00'" ]);
        if ( $this->fechaFinal > 0 ) array_push($arrayFiltros, [ "campo" => "OC.fechaCreacion", "operador" => "<=", "valor" => "'".fFechaSQL($this->fechaFinal)." 23:59:59'" ]);

        $ordenCompra = New OrdenCompra;
        $ordenesCompra = $ordenCompra->consultarFiltros($arrayFiltros);
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaCreacion" ]);
        array_push($columnas, [ "data" => "requisicion" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($ordenesCompra as $key => $value) {
        	$rutaEdit = Route::names('orden-compra.edit', $value['id']);
        	$rutaDestroy = Route::names('orden-compra.destroy', $value['id']);
            $rutaPrint = Route::names('orden-compra.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"folio" => $value["id"],
        		"obra" => mb_strtoupper(fString($value["obra.nombreCorto"])),
				"estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
				"colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                "requisicion" => mb_strtoupper(fString($value["prefijo"]))."-".$value["requisicion.folio"],
				"creo" => mb_strtoupper(fString($value["creo"])),
				"acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
								<form method='POST' action='{$rutaDestroy}' style='display: inline'>
									<input type='hidden' name='_method' value='DELETE'>
									<input type='hidden' name='_token' value='{$token}'>
									<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
										<i class='far fa-times-circle'></i>
									</button>
								</form> 
                                <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>"   
			] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    public function mostrarTablaRequisicionesOrdenCompraGlobal()
	{
        $requisicionGlobal = New OrdenCompraGlobales;
        $requisicionesGlobales = $requisicionGlobal->consultarRequisicionesPorOrdenCompraDetalles($this->ordenCompraId);

        $requisicion = New Requisicion;

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo", "title" => "#" ]);
        array_push($columnas, [ "data" => "obra", "title" => "Obra", "width" => "300px" ]);
        array_push($columnas, [ "data" => "folio", "title" => "Folio" ]);
        array_push($columnas, [ "data" => "estatus", "title" => "Estatus" ]);
        array_push($columnas, [ "data" => "fechaRequisicion", "title" => "Fecha Requisición" ]);
        array_push($columnas, [ "data" => "categoria", "title" => "Categoria" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($requisicionesGlobales as $consecutivo => $value) {
        	$folio =  mb_strtoupper(fString($value["prefijo"].'-'.$value['folio']));
            $solicito = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            $requisicion->id = $value["id"];
            $requisicion->consultarOrdenesCompra();

            $categoria = '';
            $requisicion->consultarCategorias();
            foreach ($requisicion->categorias as $key => $categoriaValue) {
                if ($key === array_key_last($requisicion->categorias)) {
                    $categoria = $categoriaValue["categoria"] . $categoria;
                } else {
                    $categoria = $categoria . ' | '. $categoriaValue["categoria"]  ;
                }
            }

        	array_push( $registros, [
                "consecutivo" => ($consecutivo + 1),
                "id" => $value["id"],
        		"obra" => fString($value["obra"]),
        		"folio" => $folio,
        		"estatus" => fString($value["estatus.descripcion"]),
                "colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
        		"fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
                "solicito" => fString($solicito),
                "categoria" => $categoria,
        ] );
        }
        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
        /* 
    OBTENER PARTIDAS POR REQUISICION
    */
    public function partidaPorOrdenCompraGlobal(){
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");
 
            $partida = New OrdenCompraGlobales;
            $partidas = $partida->consultarPartidaPorOrdenCompra($this->ordenCompraId);
         
            $columnas = array();
            array_push($columnas, [ "data" => "consecutivo" ]);
			array_push($columnas, [ "data" => "cantidad" ]);
			array_push($columnas, [ "data" => "concepto" ]);
			array_push($columnas, [ "data" => "costo_unitario" ]);
            array_push($columnas, [ "data" => "importe" ]);

			$registros = array();
			foreach ($partidas as $key => $value) {

			array_push( $registros, [ 
                                		"consecutivo" => ($key + 1),
										"cantidad" => $value["cantidad"],
										"concepto" => $value["concepto"]." | ".$value["descripcion"],
										"costo_unitario" => $value["importeUnitario"],
                                        "importe" => ($value["importeUnitario"] * $value["cantidad"]),
										]);
			}

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;

            $respuesta['datos']['columnas'] = $columnas;
            $respuesta['datos']['registros'] = $registros;


        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    public $archivosEliminar = [];

    public function verArchivos()
    {

        try {

            $ids = isset($_GET["requisicionId"]) ? explode(",", $_GET["requisicionId"]) : [];
            $ordenCompraId = isset($_GET["ordenCompraId"]) ? $_GET["ordenCompraId"] : "";

            $ordenCompraGlobal = New OrdenCompraGlobales;
            $requisicion = new Requisicion;

            $archivos = []; // arreglo acumulador

            $datosOrdenDeCompra = $ordenCompraGlobal->consultarDatosOrdenCompra($ordenCompraId);
            $rutasGeneradas = $this->crearPDFOrdenesDeCompra($datosOrdenDeCompra); // array

            $archivos = array_merge($archivos, $rutasGeneradas); // agrega al arreglo existente

            if (!isset($_SESSION['archivosEliminar'])) {
               $_SESSION['archivosEliminar'] = []; // inicializa si no existe
            }

            $_SESSION['archivosEliminar']= $rutasGeneradas; // agrega al arreglo existente

            foreach ($ids as $value) {
                if ($requisicion->consultar(null , $value)) {
                    $generador = new RequisicionPDFGenerator($requisicion);
                    $ruta = $generador->generar();
                    $archivos[] = $ruta;
                    $this->archivosEliminar[] = $ruta; // Para eliminar después
                }

                $ordenCompraGlobal->requisicionId = $value;

                $ordenCompraGlobal->consultarComprobantes();
                $ordenCompraGlobal->consultarOrdenes();
                $ordenCompraGlobal->consultarCotizacionesOrdenesGlobales();
                $ordenCompraGlobal->consultarFacturas();
                $ordenCompraGlobal->consultarSoporte();

                foreach ($ordenCompraGlobal->comprobantesPago as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                foreach ($ordenCompraGlobal->ordenesCompra as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                foreach ($ordenCompraGlobal->cotizaciones as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                foreach ($ordenCompraGlobal->facturas as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                foreach ($ordenCompraGlobal->soportes as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }
            }

            // Une el arreglo nuevo con el existente
            $_SESSION['archivosEliminar'] = array_merge($_SESSION['archivosEliminar'], $this->archivosEliminar);

            $nombreArchivo = "orden_compra.pdf";
            $rutaSalida = "/tmp/" . $nombreArchivo;

            $comando = "pdfunite " . implode(" ", $archivos) . " " . $rutaSalida . " 2>&1";

            shell_exec($comando);

            $rutaDestino = __DIR__ . "/../../reportes/orden-compra-global/" . $nombreArchivo;
            if (file_exists(str_replace("'", "", $rutaSalida))) {
                // Crear el directorio si no existe
                if (!is_dir(dirname($rutaDestino))) {
                    mkdir(dirname($rutaDestino), 0777, true);
                }
                // Mover el archivo generado al destino
                rename(str_replace("'", "", $rutaSalida), $rutaDestino);
            }

            if (file_exists(str_replace("'", "", $rutaDestino))) { //Verificar que el archivo se creo.

                // unlink(str_replace("'", "", $rutaRequisicion)); //Borrar el archivo temporal REQUISICION

                // foreach ($rutasOrdenDeCompra as $ruta) { 
                //     $rutaLimpia = str_replace("'", "", $ruta); 
                //     if (file_exists($rutaLimpia)) {
                //         unlink($rutaLimpia); // Eliminar rutas de las ordenes de compra
                //     }
                // }
                        
            } else {
                echo "Error al fusionar los archivos PDF.";
            }   
            
            $rutaModalEliminar =   $_SERVER['DOCUMENT_ROOT'] . CONST_APP_FOLDER .'reportes/orden-compra-global/'.$nombreArchivo;
            $rutaModal =  CONST_APP_FOLDER .'reportes/orden-compra-global/'.$nombreArchivo;

            $_SESSION['archivosEliminar'][] = $rutaModalEliminar;

            $respuesta = array( 
                'error' => false,
                'ruta' => $rutaModal,
            );
            echo json_encode($respuesta);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

    }

    public function eliminarArchivoTemporal()
    {
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");


            foreach ($_SESSION['archivosEliminar'] as $archivo) {
                if (file_exists($archivo)) {
                    if (unlink($archivo)) {
                        $respuesta = [
                            'error' => false,
                            'message' => "Archivo eliminado correctamente."
                        ];
                    } else {
                        $respuesta = [
                            'error' => true,
                            'message' => "No se pudo eliminar el archivo."
                        ];
                    }
                } else {
                    $respuesta = [
                        'error' => true,
                        'message' => "El archivo no existe."
                    ];
                }
            }

        } catch (\Exception $e) {
            $respuesta = [
                'error' => true,
                'message' => "Error al eliminar el archivo.",
                'errorMessage' => $e->getMessage()
            ];
        }

        unset($_SESSION['archivosEliminar']);
        echo json_encode($respuesta);
    }
     
    function crearPDFOrdenesDeCompra($datos)
    {
        include "../../reportes/PDFOrdenCompra.php";
        
        // Llamar a la función
        $rutasArchivos = generarPDFOrdenes($datos);

        return $rutasArchivos;
    }

}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $ordenGlobalAjax = New OrdenCompraGlobalesAjax();

    if ( isset($_POST["accion"]) ) {
        if ( $_POST["accion"] == "agregar" ) {
            /*=============================================
            CREAR DETALLE DE OBRA
            =============================================*/
            $ordenGlobalAjax->crear();

        } else if ( $_POST["accion"] == "agregarSemana" ) {
			/*=============================================
            AGREGAR SEMANA
            =============================================*/
            $ordenGlobalAjax->addSemana();
		}else if ( $_POST["accion"] == "eliminarArchivoTemporal" ) {
			/*=============================================
            AGREGAR SEMANA
            =============================================*/
            $ordenGlobalAjax->eliminarArchivoTemporal();
		} else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }
         }else if(isset($_GET["accion"])){
         if ($_GET["accion"] == "tablaRequisicionDetallesOrdenCompraGlobal") {
        /*=============================================
        CONSULTAR REQUISICIONES DE LAS ORDENES DE COMPRAS GLOBALES
        =============================================*/
        $ordenGlobalAjax->ordenCompraId = $_GET["ordenCompraId"];
        $ordenGlobalAjax->mostrarTablaRequisicionesOrdenCompraGlobal();
        }
        else if ( $_GET["accion"] == "verArchivos"  ) {
            /*=============================================
            VER ARCHIVOS
            =============================================*/
            $ordenGlobalAjax->requisicionId = $_GET["requisicionId"];
            $ordenGlobalAjax->verArchivos();

        }
        else if ($_GET["accion"] == "tablaPartidasDetallesOrdenCompraGlobal") {
        /*=============================================
        CONSULTAR REQUISICIONES DE LAS ORDENES DE COMPRAS GLOBALES
        =============================================*/
        $ordenGlobalAjax->ordenCompraId = $_GET["ordenCompraId"];
        $ordenGlobalAjax->partidaPorOrdenCompraGlobal();
        }
    }else{

        /*=============================================
        TABLA DE ORDENES DE COMPRA
        =============================================*/
		$ordenGlobalAjax->mostrarTabla();

    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);
}
?>