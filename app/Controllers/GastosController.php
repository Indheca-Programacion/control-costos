<?php

namespace App\Controllers;

require_once "app/Models/Gastos.php";
require_once "app/Requests/SaveGastosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Gastos;
use App\Requests\SaveGastosRequest;
use App\Route;

class GastosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Gastos);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/gastos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $gastos = New Gastos;
        Autorizacion::authorize('create', $gastos);

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultar();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultar();

        $contenido = array('modulo' => 'vistas/modulos/gastos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Gastos);

        $request = SaveGastosRequest::validated();

        $gestos = New Gastos;
        $respuesta = $gestos->crear($request);

        //TODO: Crear requisiciones en automatico 

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Gastos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El gasto fue creado correctamente' );
            header("Location:" . Route::names('gastos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Gastos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('gastos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Gastos);

        $gastos = New Gastos;

        if ( $gastos->consultar(null , $id) ) {

            require_once "app/Models/GastoDetalles.php";
            $gastoDetalles = New \App\Models\GastoDetalles;
            $detalles = $gastoDetalles->consultarPorGasto($id);
            
            require_once "app/Models/Requisicion.php";
            $requisiciones = New \App\Models\Requisicion;
            $requisiciones->consultar(null,$gastos->requisicionId);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuarios = $usuario->consultar();
            
            $usuario->consultar(null, usuarioAutenticado()["id"]);
            $permisoAutorizar = Autorizacion::permiso($usuario, 'autorizar-gasto', 'crear');

            $ubicacion = $obra->consultar(null,$gastos->obra);

            require_once "app/Models/GastosTipos.php";
            $gastosTipos = New \App\Models\GastosTipos;
            $gastosTipo = $gastosTipos->consultar();

            require_once "app/Models/ObraDetalles.php";
            $obra_detalle = New \App\Models\ObraDetalles;
            $obra_detalles = $obra_detalle->consultarPorObra($gastos->obra);

            $contenido = array('modulo' => 'vistas/modulos/gastos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Gastos);

        $request = SaveGastosRequest::validated($id);

        $gastos = New Gastos;
        $gastos->id = $id;
        
        $respuesta = $gastos->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Gasto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Gasto fue actualizado correctamente' );
            header("Location:" . Route::names('gastos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Gasto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('gastos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New Gastos);

        try {
            // Sirve para validar el Token
            if ( !SaveGastosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                       'titulo' => 'Eliminar Gastos',
                                       'subTitulo' => 'Error',
                                       'mensaje' => $error );
            header("Location:" . Route::names('gastos.index'));
            die();

            }

            $gastos = New Gastos;
            $gastos->id = $id;
            $respuesta = $gastos->eliminar();

            if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                       'titulo' => 'Eliminar Gastos',
                                       'subTitulo' => 'OK',
                                       'mensaje' => 'El Gasto fue eliminado correctamente' );

            header("Location:" . Route::names('gastos.index'));

            } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                       'titulo' => 'Eliminar Gastos',
                                       'subTitulo' => 'Error',
                                       'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('gastos.index'));

            }
        } catch (\Exception $e) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
            'clase' => 'bg-danger',
            'titulo' => 'Eliminar Gastos',
            'subTitulo' => 'Error',
            'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen gastos con archivos no se podrá eliminar ***'
            );
            header("Location:" . Route::names('gastos.index'));
        }

        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Gastos);

        $gastos = New Gastos;

        if ( $gastos->consultar(null , $id) ) {

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null, $gastos->obra);
            
            //TODO: Cambiar por usuarios
            require_once "app/Models/Usuario.php";

            $usuarioAutorizacion = null;
            $autorizacionFirma = null;
            if ( !is_null($gastos->usuarioIdAutorizacion) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $gastos->usuarioIdAutorizacion);

                $usuarioAutorizacion = mb_strtoupper($usuario->nombreCompleto);
                $autorizacionFirma = $usuario->firma;
                unset($usuario);
            }

            $usuarioEncargado = null;
            $encargadoFirma = null;
            if ( !is_null($gastos->encargado) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $gastos->encargado);

                $usuarioEncargado = mb_strtoupper($usuario->nombreCompleto);
                $encargadoFirma = $usuario->firma;
                unset($usuario);
            }

            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $gastos->encargado);

            require_once "app/Models/GastoDetalles.php";
            $gastosDetalles = New \App\Models\GastoDetalles;

            $detallesGastos = $gastosDetalles->consultarPorGasto($gastos->id);

            if($gastos->tipoGasto == 1){
                include "reportes/gastos-deducibles.php";
            }else{
                include "reportes/gastos-no-deducibles.php";
            }

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
