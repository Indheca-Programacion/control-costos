<?php

namespace App\Controllers;

require_once "app/Models/Plantilla.php";
require_once "app/Requests/SavePlantillasRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Plantilla;
use App\Requests\SavePlantillasRequest;
use App\Route;

class PlantillasController 
{

    public function index(){
        Autorizacion::authorize('view', new Plantilla);

        $contenido = array('modulo' => 'vistas/modulos/plantillas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create(){
        Autorizacion::authorize('create', new Plantilla);

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        $contenido = array('modulo' => 'vistas/modulos/plantillas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store(){
        Autorizacion::authorize('create', New Plantilla);

        $request = SavePlantillasRequest::validated();

        $plantilla = New Plantilla;
        $respuesta = $plantilla->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Plantilla',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La plantilla fue creada correctamente' );
            header("Location:" . Route::names('plantillas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Plantilla',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('plantillas.create'));

        }
        
        die();
    }

    public function edit($id)
    {        
        Autorizacion::authorize('update', new Plantilla);

        $plantilla = New Plantilla;

        if ( $plantilla->consultar(null , $id) ) {

            $detalles = $plantilla->consultarDetalles($id);

            require_once "app/Models/Obra.php";
            $Obra = New \App\Models\Obra;
            $obras = $Obra->consultar();

            require_once "app/Models/Insumo.php";
            $Insumo = New \App\Models\Insumo;
            $Insumos = $Insumo->consultarPorPlantilla($id);

            require_once "app/Models/Indirecto.php";
            $Indirecto = New \App\Models\Indirecto;
            $Indirectos = $Indirecto->consultarPorPlantilla($id);

            $contenido = array('modulo' => 'vistas/modulos/plantillas/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {        
        Autorizacion::authorize('update', new Plantilla);
        
        $request = SavePlantillasRequest::validated($id);

        $plantilla = New Plantilla;
        $plantilla->id = $id;
        $respuesta = $plantilla->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Plantilla',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La plantilla fue actualizada correctamente' );
            header("Location:" . Route::names('plantillas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Plantilla',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('plantillas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {        
        Autorizacion::authorize('delete', new Plantilla);

        // Sirve para validar el Token
        if ( !SavePlantillasRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Plantilla',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('permisos.index'));
            die();

        }
        
        $plantilla = New Plantilla;
        $plantilla->id = $id;
        $respuesta = $plantilla->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Plantilla',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La plantilla fue eliminada correctamente' );

            header("Location:" . Route::names('plantillas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Plantilla',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este permiso no se podrá eliminar ***' );
            header("Location:" . Route::names('plantillas.index'));

        }
        
        die();
    }
}