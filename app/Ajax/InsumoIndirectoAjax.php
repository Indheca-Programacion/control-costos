<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
// require_once "../Models/Usuario.php";
require_once "../Models/Insumo.php";
require_once "../Models/Indirecto.php";
// require_once "../Controllers/Autorizacion.php";

use App\Route;
// use App\Models\Usuario;
use App\Models\Insumo;
use App\Models\Indirecto;
// use App\Controllers\Autorizacion;
// use App\Controllers\Validacion;

class IndirectoAjax
{
	/*=============================================
	TABLA DE INSUMOS E INDIRECTOS
	=============================================*/
	public function mostrarTabla()
	{
        $insumo = New Insumo;
        $insumos = $insumo->consultar();

		$indirecto = New Indirecto;
        $indirectos = $indirecto->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "tipo" ]);
        array_push($columnas, [ "data" => "tipo_insumo_indirecto" ]);
        array_push($columnas, [ "data" => "codigo_numero" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($insumos as $key => $value) {
            $rutaEdit = Route::names('insumos.edit', $value['id']);
            $rutaDestroy = Route::names('insumos.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['descripcion']));

            array_push( $registros, [
                "consecutivo" => ($key + 1),
                "tipo" => "INSUMO",
                "tipo_insumo_indirecto" => mb_strtoupper(fString($value["insumo_tipos.descripcion"])),
                "codigo_numero" => mb_strtoupper(fString($value["codigo"])),
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "unidad" => mb_strtoupper(fString($value["unidades.descripcion"])),
                "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}' tipo='Insumo'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>"
            ] );
        }

        foreach ($indirectos as $key => $value) {
        	$rutaEdit = Route::names('indirectos.edit', $value['id']);
        	$rutaDestroy = Route::names('indirectos.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
                "tipo" => "INDIRECTO",
        		"tipo_insumo_indirecto" => '[ ' . mb_strtoupper(fString($value["indirecto_tipos.numero"])) . '  ] ' . mb_strtoupper(fString($value["indirecto_tipos.descripcion"])),
				"codigo_numero" => mb_strtoupper(fString($value["numero"])),
				"descripcion" => mb_strtoupper(fString($value["descripcion"])),
				"unidad" => mb_strtoupper(fString($value["unidades.descripcion"])),
				"acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
								<form method='POST' action='{$rutaDestroy}' style='display: inline'>
									<input type='hidden' name='_method' value='DELETE'>
									<input type='hidden' name='_token' value='{$token}'>
									<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}' tipo='Indirecto'>
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
    public function getCodigo(){
        if ($this->tipo =="insumoTipoId") {
            $insumo = New Insumo;
            $respuesta = $insumo->consultarLasCode($this->id);
            if($respuesta) $id = "codigo";

        } else{
            $indirecto = New Indirecto;
            $respuesta = $indirecto->consultarLasCode($this->id);
            if($respuesta) $id = "numero";

        }
        $codigo="";
        if(isset($id)){
            $numeros = explode(".", $respuesta[$id]);

            // Tomar el último número, convertirlo a entero y sumarle 1
            $ultimoNumero = intval(end($numeros));
            $resultado = $ultimoNumero + 1;
    
            // Actualizar el último número en el array
            $numeros[count($numeros) - 1] = $resultado;
    
            // Reconstruir el string con el último número sumado
            $codigo = implode(".", $numeros);

        }
        

        echo json_encode($codigo);
    }
}

try {
    $indirectoAjax = New IndirectoAjax();
    if(isset($_GET["id"])){
        $indirectoAjax->id = $_GET["id"]; 
        $indirectoAjax->tipo = $_GET["tipo"]; 
        $indirectoAjax->getCodigo();
    }else{
        /*=============================================
        TABLA DE INSUMOS E INDIRECTOS
        =============================================*/
        $indirectoAjax->mostrarTabla();
    }
} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
