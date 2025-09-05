<?php

namespace App\Controllers;

require_once "app/Models/Empresa.php";
require_once "app/Policies/EmpresaPolicy.php";
require_once "app/Requests/SaveEmpresasRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Empresa;
use App\Policies\EmpresaPolicy;
use App\Requests\SaveEmpresasRequest;
use App\Route;

class EmpresasController
{
    public function index()
    {
        Autorizacion::authorize('view', new Empresa);

        $empresa = New Empresa;
        $empresas = $empresa->consultar();

        // include "vistas/modulos/empresas/index.php";

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/empresas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $empresa = new Empresa;
        Autorizacion::authorize('create', $empresa);

        // include "vistas/modulos/empresas/crear.php";
        $contenido = array('modulo' => 'vistas/modulos/empresas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Empresa);

        $request = SaveEmpresasRequest::validated();

        $empresa = New Empresa;
        $respuesta = $empresa->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Empresa',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La empresa fue creada correctamente' );
            header("Location:" . Route::names('empresas.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Empresa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('empresas.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Empresa);

        $empresa = New Empresa;

        if ( $empresa->consultar(null , $id) ) {
            // include "vistas/modulos/empresas/editar.php";
            $contenido = array('modulo' => 'vistas/modulos/empresas/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Empresa);

        $request = SaveEmpresasRequest::validated($id);

        $empresa = New Empresa;
        $empresa->id = $id;
        $respuesta = $empresa->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Empresa',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La empresa fue actualizada correctamente' );
            header("Location:" . Route::names('empresas.index'));

        } else {            

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Empresa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('empresas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Empresa);

        // Sirve para validar el Token
        if ( !SaveEmpresasRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Empresa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('empresas.index'));
            die();

        }

        $empresa = New Empresa;
        // $empresa->id = $id;
        $empresa->consultar(null , $id); // Para tener las rutas de las imágenes 
        $respuesta = $empresa->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Empresa',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La empresa fue eliminada correctamente' );

            header("Location:" . Route::names('empresas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Empresa',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta empresa no se podrá eliminar ***' );
            header("Location:" . Route::names('empresas.index'));

        }
        
        die();

    }
}
