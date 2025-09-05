<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/RequisicionPersonal.php";
require_once "../Requests/SaveRequisicionPersonalRequest.php";

require_once "../Controllers/Autorizacion.php";


use App\Route;
use App\Models\Usuario;
use App\Models\RequisicionPersonal;
use App\Requests\SaveRequisicionPersonalRequest;
use App\Controllers\Autorizacion;

class RequisicionPersonalAjax
{
    /*=============================================
    TABLA DE PERMISOS
    =============================================*/
    public function mostrarTabla()
    {
        $reqPersonal = New RequisicionPersonal;
        $requisiciones = $reqPersonal->consultar();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "fecha_inicio" ]);
        array_push($columnas, [ "data" => "fecha_fin" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($requisiciones as $key => $value) {
        	$rutaEdit = Route::names('requisicion-personal.edit', $value['id']);
        	$rutaDestroy = Route::names('requisicion-personal.destroy', $value['id']);
            $rutaPrint = Route::names('requisicion-personal.print',$value['id']);
            $descripcion = $value["descripcionD"];
            if(is_null($value["descripcionD"])){
                $descripcion = $value["descripcionI"];
            }
            $estatus = 'Falta Autorizacion';
            if(!is_null($value["usuarioIdAuthRH"]) && !is_null($value["usuarioIdAutorizacion"]) ){
                $estatus = 'Autorizado';
            }
        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"folio" => mb_strtoupper(fString($value["folio"])),
        		"obra" => mb_strtoupper(fString($value["descripcionObra"])),
        		"cantidad" => mb_strtoupper(fString($value["cantidad"])),
        		"descripcion" => mb_strtoupper(fString($descripcion)),
				"fecha_inicio" => mb_strtoupper(fString($value["fecha_inicio"])),
				"fecha_fin" => mb_strtoupper(fString($value["fecha_fin"])),
                "estatus" => $estatus,
				"acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
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
    TABLA DE PERMISOS
    =============================================*/

    public function crear()
    {
        try {
             // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisicion-personal", "crear") ) throw new \Exception("No está autorizado a crear nuevas requisiciones.");

            $request = SaveRequisicionPersonalRequest::validated();

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

            $reqPersonal = New RequisicionPersonal;
            $obraId = $_POST["obraId"];
            $lastFolio = $reqPersonal->getLastFolio($obraId);
            $request["folio"] = $lastFolio;
            // Crear el nuevo registro
            if ( !$reqPersonal->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro de requisicion de personal, intente de nuevo.");


            $respuesta = [
                'error' => false,
                'respuesta' => $reqPersonal,
                'respuestaMessage' => "La requisicion de personal fue creada correctamente."
            ];

        }catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);
    }

    /*=============================================
    AUTORIZA
    =============================================*/
    public function autorizar(){
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "autorizar-req", "crear") ) throw new \Exception("No tiene permitido autorizar.");

            $usuario->consultarPerfiles();

            $perfil = "jefe de area";
            if (in_array('jefe de RH',$usuario->perfiles)) {
                $perfil = "jefe de RH";
            }
            
            $reqPersonal = New RequisicionPersonal;

            if ( !$reqPersonal->auth($_POST["id"],$perfil) ) throw new \Exception("Hubo un error al autorizar, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $reqPersonal,
                'respuestaMessage' => "La requisicion ha sido autorizada correctamente."
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
}


try {

    $reqPersonalAjax = new RequisicionPersonalAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "crear" ) {

            /*=============================================
            CREAR REQUISIICON
            =============================================*/
            $reqPersonalAjax->crear();

        } else if($_POST["accion"] == "autorizar"){
            /*=============================================
            AUTORIZAR
            =============================================*/
            $reqPersonalAjax->autorizar(); 
        } else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }

    } else{

        /*=============================================
        TABLA DE REQUISICIONES DE PERSONAL
        =============================================*/
        $reqPersonalAjax -> mostrarTabla();

    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}