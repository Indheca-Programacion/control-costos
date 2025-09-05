<?php

namespace App\Controllers;

require_once "app/Models/Carga.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveCargasRequest.php";
require_once "app/Models/Obra.php";


use App\Conexion;
use App\Models\Carga;
use App\Models\Obra;
use App\Requests\SaveCargasRequest;

use App\Route;

class CargasController
{
    public function index()
    {
        Autorizacion::authorize('view', new Carga);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/cargas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $cargas = New Carga;
        Autorizacion::authorize('create', $cargas);

        $obra = New Obra;
        $obras = $obra->consultar();

        $contenido = array('modulo' => 'vistas/modulos/cargas/crear.php');

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
        Autorizacion::authorize('update', new Carga);

        $cargas = New Carga;

        $obra = New Obra;
        $obras = $obra->consultar();

        if ($cargas->consultar(null,$id)) {

            $contenido = array('modulo' => 'vistas/modulos/cargas/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Carga);

        $Carga = New Carga;
        $cargas = $Carga->consultar();
        // var_dump($cargas);
        if ( count($cargas) > 0 ) {

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null,2);


            include "reportes/cargas.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
