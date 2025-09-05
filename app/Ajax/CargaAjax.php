<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Carga.php";
require_once "../Models/Movimientos.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Carga;
use App\Models\Movimientos;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class CargaAjax
{

	/*=============================================
	TABLA DE QR
	=============================================*/
	public function mostrarTabla()
	{
		$cargas = New Carga;
        $cargas = $cargas->consultar();


		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo", "title" => "#" ]);
        array_push($columnas, [ "data" => "codigo", "title" => "Código" ]);
        array_push($columnas, [ "data" => "folio", "title" => "Folio" ]);
        array_push($columnas, [ "data" => "obra", "title" => "Obra" ]);
        array_push($columnas, [ "data" => "material", "title" => "Material" ]);
        array_push($columnas, [ "data" => "peso", "title" => "Peso" ]);
        array_push($columnas, [ "data" => "fechaHora", "title" => "Fecha y Hora" ]);
        array_push($columnas, [ "data" => "folioCargas", "title" => "Folio Carga" ]);
        array_push($columnas, [ "data" => "maquinariaAsignada", "title" => "Maquinaria Asignada" ]);
        array_push($columnas, [ "data" => "acciones", "title" => "Acciones", "orderable" => false, "searchable" => false ]);

        $token = createToken();
        
        $registros = array();
        foreach ($cargas as $key => $value) {

        	$rutaEdit = Route::names('cargas.edit', $value['idCarga']);
            $rutaPrint = Route::names('cargas.print', $value['idCarga']);

            $folio = $value["idCarga"];

        	array_push( $registros, [ 
                "consecutivo" => $key + 1,
                "codigo" => sprintf("C%04d", $value["codigo"]),
                "folio" => $value["folioCarga"],
                "obra" => mb_strtoupper($value["nombreObra"]),
                "material" => mb_strtoupper($value["nombreMaterial"]),
                "peso" =>  $value["pesoCarga"],
                "fechaHora" =>  fFechaLargaHora($value["fechaHoraCarga"]),
                "folioCargas" =>  $value["folioCarga"],
                "maquinariaAsignada" =>  $value["nombreMaquinaria"],
                "acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
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

    public function mostrarTablaMovimiento()
	{   

        $movimiento = New Movimientos;
        $movimientos = $movimiento->consultar("nId04Carga",$this->idCarga);

      $columnas = array();
        array_push($columnas, [ "data" => "codigo" ]);
        array_push($columnas, [ "data" => "placa" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "tipo" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "operador" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($movimientos as $key => $value) {
            $rutaEdit = Route::names('movimientos.edit', $value['id']);
            $folio = mb_strtoupper(fString($value["id"]));
        	$rutaDestroy = Route::names('movimientos.destroy', $value['id']);
        	array_push( $registros, [
                "codigo" =>sprintf("C%04d",$value["id"]),
				"obra" => mb_strtoupper(fString($value["obra.descripcion"])),
				"tipo" => mb_strtoupper(fString($value["tipoMovimiento"])),
                "estatus" => mb_strtoupper(fString($value["estatusMovimiento"])),
                "operador" => mb_strtoupper(fString($value["operador"])),
                "placa" => mb_strtoupper(fString($value["placa"])),
				"fecha" => mb_strtoupper(fFechaLargaHora($value["fechaCreacion"])),

			] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
}

/*=============================================
TABLA DE QR
=============================================*/

$cargaAjax = new CargaAjax();

/*=============================================
TABLA DE TAREAS
=============================================*/
try {
    if (isset($_POST["accion"])){

        
    }elseif(isset($_GET["idCarga"])){
        $cargaAjax->idCarga = $_GET["idCarga"];
        $cargaAjax->mostrarTablaMovimiento();
    }
    else {
        $cargaAjax->mostrarTabla();
    }
} catch (\Exception $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
