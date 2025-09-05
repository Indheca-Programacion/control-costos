<?php

namespace App\Controllers;

require_once "app/Models/Perfil.php";
require_once "app/Policies/PerfilPolicy.php";
// require_once "app/Requests/Request.php";
require_once "app/Requests/SavePerfilesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Perfil;
use App\Policies\PerfilPolicy;
// use App\Requests\Request;
use App\Requests\SavePerfilesRequest;
use App\Route;

class PerfilesController
{
    public function index()
    {
        Autorizacion::authorize('view', new Perfil);

        $perfil = New Perfil;
        $perfiles = $perfil->consultar();

        // include "vistas/modulos/perfiles/index.php";
        $contenido = array('modulo' => 'vistas/modulos/perfiles/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $perfil = new Perfil;
        Autorizacion::authorize('create', $perfil);

        require_once "app/Models/Permiso.php";
        $permiso = New \App\Models\Permiso;
        $permisos = $permiso->consultar(null, null, aplicacionId());

        // include "vistas/modulos/perfiles/crear.php";
        $contenido = array('modulo' => 'vistas/modulos/perfiles/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    // public function store(SaveRoleRequest $request)
    public function store()
    {
        Autorizacion::authorize('create', new Perfil);

        $request = SavePerfilesRequest::validated();

        $perfil = New Perfil;
        $respuesta = $perfil->crear($request);

        // if ( $request->filled('permissions') )
        // {
        //     $role->givePermissionTo($request->permissions);
        // }

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El perfil fue creado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Perfil',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El perfil fue creado correctamente' );
            header("Location:" . Route::names('perfiles.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('perfiles.create'));

        }
        
        die();
    }

    // public function edit(Role $role)
    public function edit($id)
    {        
        Autorizacion::authorize('update', new Perfil);

        $perfil = New Perfil;        

        if ( $perfil->consultar(null , $id) ) {
            $perfil->consultarPermisos();

            require_once "app/Models/Permiso.php";
            $permiso = New \App\Models\Permiso;
            $permisos = $permiso->consultar(null, null, aplicacionId());

            // include "vistas/modulos/perfiles/editar.php";
            $contenido = array('modulo' => 'vistas/modulos/perfiles/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    // public function update(SaveRoleRequest $request, Role $role)
    public function update($id)
    {        
        Autorizacion::authorize('update', new Perfil);
        
        // $request = SavePerfilesRequest::value();
        $request = SavePerfilesRequest::validated();

        $perfil = New Perfil;
        $perfil->id = $id;
        $respuesta = $perfil->actualizar($request);

        // $role->syncPermissions($request->permissions);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El perfil fue actualizado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Perfil',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El perfil fue actualizado correctamente' );
            header("Location:" . Route::names('perfiles.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('perfiles.edit', $id));

        }
        
        die();
    }

    // public function destroy(Role $role)
    public function destroy($id)
    {        
        Autorizacion::authorize('delete', new Perfil);

        // Sirve para validar el Token
        if ( !SavePerfilesRequest::validatingToken($error) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = $error;
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('perfiles.index'));
            die();

        }

        // throw new \Illuminate\Auth\Access\AuthorizationException('No se puede eliminar este
        // Verifica que el usuario no sea Administrador
        $perfil = New Perfil;        
        if ( $perfil->consultar(null , $id) ) {
            
            if ( mb_strtoupper($perfil->nombre) == mb_strtoupper(CONST_ADMIN) ) {

                // $_SESSION[CONST_SESSION_APP]["flash"] = "El perfil 'Administrador' no puede ser eliminado";
                // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => "El perfil 'Administrador' no puede ser eliminado" );
                header("Location:" . Route::names('perfiles.index'));

                die();
            }

        }
        
        $perfil = New Perfil;
        $perfil->id = $id;
        $respuesta = $perfil->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El Perfil fue eliminado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Perfil fue eliminado correctamente' );

            header("Location:" . Route::names('perfiles.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este perfil no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este perfil no se podrá eliminar ***' );
            header("Location:" . Route::names('perfiles.index'));

        }
        
        die();
    }
}
