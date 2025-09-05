<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Indirecto.php";
require_once "../Requests/SaveIndirectosRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Indirecto;
use App\Requests\SaveIndirectosRequest;
use App\Controllers\Autorizacion;
// use App\Controllers\Validacion;

class IndirectoAjax
{
	/*=============================================
	TABLA DE INDIRECTOS
	=============================================*/
	public function mostrarTabla()
	{
		$indirecto = New Indirecto;
        $indirectos = $indirecto->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "tipoIndirecto" ]);
        array_push($columnas, [ "data" => "numero" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($indirectos as $key => $value) {
        	$rutaEdit = Route::names('indirectos.edit', $value['id']);
        	$rutaDestroy = Route::names('indirectos.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [
                "id"=>($value["id"]),
        		"consecutivo" => ($key + 1),
        		"tipoIndirecto" => '[ ' . mb_strtoupper(fString($value["indirecto_tipos.numero"])) . '  ] ' . mb_strtoupper(fString($value["indirecto_tipos.descripcion"])),
				"numero" => mb_strtoupper(fString($value["numero"])),
				"descripcion" => mb_strtoupper(fString($value["descripcion"])),
				"unidad" => mb_strtoupper(fString($value["unidades.descripcion"])),
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

    /*=============================================
    CREAR INDIRECTO
    =============================================*/
    public function crear()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "crear") ) throw new \Exception("No está autorizado a crear nuevos Indirectos.");

            $request = SaveIndirectosRequest::validated();

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

            $indirecto = New Indirecto;

            // Crear el nuevo registro
            if ( !$indirecto->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            // Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
            if ( !$indirecto->consultar(null, $indirecto->id) ) throw new \Exception("De favor refresque la pantalla para ver el nuevo registro.");

            $respuesta = [
                'error' => false,
                'respuesta' => $indirecto,
                'respuestaMessage' => "El indirecto fue creado correctamente."
            ];

        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);
    }
    public $obraId;

    public function consultarFiltros()
    {
        $indirecto = New Indirecto;
        $indirecto = $indirecto->consultarFiltroDetalles($this->obraId);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "tipoIndirecto" ]);
        array_push($columnas, [ "data" => "codigo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        
        $registros = array();
        $registros = array();
        foreach ($indirecto as $key => $value) {
            array_push( $registros, [
                "id"=>($value["id"]),
                "consecutivo" => ($key + 1),
                "indirectoTipoId" => $value["indirectoTipoId"],
                "tipoIndirecto" => mb_strtoupper(fString($value["indirecto_tipos.descripcion"])),
                "codigo" => mb_strtoupper(fString($value["numero"])),
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "unidadId" => $value["unidadId"],
                "unidad" => mb_strtoupper(fString($value["unidades.descripcion"]))
            ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    public function consultarPorObra()
    {
        $indirecto = New Indirecto;
        $indirectos = $indirecto->consultarPorObra($this->obraId);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        
        $registros = array();
        foreach ($indirectos as $key => $value) {
            array_push( $registros, [
                "id"=>($value["obraDetalleId"]),
                "consecutivo" => ($key + 1),
                "indirectoTipoId" => $value["indirectoTipoId"],
                "tipoIndirecto" => mb_strtoupper(fString($value["indirecto_tipos.descripcion"])),
                "codigo" => mb_strtoupper(fString($value["numero"])),
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "unidadId" => $value["unidadId"],
                "unidad" => mb_strtoupper(fString($value["unidades.descripcion"]))
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

try {

    $indirectoAjax = New IndirectoAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "crear" ) {

            /*=============================================
            CREAR INDIRECTO
            =============================================*/
            $indirectoAjax->crear();

        } else if( $_POST["accion"] == "buscarIndirectos" ) {
            $indirectoAjax->obraId = (int) $_POST["obraId"];
            $indirectoAjax->consultarPorObra();
        } else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }

    } elseif ( isset($_GET["obraId"]) ){
        /*=============================================
        CONSULTAR FILTROS
        =============================================*/
        $indirectoAjax->obraId = $_GET["obraId"];
        $indirectoAjax->consultarFiltros();
    }else{

        /*=============================================
        TABLA DE INDIRECTOS
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
