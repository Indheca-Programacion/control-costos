<?php

namespace App\Controllers;

require_once "app/Models/Unidad.php";
// require_once "app/Policies/UnidadPolicy.php";
require_once "app/Requests/SaveUnidadesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Unidad;
// use App\Policies\UnidadPolicy;
use App\Requests\SaveUnidadesRequest;
use App\Route;

class UnidadesController
{
    public function index()
    {
        Autorizacion::authorize('view', New Unidad);

        $unidad = New Unidad;
        $unidades = $unidad->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/unidades/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $unidad = New Unidad;
        Autorizacion::authorize('create', $unidad);

        $contenido = array('modulo' => 'vistas/modulos/unidades/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Unidad);

        $request = SaveUnidadesRequest::validated();

        $unidad = New Unidad;
        $respuesta = $unidad->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Unidad',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La unidad fue creada correctamente' );
            header("Location:" . Route::names('unidades.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Unidad',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('unidades.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Unidad);

        $unidad = New Unidad;

        if ( $unidad->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/unidades/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Unidad);

        $request = SaveUnidadesRequest::validated($id);

        $unidad = New Unidad;
        $unidad->id = $id;
        $respuesta = $unidad->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Unidad',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La unidad fue actualizada correctamente' );
            header("Location:" . Route::names('unidades.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Unidad',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('unidades.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New Unidad);

        // Sirve para validar el Token
        if ( !SaveUnidadesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Unidad',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('unidades.index'));
            die();

        }

        $unidad = New Unidad;
        $unidad->id = $id;
        $respuesta = $unidad->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Unidad',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La unidad fue eliminada correctamente' );

            header("Location:" . Route::names('unidades.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Unidad',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta unidad no se podrá eliminar ***' );
            header("Location:" . Route::names('unidades.index'));

        }
        
        die();

    }
}
