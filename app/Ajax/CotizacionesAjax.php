<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Cotizacion.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Cotizacion;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class CotizacionesAjax
{

	/*=============================================
	TABLA DE COTIZACIONES
	=============================================*/
	public function mostrarTabla()
	{
		$cotizacion = New Cotizacion;
        $cotizaciones = $cotizacion->consultarPorProveedor(\usuarioAutenticadoProveedor()["id"]);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "fechaRequisicion" ]);
        array_push($columnas, [ "data" => "fechaLimite" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "acciones", "title" => "Acciones", "orderable" => false ]);

        $token = createToken();
        
        $registros = array();
        foreach ($cotizaciones as $key => $value) {
            $rutaEdit = Route::names('cotizaciones.edit', $value['id']);
            $rutaDestroy = Route::names('cotizaciones.destroy', $value['id']);

            array_push($registros, [ 
                "consecutivo" => ($key + 1),
                "fechaRequisicion" => fFechaLarga($value['requisiciones.fechaRequerida']),
                "fechaLimite" => fFechaLarga($value['fechaLimite']),
                "estatus" => $value['estatus.descripcion'],
                "acciones" =>  "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"
            ]);
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

    /*=============================================
    SUBIR DOCUMENTO
    =============================================*/
    public function subirDocumento()
    {
        $cotizacion = new Cotizacion;
        $cotizacion->proveedorId = $_POST['proveedorId'];
        $respuesta = $cotizacion->insertarArchivos($_POST['requisicionId'],  $_FILES['cotizacionArchivos'], '../../');
        if ( $respuesta ) {
            $respuesta = array(
                "codigo" => 200,
                "error" => false,
                "mensaje" => "El documento se subió correctamente"
            );
        } else {
            $respuesta = array(
                "codigo" => 500,
                "error" => true,
                "mensaje" => "Error al subir el documento"
            );
        }
        echo json_encode($respuesta);
    }

}

/*=============================================
TABLA DE COTIZACIONES
=============================================*/

try {
    $cotizacionAjax = new CotizacionesAjax;
    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "subirCotizacion" ) {

            /*=============================================
            SUBIR COTIZACION
            =============================================*/
            $cotizacionAjax->subirDocumento();

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
        TABLA DE COTIZACIONES
        =============================================*/
		$cotizacionAjax->mostrarTabla();

    }
} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
