<?php

namespace App\Controllers;

require_once "app/Models/Permiso.php";
require_once "app/Policies/PermisoPolicy.php";
require_once "app/Requests/SavePermisosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Permiso;
use App\Policies\PermisoPolicy;
use App\Requests\SavePermisosRequest;
use App\Route;

class PermisosController
{
    public function index()
    {
        Autorizacion::authorize('view', new Permiso);

        $permiso = New Permiso;
        $permisos = $permiso->consultar();

        // include "vistas/modulos/permisos/index.php";
        $contenido = array('modulo' => 'vistas/modulos/permisos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $permiso = new Permiso;
        Autorizacion::authorize('create', $permiso);

        require_once "app/Models/Aplicacion.php";
        $aplicacion = New \App\Models\Aplicacion;
        $aplicaciones = $aplicacion->consultar();

        // include "vistas/modulos/permisos/crear.php";
        $contenido = array('modulo' => 'vistas/modulos/permisos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', new Permiso);

        $request = SavePermisosRequest::validated();

        $permiso = New Permiso;
        $respuesta = $permiso->crear($request);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El permiso fue creado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Permiso',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El permiso fue creado correctamente' );
            header("Location:" . Route::names('permisos.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('permisos.create'));

        }
        
        die();
    }

    public function edit($id)
    {        
        Autorizacion::authorize('update', new Permiso);

        $permiso = New Permiso;

        if ( $permiso->consultar(null , $id) ) {

            $permiso->consultarAplicaciones();

            require_once "app/Models/Aplicacion.php";
            $aplicacion = New \App\Models\Aplicacion;
            $aplicaciones = $aplicacion->consultar();

            // include "vistas/modulos/permisos/editar.php";
            $contenido = array('modulo' => 'vistas/modulos/permisos/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {        
        Autorizacion::authorize('update', new Permiso);
        
        $request = SavePermisosRequest::validated($id);

        $permiso = New Permiso;
        $permiso->id = $id;
        $respuesta = $permiso->actualizar($request);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El permiso fue actualizado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Permiso',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El permiso fue actualizado correctamente' );
            header("Location:" . Route::names('permisos.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('permisos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {        
        Autorizacion::authorize('delete', new Permiso);

        // Sirve para validar el Token
        if ( !SavePermisosRequest::validatingToken($error) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = $error;
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('permisos.index'));
            die();

        }
        
        $permiso = New Permiso;
        $permiso->id = $id;
        $respuesta = $permiso->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El permiso fue eliminado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Permiso',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El permiso fue eliminado correctamente' );

            header("Location:" . Route::names('permisos.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este permiso no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este permiso no se podrá eliminar ***' );
            header("Location:" . Route::names('permisos.index'));

        }
        
        die();
    }
}
