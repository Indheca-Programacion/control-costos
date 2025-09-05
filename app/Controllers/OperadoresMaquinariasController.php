<?php

namespace App\Controllers;

require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveOperadorMaquinariasRequest.php";
require_once "app/Models/OperadorMaquinaria.php";


use App\Conexion;
use App\Models\OperadorMaquinaria;
use App\Requests\SaveOperadorMaquinariasRequest;

use App\Route;

class OperadoresMaquinariasController
{
    public function index()
    {
        Autorizacion::authorize('view', new OperadorMaquinaria);
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/operadores-maquinarias/index.php');

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
        Autorizacion::authorize('update', new OperadorMaquinaria);

        $operador = New OperadorMaquinaria;

        if ($operador->consultar(null,$id)) {

            $contenido = array('modulo' => 'vistas/modulos/operadores-maquinarias/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
    public function update($id)
    {
        Autorizacion::authorize('update', New OperadorMaquinaria);

        $request = SaveOperadorMaquinariasRequest::validated($id);

        $operador = New OperadorMaquinaria;
        $operador->id = $id;

        $respuesta = $operador->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Operador',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El operador fue actualizada correctamente' );
            header("Location:" . Route::names('operadores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Operador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('operadores.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
    }
}
