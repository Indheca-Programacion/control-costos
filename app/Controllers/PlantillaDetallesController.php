<?php

namespace App\Controllers;

require_once "app/Models/Plantilla.php";
require_once "app/Models/PlantillaDetalles.php";
require_once "app/Requests/SavePlantillaDetallesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Plantilla;
use App\Models\PlantillaDetalles;
use App\Requests\SavePlantillaDetallesRequest;
use App\Route;

class PlantillaDetallesController 
{

    public function index(){
        Autorizacion::authorize('view', new Plantilla);

        include "vistas/modulos/plantilla.php";
    }

    public function create(){
        Autorizacion::authorize('create', new Plantilla);

        include "vistas/modulos/plantilla.php";
    }

    public function store(){
    }

    public function edit($id)
    {        
        Autorizacion::authorize('update', new Plantilla);

        $plantilla = New PlantillaDetalles;

        if ( $plantilla->consultar(null , $id) ) {

            $contenido = array('modulo' => 'vistas/modulos/plantilla-detalles/editar.php');

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
        
        $request = SavePlantillaDetallesRequest::validated($id);

        $plantilla = New PlantillaDetalles;
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
            
            header("Location:" . Route::names('plantillas.edit', $_POST["plantilla"]));

        }
        
        die();
    }

    public function destroy($id)
    {        
        Autorizacion::authorize('delete', new Plantilla);

        // Sirve para validar el Token
        if ( !SavePlantillaDetallesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Plantilla',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('permisos.index'));
            die();

        }
        
        $plantilla = New PlantillaDetalles;
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