<?php

namespace App\Controllers;

require_once "app/Models/Tarea.php";
require_once "app/Policies/TareaPolicy.php";
require_once "app/Requests/SaveTareaRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Tarea;
use App\Policies\TareaPolicy;
use App\Requests\SaveTareaRequest;
use App\Route;

class TareasController
{
    public function index()
    {
        Autorizacion::authorize('view', new Tarea);

        $tarea = New Tarea;

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/tareas/index.php');

        include "vistas/modulos/plantilla.php";
        
    }

    public function create()
    {
        Autorizacion::authorize('create', new Tarea);

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultar();

        $contenido = array('modulo' => 'vistas/modulos/tareas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Tarea);

        $request = SaveTareaRequest::validated();

        $tarea = New Tarea;

        $respuesta = $tarea->crear($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tarea',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La tarea fue creada correctamente' );
            header("Location:" . Route::names('tareas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('tareas.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Tarea);

        $tarea = New Tarea;

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultar();
        $usuario->consultar(null, usuarioAutenticado()["id"]);
        $usuario->consultarPerfiles();

        if ( $tarea->consultar(null , $id) ) {

            $permitirEditar=false;
            if( $usuario->checkAdmin() || $tarea->creo == usuarioAutenticado()["id"] ){
                $permitirEditar = true;
            }

            $tarea->consultarObservaciones();
            $tarea->consultarArchivos();

            $cantidadArchivos = count($tarea->archivos);

            $permitirEliminarArchivos = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "tareas-subir", "eliminar") ) $permitirEliminarArchivos = true;

            $contenido = array('modulo' => 'vistas/modulos/tareas/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";

        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Tarea);

        $request = SaveTareaRequest::validated($id);

        $tarea = New Tarea;
        $tarea->id = $id;
        
        $respuesta = $tarea->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tarea',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La tarea fue actualizada correctamente' );
            header("Location:" . Route::names('tareas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('tareas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Tarea);

        // Sirve para validar el Token
        if ( !SaveTareaRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('tareas.index'));
            die();

        }

        $tarea = New Tarea;
        $tarea->id = $id;
        $respuesta = $tarea->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La sucursal fue eliminada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tarea',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La tarea fue eliminada correctamente' );
            header("Location:" . Route::names('tareas.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***' );
            header("Location:" . Route::names('tareas.index'));

        }
        
        die();

    }

    public function download($id)
    {
        Autorizacion::authorize('view', New Tarea);

        $tarea = New Tarea;

        $respuesta = array();

        if ( $tarea->consultar(null , $id) ) {

            $tarea->consultarArchivos();

            $respuesta = array( 'codigo' => ( count($tarea->archivos) > 0 ) ? 200 : 204,
                                'error' => false,
                                'cantidad' => count($tarea->archivos),
                                'archivos' => $tarea->archivos );

        } else {
            $respuesta = array( 'codigo' => 500,
                                'error' => true,
                                'errorMessage' => 'No se logró consultar la Requisición' );
        }

        echo json_encode($respuesta);
    }
}
