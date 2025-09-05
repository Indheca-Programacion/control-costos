<?php

namespace App\Controllers;

require_once "app/Models/PermisoCategoriaProveedor.php";
require_once "app/Policies/PermisoCategoriaProveedorPolicy.php";
require_once "app/Requests/SavePermisoCategoriaProveedorRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\PermisoCategoriaProveedor;
use App\Policies\PermisoCategoriaProveedorPolicy;
use App\Requests\SavePermisoCategoriaProveedorRequest;
use App\Route;

class PermisoCategoriaProveedorController
{
    public function index()
    {
        Autorizacion::authorize('view', new PermisoCategoriaProveedor);

        $permisoCategoriaProveedor = New PermisoCategoriaProveedor;
        $permisoCategoriaProveedor = $permisoCategoriaProveedor->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/permisos-categoria-proveedores/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $permisoCategoriaProveedor = new PermisoCategoriaProveedor;
        Autorizacion::authorize('create', $permisoCategoriaProveedor);

        $contenido = array('modulo' => 'vistas/modulos/permisos-categoria-proveedores/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New PermisoCategoriaProveedor);

        $request = SavePermisoCategoriaProveedorRequest::validated();

        $permisoCategoriaProveedor = New PermisoCategoriaProveedor;
        $respuesta = $permisoCategoriaProveedor->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Permiso',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El permiso fue creada correctamente' );
            header("Location:" . Route::names('permiso-proveedor.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('permiso-proveedor.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new PermisoCategoriaProveedor);

        $permisoCategoriaProveedor = New PermisoCategoriaProveedor;

        if ( $permisoCategoriaProveedor->consultar(null , $id) ) {

            $contenido = array('modulo' => 'vistas/modulos/permisos-categoria-proveedores/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new PermisoCategoriaProveedor);

        $request = SavePermisoCategoriaProveedorRequest::validated($id);

        $permisoCategoriaProveedor = New PermisoCategoriaProveedor;
        $permisoCategoriaProveedor->id = $id;
        $respuesta = $permisoCategoriaProveedor->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Permiso',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El permiso fue actualizada correctamente' );
            header("Location:" . Route::names('permiso-proveedor.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('permiso-proveedor.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new PermisoCategoriaProveedor);

        // Sirve para validar el Token
        if ( !SavePermisoCategoriaProveedorRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('permiso-proveedor.index'));
            die();

        }

        $permisoCategoriaProveedor = New PermisoCategoriaProveedor;
        // $empresa->id = $id;
        $permisoCategoriaProveedor->consultar(null , $id); // Para tener las rutas de las imágenes 
        $respuesta = $permisoCategoriaProveedor->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Permiso',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El permiso fue eliminada correctamente' );

            header("Location:" . Route::names('permiso-proveedor.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Permiso',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta empresa no se podrá eliminar ***' );
            header("Location:" . Route::names('permiso-proveedor.index'));

        }
        
        die();

    }
}
