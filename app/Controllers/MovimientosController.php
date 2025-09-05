<?php

namespace App\Controllers;

require_once "app/Models/Movimientos.php";
require_once "app/Requests/SaveMovimientosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Movimientos;
use App\Requests\SaveMovimientosRequest;
use App\Route;

class MovimientosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Movimientos);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/movimientos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $movimientos = New Movimientos;
        Autorizacion::authorize('create', $movimientos);

        require_once "app/Models/Obra.php";
        $obras =New \App\Models\Obra;
        $obras = $obras->consultar();

        $contenido = array('modulo' => 'vistas/modulos/movimientos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Movimientos);

        $request = SaveGastosRequest::validated();

        $movimientos = New Movimientos;
        $respuesta = $movimientos->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Movimiento',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El movimiento fue creado correctamente' );
            header("Location:" . Route::names('movimientos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Movimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('movimientos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Movimientos);

        $movimientos = New Movimientos;

        if ( $movimientos->consultar(null , $id) ) {


            $contenido = array('modulo' => 'vistas/modulos/movimientos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Movimientos);

        $request = SaveMovimientosRequest::validated($id);

        $movimientos = New Movimientos;
        $movimientos->id = $id;
        
        $respuesta = $movimientos->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Movimientos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Movimiento fue actualizado correctamente' );
            header("Location:" . Route::names('movimientos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Movimientos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('movimientos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New Movimientos);

        // Sirve para validar el Token
        if ( !SaveMovimientosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Movimientos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('movimientos.index'));
            die();

        }

        $movimientos = New Movimientos;
        $movimientos->id = $id;
        $respuesta = $movimientos->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Movimientos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Movimiento fue eliminado correctamente' );

            header("Location:" . Route::names('movimientos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Movimientos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('movimientos.index'));

        }
        
        die();

    }
}
