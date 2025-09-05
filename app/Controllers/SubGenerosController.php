<?php

namespace App\Controllers;

require_once "app/Models/SubGenero.php";
require_once "app/Requests/SaveSubGenerosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\SubGenero;
use App\Requests\SaveSubGenerosRequest;
use App\Route;

class SubGenerosController
{
    public function index()
    {
        Autorizacion::authorize('view', New SubGenero);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/subgeneros/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $subgenero = New SubGenero;
        Autorizacion::authorize('create', $subgenero);

        $contenido = array('modulo' => 'vistas/modulos/subgeneros/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New SubGenero);

        $request = SaveSubGenerosRequest::validated();

        $subgenero = New SubGenero;
        $respuesta = $subgenero->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear SubGenero',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El subgenero fue creado correctamente' );
            header("Location:" . Route::names('subgeneros.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear SubGenero',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('subgeneros.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New SubGenero);

        $subgenero = New SubGenero;

        if ( $subgenero->consultar(null , $id) ) {

            $contenido = array('modulo' => 'vistas/modulos/subgeneros/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New SubGenero);

        $request = SaveSubGenerosRequest::validated($id);

        $subgenero = New SubGenero;
        $subgenero->id = $id;
        
        $respuesta = $subgenero->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar SubGenero',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El SubGenero fue actualizado correctamente' );
            header("Location:" . Route::names('subgeneros.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar SubGenero',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('subgeneros.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New SubGenero);

        // Sirve para validar el Token
        if ( !SaveSubGenerosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar SubGenero',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('subgeneros.index'));
            die();

        }

        $subgenero = New SubGenero;
        $subgenero->id = $id;
        $respuesta = $subgenero->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Subgenero',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Gasto fue eliminado correctamente' );

            header("Location:" . Route::names('subgeneros.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Subgenero',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('subgeneros.index'));

        }
        
        die();

    }

}
