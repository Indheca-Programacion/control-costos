<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/OrdenCompra.php";
require_once "../Requests/SaveObrasRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\OrdenCompra;
use App\Requests\SaveObrasRequest;

class OrdenCompraProveedorAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
		$ordenCompra = New OrdenCompra;
        $ordenCompra->id = usuarioAutenticadoProveedor()["id"];
        $ordenCompras = $ordenCompra->consultarOrdenCompraProveedor();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        // array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "fechaCreacion" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "subtotal" ]);
        array_push($columnas, [ "data" => "importe" ]);
        array_push($columnas, [ "data" => "moneda" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $registros = array();
        foreach ($ordenCompras as $key => $value) {
        	$folio = mb_strtoupper(fString($value['folio']));
            array_push( $registros, [
                "consecutivo" => ($key + 1),
                "folio" => $value["folio"],
                // "obra" => mb_strtoupper(fString($value["obra.nombreCorto"])),
                "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                "estatus" => mb_strtoupper(fString("pagado")),
                "subtotal" => '$ '.round($value["subtotal"],2),
                "importe" => '$ '.round($value["total"],2),
                "moneda" => mb_strtoupper(fString($value["moneda.descripcion"])),
                "acciones" => "<button type='button' class='btn btn-xs btn-info ver-archivos' data-id='{$value["id"]}' data-toggle='modal' data-target='#modalVerPagos'><i class='fas fa-folder-open'></i></button>
                               <button type='button' class='btn btn-xs btn-success agregar-factura' data-id='{$value["requisicion.id"]}' data-toggle='modal' data-target='#modalAgregarFactura'><i class='fas fa-file-upload'></i></button>
                               "
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

    /*=============================================
    CONSULTAR ARCHIVOS DE ORDEN DE COMPRA
    =============================================*/
    public function consultarArchivos()
    {
        $ordenCompra = New OrdenCompra;
        $ordenCompra->id = $_GET["ordenId"];
        $ordenCompra->consultar("id", $ordenCompra->id);
        $archivos = $ordenCompra->consultarArchivos();

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['archivos'] = $ordenCompra->archivos;

        echo json_encode($respuesta);
    }

    /*=============================================
    SUBIR FACTURAS
    =============================================*/
    public function subirFacturas()
    {
        $orden = New OrdenCompra;
        $orden->requisicionId = $_POST["requisicionId"];
        $orden->consultar("id", $orden->requisicionId);

        if ( !isset($_FILES["archivoFactura"]) || count($_FILES["archivoFactura"]["name"]) == 0 ) {
            $respuesta = [
                'codigo' => 400,
                'error' => true,
                'errorMessage' => "Debe seleccionar al menos un archivo."
            ];
            echo json_encode($respuesta);
            return;
        }

        $archivosSubidos = $orden->insertarFacturas($_FILES["archivoFactura"],"../../");

        if ( !$archivosSubidos ) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Error al subir los archivos."
            ];
            echo json_encode($respuesta);
            return;
        }

        $respuesta = [
            'codigo' => 200,
            'error' => false,
            'mensaje' => "Archivos subidos correctamente.",
            'archivos' => $archivosSubidos
        ];

        echo json_encode($respuesta);
    }
}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $ordenAjax = New OrdenCompraProveedorAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "agregar" ) {

            /*=============================================
            CREAR DETALLE DE OBRA
            =============================================*/
            $ordenAjax->crear();

        } elseif ( $_POST["accion"] == "subirFacturas") {

            /*=============================================
            SUBIR ARCHIVOS
            =============================================*/
            $ordenAjax->subirFacturas();

        }else {

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
    } elseif (isset($_GET["accion"]) && $_GET["accion"] == "buscarArchivos") {

        /*=============================================
        CONSULTAR ARCHIVOS DE ORDEN DE COMPRA
        =============================================*/
        $ordenAjax->consultarArchivos();

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