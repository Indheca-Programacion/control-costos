<?php

namespace App\Controllers;

require_once "app/Models/GastosTipos.php";
require_once "app/Requests/SaveGastosTiposRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\GastosTipos;
use App\Requests\SaveGastosTiposRequest;
use App\Route;

class GastosTiposController
{
    public function index()
    {
        Autorizacion::authorize('view', New GastosTipos);

        $gastos = New GastosTipos;
        $gasto = $gastos->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/gastos-tipos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $gastos = New GastosTipos;
        Autorizacion::authorize('create', $gastos);

        $contenido = array('modulo' => 'vistas/modulos/gastos-tipos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New GastosTipos);

        $request = SaveGastosTiposRequest::validated();

        $gastos = New GastosTipos;
        $respuesta = $gastos->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tipo de Gasto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de gasto fue creado correctamente' );
            header("Location:" . Route::names('gastos-tipos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tipo de Gasto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('gastos-tipos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New GastosTipos);

        $gastosTipos = New GastosTipos;

        if ( $gastosTipos->consultar(null , $id) ) {

            $contenido = array('modulo' => 'vistas/modulos/gastos-tipos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New GastosTipos);

        $request = SaveGastosTiposRequest::validated($id);

        $gastos = New GastosTipos;
        $gastos->id = $id;
        
        $respuesta = $gastos->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tipos de Gastos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de gasto fue actualizado correctamente' );
            header("Location:" . Route::names('gastos-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tipos de Gastos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('gastos-tipos.edit', $id));

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
