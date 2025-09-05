<?php

namespace App\Controllers;

require_once "app/Models/InsumoTipo.php";
// require_once "app/Policies/InsumoTipoPolicy.php";
require_once "app/Requests/SaveInsumoTiposRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\InsumoTipo;
// use App\Policies\InsumoTipoPolicy;
use App\Requests\SaveInsumoTiposRequest;
use App\Route;

class InsumoTiposController
{
    public function index()
    {
        Autorizacion::authorize('view', New InsumoTipo);

        $insumoTipo = New InsumoTipo;
        $insumoTipos = $insumoTipo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/insumo-tipos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $insumoTipo = New InsumoTipo;
        Autorizacion::authorize('create', $insumoTipo);

        require_once "app/Models/Perfil.php";
        $perfil = New \App\Models\Perfil;

        $perfiles = $perfil->consultar();

        $contenido = array('modulo' => 'vistas/modulos/insumo-tipos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New InsumoTipo);

        $request = SaveInsumoTiposRequest::validated();

        $insumoTipo = New InsumoTipo;
        $respuesta = $insumoTipo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tipo de Insumo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de insumo fue creado correctamente' );
            header("Location:" . Route::names('insumo-tipos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tipo de Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('insumo-tipos.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New InsumoTipo);

        $insumoTipo = New InsumoTipo;

        require_once "app/Models/Perfil.php";
        $perfil = New \App\Models\Perfil;

        $perfiles = $perfil->consultar();

        if ( $insumoTipo->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/insumo-tipos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New InsumoTipo);

        $request = SaveInsumoTiposRequest::validated($id);

        $insumoTipo = New InsumoTipo;
        $insumoTipo->id = $id;
        $respuesta = $insumoTipo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tipo de Insumo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de insumo fue actualizado correctamente' );
            header("Location:" . Route::names('insumo-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tipo de Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('insumo-tipos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New InsumoTipo);

        // Sirve para validar el Token
        if ( !SaveInsumoTiposRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('insumo-tipos.index'));
            die();

        }

        $insumoTipo = New InsumoTipo;
        $insumoTipo->id = $id;
        $respuesta = $insumoTipo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tipo de Insumo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de insumo fue eliminado correctamente' );

            header("Location:" . Route::names('insumo-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tipo de insumo no se podrá eliminar ***' );
            header("Location:" . Route::names('insumo-tipos.index'));

        }
        
        die();
    }
}
