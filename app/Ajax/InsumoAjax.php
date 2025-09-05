<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Insumo.php";
require_once "../Requests/SaveInsumosRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Insumo;
use App\Requests\SaveInsumosRequest;
use App\Controllers\Autorizacion;
// use App\Controllers\Validacion;

class InsumoAjax
{
	/*=============================================
	TABLA DE INSUMOS
	=============================================*/
	public function mostrarTabla()
	{
		$insumo = New Insumo;
        $insumos = $insumo->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "tipoInsumo" ]);
        array_push($columnas, [ "data" => "codigo" ]);
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
                "id"=>($value["id"]),
        		"consecutivo" => ($key + 1),
        		"tipoInsumo" => mb_strtoupper(fString($value["insumo_tipos.descripcion"])),
				"codigo" => mb_strtoupper(fString($value["codigo"])),
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
    CREAR INSUMO
    =============================================*/
    public function crear()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "crear") ) throw new \Exception("No está autorizado a crear nuevos Insumos.");

            $request = SaveInsumosRequest::validated();

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

            $insumo = New Insumo;

            // Crear el nuevo registro
            if ( !$insumo->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            // Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
            if ( !$insumo->consultar(null, $insumo->id) ) throw new \Exception("De favor refresque la pantalla para ver el nuevo registro.");

            $respuesta = [
                'error' => false,
                'respuesta' => $insumo,
                'respuestaMessage' => "El Directo fue creado correctamente."
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

    /*=============================================
    Agregar INSUMO
    =============================================*/
    public function agregar(){
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "crear") ) throw new \Exception("No está autorizado a agregar nuevos Insumos.");

            $request = SaveInsumosRequest::validated();

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

            $insumo = New Insumo;
            $response = $insumo->agregar($request);

            // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "El Directo fue agregador correctamente."
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
    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $insumoTipoId;
    public $obraId;

    public function consultarFiltros()
    {

        $insumo = New Insumo;
        $insumos = $insumo->consultarFiltroDetalles($this->obraId);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "tipoInsumo" ]);
        array_push($columnas, [ "data" => "codigo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        
        $registros = array();
        foreach ($insumos as $key => $value) {
            array_push( $registros, [
                "id"=>($value["id"]),
                "consecutivo" => ($key + 1),
                "insumoTipoId" => $value["insumoTipoId"],
                "tipoInsumo" => mb_strtoupper(fString($value["insumo_tipos.descripcion"])),
                "codigo" => mb_strtoupper(fString($value["codigo"])),
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
        $insumo = New Insumo;
        $insumos = $insumo->consultarPorObra($this->obraId);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        
        $registros = array();
        foreach ($insumos as $key => $value) {
            array_push( $registros, [
                "id"=>($value["obraDetalleId"]),
                "consecutivo" => ($key + 1),
                "insumoTipoId" => $value["insumoTipoId"],
                "tipoInsumo" => mb_strtoupper(fString($value["insumo_tipos.descripcion"])),
                "codigo" => mb_strtoupper(fString($value["codigo"])),
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

    $insumoAjax = New InsumoAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "crear" ) {

            /*=============================================
            CREAR INSUMO
            =============================================*/
            $insumoAjax->crear();

        } else if( $_POST["accion"] == "agregar" ){
            /*=============================================
            AGREGAR INSUMO
            =============================================*/
            $insumoAjax->agregar();
        } else if( $_POST["accion"] == "buscarInsumos"){
            $insumoAjax->obraId = (int) $_POST["obraId"];
            $insumoAjax->consultarPorObra();
        } else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }

    } elseif ( isset($_GET["obraId"]) ) {

        /*=============================================
        CONSULTAR FILTROS
        =============================================*/
        $insumoAjax->obraId = $_GET["obraId"];
        $insumoAjax->consultarFiltros();

    }else {

        /*=============================================
        TABLA DE INSUMOS
        =============================================*/
        $insumoAjax->mostrarTabla();

    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
