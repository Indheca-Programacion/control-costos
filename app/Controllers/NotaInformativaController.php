<?php

namespace App\Controllers;

require_once "app/Models/NotaInformativa.php";
require_once "app/Policies/NotaInformativaPolicy.php";
require_once "app/Requests/SaveNotaInformativaRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\NotaInformativa;
use App\Policies\NotaInformativaPolicy;
use App\Requests\SaveNotaInformativaRequest;
use App\Route;

class NotaInformativaController
{
    public function index()
    {
        Autorizacion::authorize('view', New NotaInformativa);

        $NotaInformativa = New NotaInformativa;
        $NotaInformativas = $NotaInformativa->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/nota-informativa/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $NotaInformativa = New NotaInformativa;
        Autorizacion::authorize('create', $NotaInformativa);

        $formularioEditable = true;
        $contenido = array('modulo' => 'vistas/modulos/nota-informativa/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New NotaInformativa);

        $request = SaveNotaInformativaRequest::validated();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario = $usuario->consultar(null,usuarioAutenticado()["id"]);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Nota Informativa',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La Nota Informativa fue creada correctamente' );
            header("Location:" . Route::names('NotaInformativas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Nota Informativa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('NotaInformativas.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New NotaInformativa);

        $NotaInformativa = New NotaInformativa;

        if ( $NotaInformativa->consultar(null , $id) ) {

            $NotaInformativa->consultarArchivos();

            $contenido = array('modulo' => 'vistas/modulos/nota-informativa/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New NotaInformativa);

        $request = SaveNotaInformativasRequest::validated($id);

        // Validar que la fechaFinalizacion no sea menor a fechaInicio
        if ( $request['fechaFinalizacion'] != ''  ) {

            $fechaInicio = \DateTime::createFromFormat('Y-m-d', fFechaSQL($request['fechaInicio']));
            $fechaFinalizacion = \DateTime::createFromFormat('Y-m-d', fFechaSQL($request['fechaFinalizacion']));
            
            if ( $fechaFinalizacion < $fechaInicio ) {

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                               'titulo' => 'Actualizar NotaInformativa',
                                                               'subTitulo' => 'Error',
                                                               'mensaje' => 'La fecha de finalización no puede ser menor a la fecha de inicio' );
                header("Location:" . Route::names('NotaInformativas.edit', $id));

                die();

            }

        }

        $NotaInformativa = New NotaInformativa;
        $NotaInformativa->id = $id;
        $respuesta = $NotaInformativa->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar NotaInformativa',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La NotaInformativa fue actualizada correctamente' );
            header("Location:" . Route::names('NotaInformativas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar NotaInformativa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('NotaInformativas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New NotaInformativa);

        // Sirve para validar el Token
        if ( !SaveNotaInformativaRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar NotaInformativa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('NotaInformativas.index'));
            die();

        }

        $NotaInformativa = New NotaInformativa;
        $NotaInformativa->id = $id;
        $respuesta = $NotaInformativa->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar NotaInformativa',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La NotaInformativa fue eliminada correctamente' );

            header("Location:" . Route::names('nota-informativa.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar NotaInformativa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta NotaInformativa no se podrá eliminar ***' );
            header("Location:" . Route::names('nota-informativa.index'));

        }
        
        die();
    }
}
