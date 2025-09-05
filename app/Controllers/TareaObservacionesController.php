<?php

namespace App\Controllers;

require_once "app/Models/TareaObservaciones.php";
require_once "app/Models/Tarea.php";
require_once "app/Policies/TareaObservacionesPolicy.php";
require_once "app/Requests/SaveTareaObservacionesRequest.php";
require_once "app/Requests/SaveTareaRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\TareaObservaciones;
use App\Models\Tarea;
use App\Policies\TareaObservacionesPolicy;
use App\Requests\SaveTareaObservacionesRequest;
use App\Requests\SaveTareaRequest;
use App\Route;

class TareaObservacionesController
{

    public function store()
    {

        Autorizacion::authorize('create', New TareaObservaciones);

        $request = SaveTareaObservacionesRequest::validated();

        $observacion = New TareaObservaciones;
        
        $datos = SaveTareaRequest::validated();

        $datos["id"] = $request["fk_tarea"];

        $tarea = new Tarea;
        if(isset($datos["estatus"])) $tarea->actualizarEstatus($datos);

        $respuesta = $observacion->crear($request);
        
        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tarea',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La tarea fue actualizada correctamente' );
            header("Location:" . Route::names('tareas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('tareas.edit',$request["fk_tarea"]));

        }
        
        die();

    }

}
