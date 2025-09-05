<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/NotaInformativa.php";
require_once "../Requests/SaveObrasRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\NotaInformativa;
use App\Requests\SaveObrasRequest;
use App\Controllers\Autorizacion;

class NotaInformativaAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
		$notaInformativa = New NotaInformativa;
        $notaInformativas = $notaInformativa->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo", "title" => "#" ]);
        array_push($columnas, [ "data" => "lugar", "title" => "Lugar" ]);
        array_push($columnas, [ "data" => "fecha", "title" => "Fecha" ]);
        array_push($columnas, [ "data" => "requisicion", "title" => "Requisición" ]);
        array_push($columnas, [ "data" => "creo", "title" => "Creado por" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($notaInformativas as $key => $value) {
        	$rutaEdit = Route::names('nota-informativa.edit', $value['id']);
        	$rutaDestroy = Route::names('nota-informativa.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['requisicion']));

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
                "lugar" => mb_strtoupper(fString($value["lugar"])),
                "requisicion" => mb_strtoupper(fString($value["requisicion"])),
                "fecha" => ( is_null($value["fecha"]) ? '' : fFechaLarga($value["fecha"]) ),
				"creo" => mb_strtoupper(fString($value["creo"])),
				"acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
								<form method='POST' action='{$rutaDestroy}' style='display: inline'>
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
}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $notaInformativaAjax = New NotaInformativaAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "agregar" ) {

            /*=============================================
            CREAR DETALLE DE OBRA
            =============================================*/
            $obraAjax->crear();

        } else if ( $_POST["accion"] == "agregarSemana" ) {
			/*=============================================
            AGREGAR SEMANA
            =============================================*/
            $obraAjax->addSemana();
		} else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }
    }else{

        /*=============================================
        TABLA DE NOTAS INFORMATIVAS
        =============================================*/
		$notaInformativaAjax->mostrarTabla();

    }


} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}


