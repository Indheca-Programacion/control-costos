<?php

namespace App\Controllers;

require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveMaterialCargasRequest.php";
require_once "app/Models/MaterialCarga.php";

use App\Conexion;
use App\Models\MaterialCarga;
use App\Requests\SaveMaterialCargasRequest;

use App\Route;

class MaterialesCargasController
{
    public function index()
    {
        Autorizacion::authorize('view', new MaterialCarga);
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/materiales-cargas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $cargas = New Carga;
        Autorizacion::authorize('create', $cargas);

        $obra = New Obra;
        $obras = $obra->consultar();

        $contenido = array('modulo' => 'vistas/modulos/materiales-cargas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New QrCarga);

        $request = SaveAsistenciasRequest::validated();
        
        $respuesta = $asistencias->crear($datos);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Asistencia',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'Las asistencias fueron creadas correctamente' );
            header("Location:" . Route::names('asistencias.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Asistencia',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('asistencias.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new MaterialCarga);

        $material = New MaterialCarga;

        if ($material->consultar(null,$id)) {

            $contenido = array('modulo' => 'vistas/modulos/materiales-cargas/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New MaterialCarga);

        $request = SaveMaterialCargasRequest::validated($id);

        $material = New MaterialCarga;
        $material->id = $id;

        $respuesta = $material->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Material',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El material fue actualizada correctamente' );
            header("Location:" . Route::names('materiales.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Material',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('materiales.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
    }
}
