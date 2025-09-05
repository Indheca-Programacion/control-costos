<?php

namespace App\Controllers;

require_once "app/Models/ObraDetalles.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveObraDetallesRequest.php";

use App\Requests\SaveObraDetallesRequest;
use App\Models\ObraDetalles;
use App\Route;

class ObrasDetallesController
{
    public function index()
    {
        if ( !usuarioAutenticado() ) {
            header("Location:" . Route::routes('obras-detalles'));
            die();
        }

        // Validar Autorizacion
        // $usuario = New Usuario;
        // $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

        // if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "obras-detalles", "ver") ) {

        //     $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
        //     include "vistas/modulos/plantilla.php";
        //     die();

        // }

        // require_once "app/Models/Obra.php";
        // $obra = New \App\Models\Obra;
        // $obras = $obra->consultar();

        $contenido = array('modulo' => 'vistas/modulos/obras-detalles/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
    }

    public function store()
    {    
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New ObraDetalles);

        $obradetalle = New ObraDetalles;
        

        if ( $obradetalle->consultar(null , $id) ) {

            if ($obradetalle->insumoId !== null) {
                require_once "app/Models/InsumoTipo.php";
                $insumoTipo = New \App\Models\InsumoTipo;
                $insumoTipos = $insumoTipo->consultar();

                require_once "app/Models/Insumo.php";
                $insumos = New \App\Models\Insumo;
                $insumos->consultar(null,$obradetalle->insumoId);
            }else{
                require_once "app/Models/IndirectoTipo.php";
                $indirectoTipo = New \App\Models\IndirectoTipo;
                $indirectoTipos = $indirectoTipo->consultar();

                require_once "app/Models/Indirecto.php";
                $indirectos = New \App\Models\Indirecto;
                $indirectos->consultar(null,$obradetalle->indirectoId);
            }

            require_once "app/Models/Unidad.php";
            $unidad = New \App\Models\Unidad;
            $unidades = $unidad->consultar();

            $contenido = array('modulo' => 'vistas/modulos/obras-detalles/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ObraDetalles);

        $request = SaveObraDetallesRequest::validated($id);

        $obraDetalles = New ObraDetalles;
        $obraDetalles->id = $id;
        $request["cantidad"] = (float) str_replace(',', '', $request["cantidad"]);
        $request["presupuesto"] = (float) str_replace(',', '', $request["presupuesto"]);
        $request["presupuesto_dolares"] = (float) str_replace(',', '', $request["presupuesto_dolares"]);
        $respuesta = $obraDetalles->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Detalle de Obra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El detalle de obra fue actualizado correctamente' );
            header("Location:" . Route::names('costos-resumen.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Detalle de Obra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('obras-detalles.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New ObraDetalles);

        // Sirve para validar el Token
        if ( !SaveObraDetallesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Detalle de Obra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('costos-resumen.index'));
            die();

        }

        $obraDetalles = New ObraDetalles;
        $obraDetalles->id = $id;
        $respuesta = $obraDetalles->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Detalle de Obra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El detalle de obra fue eliminado correctamente' );

            header("Location:" . Route::names('costos-resumen.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Detalle de Obra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tipo de insumo no se podrá eliminar ***' );
            header("Location:" . Route::names('costos-resumen.index'));

        }
        
        die();
    }
}
