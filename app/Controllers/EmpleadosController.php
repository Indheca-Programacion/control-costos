<?php

namespace App\Controllers;

require_once "app/Models/Empleado.php";
require_once "app/Policies/EmpleadoPolicy.php";
require_once "app/Requests/SaveEmpleadosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Empleado;
use App\Policies\EmpleadoPolicy;
use App\Requests\SaveEmpleadosRequest;
use App\Route;

class EmpleadosController
{
    public function index()
    {
        Autorizacion::authorize('view', new Empleado);

        $empleado = New Empleado;
        $empleados = $empleado->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/empleados/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $empleado = new Empleado;
        Autorizacion::authorize('create', $empleado);

        require_once "app/Models/EmpleadoFuncion.php";
        $empleadoFuncion = New \App\Models\EmpleadoFuncion;
        $empleadoFunciones = $empleadoFuncion->consultar();

        $contenido = array('modulo' => 'vistas/modulos/empleados/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New Empleado);

        $request = SaveEmpleadosRequest::validated();

        $empleado = New Empleado;
        $respuesta = $empleado->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Empleado',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El empleado fue creado correctamente' );
            header("Location:" . Route::names('empleados.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Empleado',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('empleados.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Empleado);

        $empleado = New Empleado;

        if ( $empleado->consultar(null , $id) ) {
            require_once "app/Models/EmpleadoFuncion.php";
            $empleadoFuncion = New \App\Models\EmpleadoFuncion;
            $empleadoFunciones = $empleadoFuncion->consultar();

            $contenido = array('modulo' => 'vistas/modulos/empleados/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Empleado);

        $request = SaveEmpleadosRequest::validated($id);

        $empleado = New Empleado;
        $empleado->id = $id;
        $respuesta = $empleado->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Empleado',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El empleado fue actualizado correctamente' );
            header("Location:" . Route::names('empleados.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Empleado',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('empleados.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Empleado);

        // Sirve para validar el Token
        if ( !SaveEmpleadosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Empleado',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('empleados.index'));
            die();

        }

        $empleado = New Empleado;
        // $empleado->id = $id;
        $empleado->consultar(null , $id); // Para tener la ruta de la foto
        $respuesta = $empleado->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Empleado',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El empleado fue eliminado correctamente' );

            header("Location:" . Route::names('empleados.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Empleado',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este empleado no se podrá eliminar ***' );
            header("Location:" . Route::names('empleados.index'));

        }
        
        die();

    }
}
