<?php

namespace App\Controllers;

require_once "app/Models/Insumo.php";
// require_once "app/Policies/InsumoPolicy.php";
require_once "app/Requests/SaveInsumosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Insumo;
// use App\Policies\InsumoPolicy;
use App\Requests\SaveInsumosRequest;
use App\Route;

class InsumosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Insumo);

        $insumo = New Insumo;
        $insumos = $insumo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/insumos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $insumo = New Insumo;
        Autorizacion::authorize('create', $insumo);

        require_once "app/Models/InsumoTipo.php";
        $insumoTipo = New \App\Models\InsumoTipo;
        $insumoTipos = $insumoTipo->consultar();

        require_once "app/Models/Unidad.php";
        $unidad = New \App\Models\Unidad;
        $unidades = $unidad->consultar();

        $contenido = array('modulo' => 'vistas/modulos/insumos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New Insumo);

        $request = SaveInsumosRequest::validated();

        $insumo = New Insumo;
        $respuesta = $insumo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Insumo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El insumo fue creado correctamente' );
            header("Location:" . Route::names('insumos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('insumos.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Insumo);

        $insumo = New Insumo;

        if ( $insumo->consultar(null , $id) ) {
            require_once "app/Models/InsumoTipo.php";
            $insumoTipo = New \App\Models\InsumoTipo;
            $insumoTipos = $insumoTipo->consultar();

            require_once "app/Models/Unidad.php";
            $unidad = New \App\Models\Unidad;
            $unidades = $unidad->consultar();

            $contenido = array('modulo' => 'vistas/modulos/insumos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Insumo);

        $request = SaveInsumosRequest::validated($id);

        $insumo = New Insumo;
        $insumo->id = $id;
        $respuesta = $insumo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Insumo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El insumo fue actualizado correctamente' );
            header("Location:" . Route::names('insumos-indirectos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('insumos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Insumo);

        // Sirve para validar el Token
        if ( !SaveInsumosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('insumos-indirectos.index'));
            die();

        }

        $insumo = New Insumo;
        $insumo->id = $id;
        $respuesta = $insumo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Insumo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El insumo fue eliminado correctamente' );

            header("Location:" . Route::names('insumos-indirectos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Insumo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este insumo no se podrá eliminar ***' );
            header("Location:" . Route::names('insumos-indirectos.index'));

        }
        
        die();
    }
}
