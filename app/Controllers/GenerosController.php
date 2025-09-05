<?php

namespace App\Controllers;

require_once "app/Models/Genero.php";
require_once "app/Requests/SaveGenerosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Genero;
use App\Requests\SaveGenerosRequest;
use App\Route;

class GenerosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Genero);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/generos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $genero = New Genero;
        Autorizacion::authorize('create', $genero);

        $contenido = array('modulo' => 'vistas/modulos/generos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Genero);

        $request = SaveGenerosRequest::validated();

        $genero = New Genero;
        $respuesta = $genero->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Generos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El genero fue creado correctamente' );
            header("Location:" . Route::names('generos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Generos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('generos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Genero);

        $genero = New Genero;

        if ( $genero->consultar(null , $id) ) {

            $contenido = array('modulo' => 'vistas/modulos/generos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Genero);

        $request = SaveGenerosRequest::validated($id);

        $genero = New Genero;
        $genero->id = $id;
        
        $respuesta = $genero->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Genero',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Genero fue actualizado correctamente' );
            header("Location:" . Route::names('generos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Genero',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('generos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New Genero);

        // Sirve para validar el Token
        if ( !SaveGenerosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('generos.index'));
            die();

        }

        $genero = New Genero;
        $genero->id = $id;
        $respuesta = $genero->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Generos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Genero fue eliminado correctamente' );

            header("Location:" . Route::names('generos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('generos.index'));

        }
        
        die();

    }

}
