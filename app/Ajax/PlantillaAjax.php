<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Plantilla.php";
require_once "../Models/ObraDetalles.php";
require_once "../Requests/SavePlantillaDetallesRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Plantilla;
use App\Models\ObraDetalles;
use App\Requests\SavePlantillaDetallesRequest;
use App\Controllers\Autorizacion;

class PlantillaAjax
{

    /*=============================================
    TABLA DE PLANTILLAS
    =============================================*/
	public function mostrarTabla()
	{
        	$plantilla = New Plantilla;
            $plantillas = $plantilla->consultarPorUsuario(usuarioAutenticado()["id"]);

        	$columnas = array();
            array_push($columnas, [ "data" => "consecutivo" ]);
            array_push($columnas, [ "data" => "nombreCorto" ]);
            array_push($columnas, [ "data" => "descripcion" ]);
            array_push($columnas, [ "data" => "acciones" ]);
            
            $token = createToken();
            
            $registros = array();
            foreach ($plantillas as $key => $value) {
                $rutaEdit = Route::names('plantillas.edit', $value['id']);
                $rutaDestroy = Route::names('plantillas.destroy', $value['id']);
                $folio = mb_strtoupper(fString($value['nombreCorto']));

                array_push( $registros, [ "consecutivo" => ($key + 1),
                        "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"])),
                        "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                        "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                                        <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                                                <input type='hidden' name='_method' value='DELETE'>
                                                                <input type='hidden' name='_token' value='{$token}'>
                                                                <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                                                <i class='far fa-times-circle'></i>
                                                                </button>
                                                        </form>" ] );
            }

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos']['columnas'] = $columnas;
            $respuesta['datos']['registros'] = $registros;

            echo json_encode($respuesta);
	}	
    /*=============================================
    AGREGAR DETALLES DE PLANTILLA
    =============================================*/
    public function agregarDetalles()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "plantillas", "crear") ) throw new \Exception("No está autorizado a añadir detalles.");

            $request = SavePlantillaDetallesRequest::validated();

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

            $plantilla = New Plantilla;

            // Crear el nuevo registro
            if ( !$plantilla->crearDetalle($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "El Detalle fue creado añadido correctamente."
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
    IMPORTAR LOS DETALLES A UNA PLANTILLA
    =============================================*/
    public function importarDetalles()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "plantillas", "crear") ) throw new \Exception("No está autorizado a añadir detalles.");

            $detalleObra = New ObraDetalles;

            $detalles = $detalleObra->consultarDisponiblesPlantilla($this->obra,$this->plantilla);

            if (count($detalles) > 0 ) {
                $arrayDetalles = array();
                foreach ($detalles as $key => $value) {
                    array_push($arrayDetalles,[
                        "fk_plantilla" => $this->plantilla,
                        "directoId" => $value["insumoId"],
                        "indirectoId" => $value["indirectoId"],
                        "cantidad" => $value["cantidad"],
                        "presupuesto" => $value["presupuesto"]
                    ]);
                }
                
                $plantilla = new Plantilla;
                
                foreach ($arrayDetalles as $key => $value) {
                    $response = $plantilla->crearDetalle($value);
                }

                $respuesta = [
                    'error' => false,
                    'respuesta' => $response,
                    'respuestaMessage' => "Se ha exportado correctamente."
                ];
            }else{

                $respuesta = [
                    'error' => true,
                    'codigo' => 202,
                    'errorMessage' => "No existen detalles de la obra o ya estan todos en la plantilla."
                ];
            }

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "El Detalle fue creado añadido correctamente."
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
    IMPORTAR LA PLANTILLA
    =============================================*/
    public function importarPlantilla()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "obras-detalles", "create") ) throw new \Exception("No está autorizado a añadir detalles.");

            $plantilla = New Plantilla;

            $plantilla->id = $this->plantilla;

            $detalles = $plantilla->consultarDisponiblesObra($this->obra);

            if (count($detalles) > 0 ) {
                $arrayDetalles = array();
                foreach ($detalles as $key => $value) {
                    array_push($arrayDetalles,[
                        "obraId" => $this->obra,
                        "insumoId" => $value["directoId"],
                        "indirectoId" => $value["indirectoId"],
                        "cantidad" => $value["cantidad"],
                        "presupuesto" => $value["presupuesto"]
                    ]);
                }
                
                $detalleObra = new ObraDetalles;
                
                foreach ($arrayDetalles as $key => $value) {
                    $response = $detalleObra->agregar($value);
                }

                $respuesta = [
                    'error' => false,
                    'respuesta' => $response,
                    'respuestaMessage' => "Se ha importado correctamente."
                ];
            }else{

                $respuesta = [
                    'error' => true,
                    'codigo' => 202,
                    'errorMessage' => "La plantilla no contiene detalles por importar."
                ];
            }

        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);
    }
    public function obtenerDetalles()
    {
        $plantilla = New Plantilla;
        $plantillas = $plantilla->consultarDetalles($this->plantilla);

        $columnas = array();
        array_push($columnas, [ "data" => "tipo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "presupuesto" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = token();
        
        $registros = array();
        foreach ($plantillas as $key => $value) {
            $rutaEdit = Route::names('plantilla-detalles.edit', $value['id']);
            $rutaDestroy = Route::names('plantilla-detalles.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['descripcion']));

            array_push( $registros, [ "consecutivo" => ($key + 1),
                    "tipo" => mb_strtoupper(fString($value["tipo"])),
                    "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                    "cantidad" => ($value["cantidad"]),
                    "presupuesto" => ($value["presupuesto"]),
                    "acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                                    <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                                            <input type='hidden' name='_method' value='DELETE'>
                                                            <input type='hidden' name='_token' value='{$token}'>
                                                            <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                                            <i class='far fa-times-circle'></i>
                                                            </button>
                                                    </form>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }
}



$plantilla = new PlantillaAjax();
if (isset($_POST["accion"])) {
    if ($_POST["accion"] == "addDetalle") {
        /*=============================================
        AÑADE EL DETALLE PARA UNA PLANTILLA
        =============================================*/
        $plantilla->agregarDetalles(); 
    } else if ($_POST["accion"] == "importDetalle") {
        /*=============================================
        IMPORTA LOS MATERIALES 
        =============================================*/
        $plantilla->obra = $_POST["obra"];
        $plantilla->plantilla = $_POST["plantilla"];
        $plantilla->importarDetalles();
    } else if ($_POST["accion"] == "importarPlantilla") {
        /*=============================================
        IMPORTA LOS DATOS DE LA PLANTILLA A LA PROFORMA
        =============================================*/
        $plantilla->obra = $_POST["obra"];
        $plantilla->plantilla = $_POST["plantilla"];
        $plantilla->importarPlantilla();
    } else {

        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => "Realizó una petición desconocida."
        ];

        echo json_encode($respuesta);

    }
    
}else if ( isset($_GET["plantillaId"]) ) {

    /*=============================================
    CONSULTA RESUMEN DE COSTOS
    =============================================*/
    $plantilla->plantilla = $_GET["plantillaId"];
    $plantilla->obtenerDetalles();

}else{
    /*=============================================
    TABLA DE PLANTILLAS
    =============================================*/
    $plantilla -> mostrarTabla();
}
