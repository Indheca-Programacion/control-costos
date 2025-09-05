<?php

namespace App\Controllers;

require_once "app/Models/Sucursal.php";
require_once "app/Policies/SucursalPolicy.php";
require_once "app/Requests/SaveSucursalesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Sucursal;
use App\Policies\SucursalPolicy;
use App\Requests\SaveSucursalesRequest;
use App\Route;

class SucursalesController
{
    public function index()
    {
        Autorizacion::authorize('view', new Sucursal);

        $sucursal = New Sucursal;
        $sucursales = $sucursal->consultar();

        // include "vistas/modulos/sucursales/index.php";

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/sucursales/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $sucursal = new Sucursal;
        Autorizacion::authorize('create', $sucursal);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        // include "vistas/modulos/sucursales/crear.php";
        $contenido = array('modulo' => 'vistas/modulos/sucursales/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Sucursal);

        $request = SaveSucursalesRequest::validated();

        $sucursal = New Sucursal;
        $respuesta = $sucursal->crear($request);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La sucursal fue creada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Sucursal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La sucursal fue creada correctamente' );
            header("Location:" . Route::names('sucursales.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Sucursal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('sucursales.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Sucursal);

        $sucursal = New Sucursal;

        if ( $sucursal->consultar(null , $id) ) {

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            // include "vistas/modulos/sucursales/editar.php";
            $contenido = array('modulo' => 'vistas/modulos/sucursales/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {

            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";

        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Sucursal);

        $request = SaveSucursalesRequest::validated($id);

        $sucursal = New Sucursal;
        $sucursal->id = $id;
        $respuesta = $sucursal->actualizar($request);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La sucursal fue actualizada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Sucursal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La sucursal fue actualizada correctamente' );
            header("Location:" . Route::names('sucursales.index'));

        } else {            

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Sucursal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('sucursales.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Sucursal);

        // Sirve para validar el Token
        if ( !SaveSucursalesRequest::validatingToken($error) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = $error;
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Sucursal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('sucursales.index'));
            die();

        }

        $sucursal = New Sucursal;
        $sucursal->id = $id;
        $respuesta = $sucursal->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La sucursal fue eliminada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Sucursal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La sucursal fue eliminada correctamente' );
            header("Location:" . Route::names('sucursales.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Sucursal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***' );
            header("Location:" . Route::names('sucursales.index'));

        }
        
        die();

    }
}
