<?php

namespace App\Controllers;

require_once "app/Models/Indirecto.php";
// require_once "app/Policies/IndirectoPolicy.php";
require_once "app/Requests/SaveIndirectosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Indirecto;
// use App\Policies\IndirectoPolicy;
use App\Requests\SaveIndirectosRequest;
use App\Route;

class IndirectosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Indirecto);

        $indirecto = New Indirecto;
        $indirectos = $indirecto->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/indirectos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $indirecto = New Indirecto;
        Autorizacion::authorize('create', $indirecto);

        require_once "app/Models/IndirectoTipo.php";
        $indirectoTipo = New \App\Models\IndirectoTipo;
        $indirectoTipos = $indirectoTipo->consultar();

        require_once "app/Models/Unidad.php";
        $unidad = New \App\Models\Unidad;
        $unidades = $unidad->consultar();

        $contenido = array('modulo' => 'vistas/modulos/indirectos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New Indirecto);

        $request = SaveIndirectosRequest::validated();

        $indirecto = New Indirecto;
        $respuesta = $indirecto->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Indirecto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El indirecto fue creado correctamente' );
            header("Location:" . Route::names('indirectos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('indirectos.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Indirecto);

        $indirecto = New Indirecto;

        if ( $indirecto->consultar(null , $id) ) {
            require_once "app/Models/IndirectoTipo.php";
            $indirectoTipo = New \App\Models\IndirectoTipo;
            $indirectoTipos = $indirectoTipo->consultar();

            require_once "app/Models/Unidad.php";
            $unidad = New \App\Models\Unidad;
            $unidades = $unidad->consultar();

            $contenido = array('modulo' => 'vistas/modulos/indirectos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Indirecto);

        $request = SaveIndirectosRequest::validated($id);

        $indirecto = New Indirecto;
        $indirecto->id = $id;
        $respuesta = $indirecto->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Indirecto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El indirecto fue actualizado correctamente' );
            header("Location:" . Route::names('insumos-indirectos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('indirectos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Indirecto);

        // Sirve para validar el Token
        if ( !SaveIndirectosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('insumos-indirectos.index'));
            die();

        }

        $indirecto = New Indirecto;
        $indirecto->id = $id;
        $respuesta = $indirecto->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Indirecto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El indirecto fue eliminado correctamente' );

            header("Location:" . Route::names('insumos-indirectos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Indirecto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este indirecto no se podrá eliminar ***' );
            header("Location:" . Route::names('insumos-indirectos.index'));

        }
        
        die();
    }
}
