<?php

namespace App\Controllers;

require_once "app/Models/IndirectoTipo.php";
// require_once "app/Policies/IndirectoTipoPolicy.php";
require_once "app/Requests/SaveIndirectoTiposRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\IndirectoTipo;
// use App\Policies\IndirectoTipoPolicy;
use App\Requests\SaveIndirectoTiposRequest;
use App\Route;

class IndirectoTiposController
{
    public function index()
    {
        Autorizacion::authorize('view', New IndirectoTipo);

        $indirectoTipo = New IndirectoTipo;
        $indirectoTipos = $indirectoTipo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/indirecto-tipos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $indirectoTipo = New IndirectoTipo;
        Autorizacion::authorize('create', $indirectoTipo);

        require_once "app/Models/Perfil.php";
        $perfil = New \App\Models\Perfil;

        $perfiles = $perfil->consultar();

        $contenido = array('modulo' => 'vistas/modulos/indirecto-tipos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New IndirectoTipo);

        $request = SaveIndirectoTiposRequest::validated();

        $indirectoTipo = New IndirectoTipo;
        $respuesta = $indirectoTipo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tipo de Indirecto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de indirecto fue creado correctamente' );
            header("Location:" . Route::names('indirecto-tipos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tipo de Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('indirecto-tipos.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New IndirectoTipo);

        $indirectoTipo = New IndirectoTipo;

        require_once "app/Models/Perfil.php";
        $perfil = New \App\Models\Perfil;

        $perfiles = $perfil->consultar();

        if ( $indirectoTipo->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/indirecto-tipos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New IndirectoTipo);

        $request = SaveIndirectoTiposRequest::validated($id);

        $indirectoTipo = New IndirectoTipo;
        $indirectoTipo->id = $id;
        $respuesta = $indirectoTipo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tipo de Indirecto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de indirecto fue actualizado correctamente' );
            header("Location:" . Route::names('indirecto-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tipo de Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('indirecto-tipos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New IndirectoTipo);

        // Sirve para validar el Token
        if ( !SaveIndirectoTiposRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('indirecto-tipos.index'));
            die();

        }

        $indirectoTipo = New IndirectoTipo;
        $indirectoTipo->id = $id;
        $respuesta = $indirectoTipo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tipo de Indirecto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de indirecto fue eliminado correctamente' );

            header("Location:" . Route::names('indirecto-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tipo de indirecto no se podrá eliminar ***' );
            header("Location:" . Route::names('indirecto-tipos.index'));

        }
        
        die();
    }
}
