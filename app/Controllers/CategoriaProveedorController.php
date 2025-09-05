<?php

namespace App\Controllers;

require_once "app/Models/CategoriaProveedor.php";
require_once "app/Policies/CategoriaProveedorPolicy.php";
require_once "app/Requests/SaveCategoriaProveedorRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\CategoriaProveedor;
use App\Policies\CategoriaProveedorPolicy;
use App\Requests\SaveCategoriaProveedorRequest;
use App\Route;

class CategoriaProveedorController
{
    public function index()
    {
        Autorizacion::authorize('view', new CategoriaProveedor);

        $categoriaProveedor = New CategoriaProveedor;
        $categoriaProveedor = $categoriaProveedor->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/categoria-proveedores/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $categoriaProveedor = new CategoriaProveedor;
        Autorizacion::authorize('create', $categoriaProveedor);

        $contenido = array('modulo' => 'vistas/modulos/categoria-proveedores/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New CategoriaProveedor);

        $request = SaveCategoriaProveedorRequest::validated();

        $categoriaProveedor = New CategoriaProveedor;
        $respuesta = $categoriaProveedor->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Categoria Proveedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La categoria del proveedor fue creada correctamente' );
            header("Location:" . Route::names('categoria-proveedores.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Categoria Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('categoria-proveedores.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new CategoriaProveedor);

        $categoriaProveedor = New CategoriaProveedor;

        if ( $categoriaProveedor->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/categoria-proveedores/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new CategoriaProveedor);

        $request = SaveCategoriaProveedorRequest::validated($id);

        $categoriaProveedor = New CategoriaProveedor;
        $categoriaProveedor->id = $id;
        $respuesta = $categoriaProveedor->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Categoria Proveedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La categoria del proveedor fue actualizada correctamente' );
            header("Location:" . Route::names('categoria-proveedores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Categoria Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('categoria-proveedores.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new CategoriaProveedor);

        // Sirve para validar el Token
        if ( !SaveCategoriaProveedorRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Categoria Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('categoria-proveedores.index'));
            die();

        }

        $categoriaProveedor = New CategoriaProveedor;
        // $empresa->id = $id;
        $categoriaProveedor->consultar(null , $id); // Para tener las rutas de las imágenes 
        $respuesta = $categoriaProveedor->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar  Categoria Proveedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La categoria fue eliminada correctamente' );

            header("Location:" . Route::names('categoria-proveedores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar  Categoria Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta empresa no se podrá eliminar ***' );
            header("Location:" . Route::names('categoria-proveedores.index'));

        }
        
        die();

    }
}
