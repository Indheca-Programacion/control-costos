<?php

namespace App\Controllers;

require_once "app/Models/Ubicacion.php";
require_once "app/Policies/UbicacionPolicy.php";
require_once "app/Requests/SaveUbicacionesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Ubicacion;
use App\Policies\UbicacionPolicy;
use App\Requests\SaveUbicacionesRequest;
use App\Route;

class UbicacionesController
{
    public function index()
    {
        Autorizacion::authorize('view', new Ubicacion);

        $ubicacion = New Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/ubicaciones/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $ubicacion = new Ubicacion;
        Autorizacion::authorize('create', $ubicacion);

        $contenido = array('modulo' => 'vistas/modulos/ubicaciones/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Ubicacion);

        $request = SaveUbicacionesRequest::validated();

        $ubicacion = New Ubicacion;
        $respuesta = $ubicacion->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Ubicación',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La ubicación fue creada correctamente' );
            header("Location:" . Route::names('ubicaciones.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Ubicación',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('ubicaciones.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Ubicacion);

        $ubicacion = New Ubicacion;

        if ( $ubicacion->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/ubicaciones/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Ubicacion);

        $request = SaveUbicacionesRequest::validated($id);

        $ubicacion = New Ubicacion;
        $ubicacion->id = $id;
        $respuesta = $ubicacion->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Ubicación',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La ubicación fue actualizada correctamente' );
            header("Location:" . Route::names('ubicaciones.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Ubicación',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('ubicaciones.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Ubicacion);

        // Sirve para validar el Token
        if ( !SaveUbicacionesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Ubicación',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('ubicaciones.index'));
            die();

        }

        $ubicacion = New Ubicacion;
        $ubicacion->id = $id;
        $respuesta = $ubicacion->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Ubicación',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La ubicación fue eliminada correctamente' );

            header("Location:" . Route::names('ubicaciones.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Ubicación',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta ubicación no se podrá eliminar ***' );
            header("Location:" . Route::names('ubicaciones.index'));

        }
        
        die();

    }
}
