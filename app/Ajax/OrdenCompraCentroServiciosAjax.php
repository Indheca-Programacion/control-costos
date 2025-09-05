<?php

namespace App\Ajax;

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/OrdenCompraCS.php";
require_once "../../control-mantenimiento/app/Models/Requisicion.php";
require_once "../../control-mantenimiento/app/Models/Inventario.php";
require_once "../../control-mantenimiento/app/Models/InventarioSalida.php";
require_once "../../control-mantenimiento/app/Models/InventarioPartida.php";

use App\Route;
use App\Models\Usuario;
use App\Models\OrdenCompra;
use App\Models\Requisicion;
use App\Models\Inventario;
use App\Models\InventarioSalida;
use App\Models\InventarioPartida;


class OrdenCompraCentroServiciosAjax
{
	/*=============================================
	TABLA DE ordenes
	=============================================*/
	public function mostrarTabla()
	{   

        try {
            $ordenCompra = New OrdenCompra;
            $ordenCompras = $ordenCompra->consultar();

            $columnas = array();
            array_push($columnas, [ "data" => "consecutivo" ]);
            array_push($columnas, [ "data" => "folio" ]);
            array_push($columnas, [ "data" => "servicio" ]);
            array_push($columnas, [ "data" => "estatus" ]);
            array_push($columnas, [ "data" => "fechaCreacion" ]);
            array_push($columnas, [ "data" => "requisicion" ]);
            array_push($columnas, [ "data" => "creo" ]);
            array_push($columnas, [ "data" => "acciones" ]);
            
            $token = createToken();
            
            $registros = array();
            foreach ($ordenCompras as $key => $value) {
                $rutaEdit = Route::names('orden-compra-centro-servicios.edit', $value['id']);

                $folio = mb_strtoupper(fString($value['id']));

                $primerosTres = substr($value['requisicion.folio'], 0, 3);
                if ( strtoupper($primerosTres) === 'IGC' ) {
                    array_push( $registros, [
                            "consecutivo" => ($key + 1),
                            "folio" => $value["folio"],
                            "primerosTres" => $primerosTres,
                            "servicio" => mb_strtoupper(fString($value["servicio.folio"])),
                            "estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
                            "colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                            "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                            "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                            "requisicion" => $value["requisicion.folio"],
                            "creo" => mb_strtoupper(fString($value["creo"])),
                            "acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"   
                    ] );
                }
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

        var_dump($ordenesCompra);

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

    /*=============================================
	FUNCION PARA VER TODOS LOS ARCHIVOS
	=============================================*/
    public $requisicionId;

    public function verArchivos()
    {
        try {
            $requisicion = new Requisicion;
            $ordenCompra = new OrdenCompra;
            $inventario = new Inventario;
            $inventarioSalida = new InventarioSalida;
            $inventarioPartida = new InventarioPartida;

            $respuesta = [];

            if ($requisicion->consultar(null, $_GET["requisicionId"])) {

                $requisicion->consultarComprobantes();
                $requisicion->consultarFacturas();
                $requisicion->consultarCotizaciones();
                $requisicion->consultarVales();
                $requisicion->consultarDetalles();

                $ordenCompra->ordenCompraId = $_GET["ordenCompraId"];

                $ordenDeCompraDatos = $ordenCompra->consultarOrdenDeCompra();

                $rutaOrdenDeCompra = $this->crearPDFOrdenDeCompra($ordenDeCompraDatos);

                //OBTENER DATOS DE LAS ENTRADAS DE ALMACEN
                $ordenCompraFolio = $ordenDeCompraDatos[0]["folio"];

                $entradasInventario = $inventario->obtenerEntradasPorOrdenCompra($ordenCompraFolio);

                // OBTENER LAS SALIDAS DE LAS ENTRADAS
                $salidasInventario = [];

                foreach ($entradasInventario as $key => $value) {

                    $salidasDeEntrada = $inventarioSalida->consultarInventarioPorId($value["id"]);
                    
                    if (!empty($salidasDeEntrada)) {
                        // Si hay salidas, las agregamos al arreglo principal
                        $salidasInventario = array_merge($salidasInventario, $salidasDeEntrada);
                    }
                }

                //OBTENER DATOS DE LAS SALIDAS DE ALMACEN
                $archivos = [];

                // Agregar la ruta de orden de compra a $archivos
                $archivos[] = escapeshellarg($rutaOrdenDeCompra);

                // VALIDA SI HAY VALES DE ENTRADAS DE LA ORDEN DE COMPRA
                if (!empty($entradasInventario)) {
                    $rutaValeEntrada = $this->crearPDFValeEntrada($entradasInventario);
                    $archivos[] = escapeshellarg($rutaValeEntrada);
                } 

                if (!empty($salidasInventario)) {
                    $rutaValeSalida = $this->crearPDFValeSalida($salidasInventario);
                    $archivos[] = escapeshellarg($rutaValeSalida);
                } 

                $categorias = [
                    $requisicion->comprobantesPago,
                    $requisicion->cotizaciones,
                    $requisicion->facturas,
                    $requisicion->valesAlmacen
                ];

                foreach ($categorias as $documentos) {
                    foreach ($documentos as $file) {
                        if ($file["formato"] === 'application/pdf') {
                            $ruta = $_SERVER['DOCUMENT_ROOT']. ltrim($file["ruta"], '/');
                            $archivos[] = escapeshellarg($ruta); // Importante para rutas seguras
                        }
                    }
                }

  
                $nombreArchivo = "requisicion_" . $requisicion->folio . ".pdf";
                $rutaSalida = "/tmp/" . $nombreArchivo;

                if (file_exists($rutaSalida)) {
                    unlink($rutaSalida);
                }

                $comando = "pdfunite " . implode(" ", $archivos) . " " . escapeshellarg($rutaSalida);

                shell_exec($comando);

                $rutaDestino = __DIR__ . "/../../reportes/requisiciones/" . $nombreArchivo;

                if (file_exists($rutaSalida)) {
                    if (!is_dir(dirname($rutaDestino))) {
                        mkdir(dirname($rutaDestino), 0777, true);
                    }

                    if (copy($rutaSalida, $rutaDestino)) {
                        unlink($rutaSalida);
                    } else {
                        throw new \Exception("No se pudo copiar el PDF generado al destino.");
                    }
                }

                if (file_exists($rutaDestino)) {

                    // Eliminar archivo temporal si está definido y existe
                    if (!empty($rutaRequisicion) && file_exists($rutaRequisicion)) {
                        unlink($rutaRequisicion);
                    }

                    // Eliminar archivos de órdenes de compra si existen y es un arreglo
                    if (!empty($rutasOrdenDeCompra) && is_array($rutasOrdenDeCompra)) {
                        foreach ($rutasOrdenDeCompra as $ruta) {
                            $rutaLimpia = str_replace("'", "", $ruta);
                            if (file_exists($rutaLimpia)) {
                                unlink($rutaLimpia);
                            }
                        }
                    }

                    $respuesta = [
                        'error' => false,
                        'ruta' => '/reportes/requisiciones/' . $nombreArchivo
                    ];
                } else {
                    throw new \Exception("Error al fusionar los archivos PDF.");
                }

                echo json_encode($respuesta);

            }

        } catch (\Exception $e) {
            echo json_encode([
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ]);
        }
    }

    function crearPDFOrdenDeCompra($datos)
    {
        include "../../reportes/OrdenCompraConjuntoPDF.php";

        $ruta = generarPDFSimple($datos);  // obtener la ruta retornada

        return $ruta; // si quieres usarla fuera también
    }

    function crearPDFValeEntrada($entradasInventario){

        include "../../reportes/ValeEntradaConjuntoPDF.php";

        $ruta = generarPDFValeEntrada($entradasInventario);  // obtener la ruta retornada

        return $ruta; // si quieres usarla fuera también
    }

    function crearPDFValeSalida($salidasInventario){

        include "../../reportes/ValeSalidaConjuntoPDF.php";

        $ruta = generarPDFValeSalida($salidasInventario);  // obtener la ruta retornada

        return $ruta; // si quieres usarla fuera también
    }

}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $ordenAjax = New OrdenCompraCentroServiciosAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "agregar" ) {

            /*=============================================
            CREAR DETALLE DE OBRA
            =============================================*/
            $ordenAjax->crear();

        } else if ( $_POST["accion"] == "agregarSemana" ) {
			/*=============================================
            AGREGAR SEMANA
            =============================================*/
            $ordenAjax->addSemana();
		} else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }
    } elseif (isset($_GET["obraId"])) {
        /*=============================================
        CONSULTAR FILTROS
        =============================================*/
        $ordenAjax->estatusId = $_GET["estatusId"];
        $ordenAjax->fechaInicial = $_GET["fechaInicial"];
        $ordenAjax->fechaFinal = $_GET["fechaFinal"];
        $ordenAjax->obraId = $_GET["obraId"];
        $ordenAjax->consultarFiltros();
    } elseif ( isset($_GET["accion"]) ){
        /*=============================================
        VER ARCHIVOS
        =============================================*/
        $ordenAjax->requisicionId = $_GET["requisicionId"];
        $ordenAjax->verArchivos();
    }
    else{

        /*=============================================
        TABLA DE ORDENES DE COMPRA
        =============================================*/
		$ordenAjax->mostrarTabla();

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