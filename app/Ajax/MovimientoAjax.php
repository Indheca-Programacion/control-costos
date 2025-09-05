<?php

namespace App\Ajax;

session_start();

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/control-costos/php_error_log');

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Movimientos.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveMovimientosRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Movimientos;
use App\Controllers\Autorizacion;
use App\Requests\SaveMovimientosRequest;
use Exception;

class MovimientoAjax
{
	/*=============================================
	TABLA DE MOVIMIENTOS
	=============================================*/
	public function mostrarTabla()
	{
		$movimiento = New Movimientos;
        $movimientos = $movimiento->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "codigo" ]);
        array_push($columnas, [ "data" => "placa" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "tipo" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "operador" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($movimientos as $key => $value) {
            $rutaEdit = Route::names('movimientos.edit', $value['id']);
            $folio = mb_strtoupper(fString($value["codigo"]));
        	$rutaDestroy = Route::names('movimientos.destroy', $value['id']);
        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
                "codigo" =>sprintf("C%04d",$value["codigo"]),
				"obra" => mb_strtoupper(fString($value["obra.descripcion"])),
				"tipo" => mb_strtoupper(fString($value["tipoMovimiento"])),
                "estatus" => mb_strtoupper(fString($value["estatusMovimiento"])),
                "operador" => mb_strtoupper(fString($value["operador"])),
                "placa" => mb_strtoupper(fString($value["placa"])),
				"fecha" => mb_strtoupper(fFechaLargaHora($value["fechaCreacion"])),
                //<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a> No hay necesidad de esto(?)
                "acciones" => "<form method='POST' action='{$rutaDestroy}' style='display: inline'>
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

    public function crear()
    {
        if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "movimientos", "crear") ) throw new \Exception("No está autorizado a crear nuevos Movimientos.");

        $request = SaveMovimientosRequest::validated();

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

        $movimiento = New Movimientos;
        $respuesta = $movimiento->crear($request);

        $respuesta = [
            'codigo' => 200,
            'error' => false,
            'successMessage' => "El movimiento se ha creado correctamente.",
            'ruta' => Route::names('movimientos.index')
        ];

        echo json_encode($respuesta);
    }
}

/*=============================================
TABLA DE MOVIMIENTOS
=============================================*/

try {

    $MovimientoAjax = New MovimientoAjax();
    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "registroMovimiento" ) {

            /*=============================================
            CREAR MOVIMIENTO
            =============================================*/
            $MovimientoAjax->crear();

        } else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }
    } else {
        /*=============================================
        TABLA DE ASISTENCIAS
        =============================================*/
        $MovimientoAjax->mostrarTabla();
    }


} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}


