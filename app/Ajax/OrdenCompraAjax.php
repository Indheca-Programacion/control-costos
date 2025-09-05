<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/OrdenCompra.php";
require_once "../Models/Requisicion.php";
require_once "../Requests/SaveObrasRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\OrdenCompra;
use App\Models\Requisicion;
use App\Requests\SaveObrasRequest;

class OrdenCompraAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
        $usuario = New Usuario;
        $usuario->consultar(null, \usuarioAutenticado()["id"]);

		$ordenCompra = New OrdenCompra;
        if ( $usuario->empresaId == 4 ) $ordenCompras = $ordenCompra->consultarRoal();
        else $ordenCompras = $ordenCompra->consultar();

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
        array_push($columnas, [ "data" => "ruta", "title" => "Ruta" ]);
        array_push($columnas, [ "data" => "acciones", "title" => "Acciones", "orderable" => false, "searchable" => false ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($ordenCompras as $key => $value) {
        	$rutaEdit = Route::names('orden-compra.edit', $value['id']);
        	$rutaDestroy = Route::names('orden-compra.destroy', $value['id']);
            $rutaPrint = Route::names('orden-compra.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

            $requisicion = new Requisicion;
            $requisicion->consultar("id", $value['requisicion.id']);
            $requisicion->consultarFacturas();

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"folio" => $value["folio"],
        		"obra" => mb_strtoupper(fString($value["obra.nombreCorto"])),
				"estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
				"colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                "requisicion" => mb_strtoupper(fString($value["prefijo"]))."-".$value["requisicion.folio"],
                "factura" => (count($requisicion->facturas) > 1 ? "CON FACTURA" : 'SIN FACTURA'),
				"creo" => mb_strtoupper(fString($value["creo"])),
                "total" => number_format($value["total"], 2),
                "proveedor" => mb_strtoupper(fString($value["proveedor"])),
                "banco" => mb_strtoupper(fString($value["datoBancario.nombreBanco"])),
                "clabe" => mb_strtoupper(fString($value["datoBancario.clabe"])),
                "ruta" => Route::names('orden-compra.edit', $value['id']),
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
        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "E.id", "operador" => "=", "valor" => $this->empresaId ]);
        if ( $this->proveedorId > 0 ) array_push($arrayFiltros, [ "campo" => "OC.proveedorId", "operador" => "=", "valor" => $this->proveedorId ]);
        if ( $this->categoriaId > 0 ) array_push($arrayFiltros, [ "campo" => "OC.categoriaId", "operador" => "=", "valor" => $this->categoriaId ]);

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
        array_push($columnas, [ "data" => "factura", "title" => "Factura" ]);
        array_push($columnas, [ "data" => "proveedor", "title" => "Proveedor" ]);
        array_push($columnas, [ "data" => "total", "title" => "Monto Total" ]);
        array_push($columnas, [ "data" => "banco", "title" => "Banco"]);
        array_push($columnas, [ "data" => "clabe", "title" => "CLABE" ]);
        array_push($columnas, [ "data" => "ruta", "title" => "Ruta" ]);
        array_push($columnas, [ "data" => "acciones", "title" => "Acciones", "orderable" => false, "searchable" => false ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($ordenesCompra as $key => $value) {
        	$rutaEdit = Route::names('orden-compra.edit', $value['id']);
        	$rutaDestroy = Route::names('orden-compra.destroy', $value['id']);
            $rutaPrint = Route::names('orden-compra.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

            $requisicion = new Requisicion;
            $requisicion->consultar("id", $value['requisicion.id']);
            $requisicion->consultarFacturas();

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"folio" => $value["folio"],
        		"obra" => mb_strtoupper(fString($value["obra.nombreCorto"])),
				"estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
				"colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                "requisicion" => mb_strtoupper(fString($value["prefijo"]))."-".$value["requisicion.folio"],
                "factura" => (count($requisicion->facturas) > 1 ? "CON FACTURA" : 'SIN FACTURA'),
				"creo" => mb_strtoupper(fString($value["creo"])),
                "total" => number_format($value["total"], 2),
                "proveedor" => mb_strtoupper(fString($value["proveedor"])),
                "banco" => mb_strtoupper(fString($value["datoBancario.nombreBanco"])),
                "clabe" => mb_strtoupper(fString($value["datoBancario.clabe"])),
                "ruta" => Route::names('orden-compra.edit', $value['id']),
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

    public function autorizarAdicional()
    {
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $ordenCompra = New OrdenCompra;
            $ordenCompra->id = $_POST["ordenCompraId"];

            if ( !$ordenCompra->autorizarAdicional() ) throw new \Exception("Hubo un error al intentar autorizar la Orden de Compra, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "La Orden de Compra fue autorizada correctamente."
            ];

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
}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $ordenAjax = New OrdenCompraAjax();

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
		} else if ( $_POST["accion"] == "autorizarAdicional" ) {
            /*=============================================
            AUTORIZAR ADICIONAL
            =============================================*/
            $ordenAjax->autorizarAdicional();
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
        $ordenAjax->empresaId = $_GET["empresaId"] ?? 0;
        $ordenAjax->proveedorId = $_GET["proveedorId"] ?? 0;
        $ordenAjax->categoriaId = $_GET["categoriaId"] ?? 0;
        $ordenAjax->consultarFiltros();
    } else{

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