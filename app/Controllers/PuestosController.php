<?php

namespace App\Controllers;

require_once "app/Models/Puesto.php";
require_once "app/Models/PuestoSuperiorInferior.php";

require_once "app/Policies/PuestoPolicy.php";
require_once "app/Requests/SavePuestosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Puesto;
use App\Models\PuestoSuperiorInferior;

use App\PoliciesPuestoPolicy;
use App\Requests\SavePuestosRequest;
use App\Route;

class PuestosController
{
    public function index()
    {
        Autorizacion::authorize('view', new Puesto);

        $puesto = New Puesto;
        $puestos = $puesto->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/puestos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $puesto = new Puesto;
        Autorizacion::authorize('create', $puesto);

        $contenido = array('modulo' => 'vistas/modulos/puestos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Puesto);

        $request = SavePuestosRequest::validated();

        $puesto = New Puesto;
        $respuesta = $puesto->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Puesto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El puesto fue creado correctamente' );
            header("Location:" . Route::names('puestos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Puesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('puestos.create'));
        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Puesto);

        $puesto = New Puesto;
        $puestos = $puesto->consultar();

        foreach ($puestos as $index => $value) {
            if ($value["id"] == $id) {
                unset($puestos[$index]);
            }
        }

        //OBTENER LOS PUESTOS SUPERIORES E INFERIORES
        $puestoSEI = new PuestoSuperiorInferior;
        $puestoSEI->consultar(null,$id);

        if ( $puesto->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/puestos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Puesto);

        $request = SavePuestosRequest::validated($id);  

        $puesto = New Puesto;
        $puesto->id = $id;
        $respuesta = $puesto->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Puesto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El color fue actualizado correctamente' );
            header("Location:" . Route::names('puestos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Puesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('puestos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Puesto);

        // Sirve para validar el Token
        if ( !SavePuestosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Puesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('puestos.index'));
            die();

        }

        $puesto = New Puesto;
        $puesto->id = $id;
        $respuesta = $puesto->eliminar(false);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Puesto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El puesto fue eliminado correctamente' );

            header("Location:" . Route::names('puestos.index'));

        } else {             

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Puesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este color no se podrá eliminar ***' );
            header("Location:" . Route::names('puestos.index'));

        }
        
        die();

    }

    
}
