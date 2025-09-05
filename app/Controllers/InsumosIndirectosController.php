<?php

namespace App\Controllers;

require_once "app/Models/Insumo.php";
require_once "app/Models/Indirecto.php";
require_once "app/Models/Usuario.php";
require_once "app/Requests/SaveInsumosRequest.php";
require_once "app/Requests/SaveIndirectosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Insumo;
use App\Models\Indirecto;
use App\Models\Usuario;
use App\Requests\SaveInsumosRequest;
use App\Requests\SaveIndirectosRequest;
use App\Route;

class InsumosIndirectosController
{
    public function index()
    {
        // Autorizacion::authorize('view', New Indirecto);

        // Validar Autorizacion
        if ( is_null(usuarioAutenticado()) ) {
            // $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            // include "vistas/modulos/plantilla.php";
            die();
        }

        $usuario = new Usuario;
        $usuario->id = usuarioAutenticado()["id"];
        $usuario->usuario = usuarioAutenticado()["usuario"];

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "ver") ) {

            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();

        }

        // $indirecto = New Indirecto;
        // $indirectos = $indirecto->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/insumos-indirectos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        // $indirecto = New Indirecto;
        // Autorizacion::authorize('create', $indirecto);

        // Validar Autorizacion
        if ( is_null(usuarioAutenticado()) ) {
            // $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            // include "vistas/modulos/plantilla.php";
            die();
        }

        $usuario = new Usuario;
        $usuario->id = usuarioAutenticado()["id"];
        $usuario->usuario = usuarioAutenticado()["usuario"];

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "crear") ) {

            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();

        }

        require_once "app/Models/InsumoTipo.php";
        $insumoTipo = New \App\Models\InsumoTipo;
        $insumoTipos = $insumoTipo->consultar();

        require_once "app/Models/IndirectoTipo.php";
        $indirectoTipo = New \App\Models\IndirectoTipo;
        $indirectoTipos = $indirectoTipo->consultar();

        require_once "app/Models/Unidad.php";
        $unidad = New \App\Models\Unidad;
        $unidades = $unidad->consultar();

        $contenido = array('modulo' => 'vistas/modulos/insumos-indirectos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        // Autorizacion::authorize('create', New Indirecto);

        // Validar Autorizacion
        if ( is_null(usuarioAutenticado()) ) {
            // $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            // include "vistas/modulos/plantilla.php";
            die();
        }

        $usuario = new Usuario;
        $usuario->id = usuarioAutenticado()["id"];
        $usuario->usuario = usuarioAutenticado()["usuario"];

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "crear") ) {

            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();

        }

        if ( $_REQUEST['tipo'] == 'insumo' ) $request = SaveInsumosRequest::validated();
        else $request = SaveIndirectosRequest::validated();

        if ( $_REQUEST['tipo'] == 'insumo' ) {
            $titulo = 'Crear Insumo';
            $mensaje = 'El insumo fue creado correctamente';

            $insumo = New Insumo;
            $respuesta = $insumo->crear($request);
        } else {
            $titulo = 'Crear Indirecto';
            $mensaje = 'El indirecto fue creado correctamente';

            $indirecto = New Indirecto;
            $respuesta = $indirecto->crear($request);
        }

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => $titulo,
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => $mensaje );
            header("Location:" . Route::names('insumos-indirectos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => $titulo,
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('insumos-indirectos.create'));

        }
        
        die();
    }

    public function edit($id)
    {
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {
    }
}
