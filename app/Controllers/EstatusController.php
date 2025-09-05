<?php

namespace App\Controllers;

require_once "app/Models/Estatus.php";
// require_once "app/Policies/EstatusPolicy.php";
require_once "app/Requests/SaveEstatusRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Estatus;
// use App\Policies\EstatusPolicy;
use App\Requests\SaveEstatusRequest;
use App\Route;

class EstatusController
{
    public function index()
    {
        Autorizacion::authorize('view', New Estatus);

        $estatus = New Estatus;
        $status = $estatus->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/estatus/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $estatus = New Estatus;
        Autorizacion::authorize('create', $estatus);

        $contenido = array('modulo' => 'vistas/modulos/estatus/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Estatus);

        $request = SaveEstatusRequest::validated();

        $estatus = New Estatus;
        $respuesta = $estatus->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue creado correctamente' );
            header("Location:" . Route::names('estatus.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('estatus.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Estatus);

        $estatus = New Estatus;

        if ( $estatus->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/estatus/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Estatus);

        $request = SaveEstatusRequest::validated($id);

        $estatus = New Estatus;
        $estatus->id = $id;
        $respuesta = $estatus->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue actualizado correctamente' );
            header("Location:" . Route::names('estatus.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('estatus.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New Estatus);

        // Sirve para validar el Token
        if ( !SaveEstatusRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('estatus.index'));
            die();

        }

        $estatus = New Estatus;
        $estatus->id = $id;
        $respuesta = $estatus->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue eliminado correctamente' );

            header("Location:" . Route::names('estatus.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('estatus.index'));

        }
        
        die();

    }
}
