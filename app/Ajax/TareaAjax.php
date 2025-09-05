<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Tarea.php";
require_once "../Models/TareaArchivo.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Tarea;
use App\Models\TareaArchivo;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class TareaAjax
{

	/*=============================================
	TABLA DE TAREAS
	=============================================*/
	public function mostrarTabla()
	{
		require_once "../Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null,usuarioAutenticado()["id"]);
		$usuario->consultarPerfiles();

		$tarea = New Tarea;
		$tareas = $tarea->consultarPorUsuario(usuarioAutenticado()["id"]);
		if ($usuario->checkAdmin()) {
			$tareas = $tarea->consultar();
		}
		

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "responsable" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "estatusLabel" ]);
        array_push($columnas, [ "data" => "fecha_inicio" ]);
        array_push($columnas, [ "data" => "fecha_limite" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();

        $registros = array();
        foreach ($tareas as $key => $value) {
        	$rutaEdit = Route::names('tareas.edit', $value['id']);
        	$rutaDestroy = Route::names('tareas.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));
			switch ($value["estatusLabel"]) {
				case 'EN CURSO':
					$bg = 'primary';
					break;
				case 'COMPLETADO':
					$bg = 'success';
					break;	
				default:
					$bg = 'info';
					break;
			}
        	array_push( $registros, [ "consecutivo" => ($key + 1),
									  "responsable" => mb_strtoupper(fString($value["responsable"])) ,
									  "descripcion" => mb_strtoupper(fString($value["descripcion"])) ,
									  "estatus" => '<div class="progress"><div class="progress-bar bg-'.$bg.'" role="progressbar" aria-valuenow="'.$value["estatus"].'" aria-valuemin="0" aria-valuemax="10" style="width: '.$value["estatus"].'0%">
                                      <span class="sr-only"></span></div>
                                      </div>' ,
									  "estatusLabel" => "<span class='badge bg-".$bg."'>".mb_strtoupper(fString($value["estatusLabel"])).'</span>' ,
									  "fecha_inicio" => fFechaLarga($value["fecha_inicio"]) ,
									  "fecha_limite" => fFechaLarga($value["fecha_limite"]) ,
									  "creo" => mb_strtoupper(fString($value["creo"])) ,
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

	public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "tarea-observaciones", "eliminar") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a eliminar Archivos.";

            }
        
        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

        // Validar Token
        if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        $tareaArchivos = New TareaArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $tareaArchivos->id = $this->archivoId;
        $tareaArchivos->fk_tarea = $this->fk_tarea;
        
        if ( !$tareaArchivos->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $tareaArchivos->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);

    }

    public function verRequisicion()
    {
        

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $tarea = New Tarea;
            
            $datos["estatus"] = 10;
            $datos["id"] = $this->tarea_id;
            $response = $tarea->actualizarEstatus($datos);

            if ( $response ) {
                $respuesta["respuestaMessage"] = "La tarea fue completada correctamente.";
                $respuesta["respuesta"] = true;
                $respuesta["error"] = false;
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar completar la tarea, intente de nuevo.";
            } 

        
        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

        echo json_encode($respuesta);
    }

}

$tareaAjax = new TareaAjax();

/*=============================================
TABLA DE TAREAS
=============================================*/
try {
    if (isset($_POST["accion"])){
        if ( $_POST["accion"] == "eliminarArchivo" && isset($_POST["archivoId"]) ) {

            /*=============================================
            ELIMINAR ARCHIVO
            =============================================*/
            $tareaAjax->token = $_POST["_token"];
            $tareaAjax->archivoId = $_POST["archivoId"];
            $tareaAjax->fk_tarea = $_POST["tareaId"];
            $tareaAjax->eliminarArchivo();

        } elseif ( $_POST["accion"] == "verRequisicion" ) {
            /*=============================================
            VER REQUISICION
            =============================================*/
            $tareaAjax->tarea_id = $_POST["tareaId"];
            $tareaAjax->verRequisicion();
        } else {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errorMessage' => "Realizó una petición desconocida."
                ];

                echo json_encode($respuesta);

        }
        
    } else {
        $tareaAjax->mostrarTabla();
    }
} catch (\Exception $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
