<?php

namespace App\Controllers;

require_once "app/Models/CategoriaOrdenes.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveCategoriaOrdenesRequest.php";
require_once "app/Models/Obra.php";


use App\Conexion;
use App\Models\CategoriaOrdenes;
use App\Requests\SaveCategoriaOrdenesRequest;

use App\Route;

class CategoriaOrdenesController
{
    public function index()
    {
        Autorizacion::authorize('view', new CategoriaOrdenes);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/categoria-ordenes/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $categoriaOrdenes = New CategoriaOrdenes;
        Autorizacion::authorize('create', $categoriaOrdenes);

        $contenido = array('modulo' => 'vistas/modulos/categoria-ordenes/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New CategoriaOrdenes);

        $request = SaveCategoriaOrdenesRequest::validated();

        $categoriaOrdenes = New CategoriaOrdenes;

        $respuesta = $categoriaOrdenes->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Categoria de Ordenes',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'Las categorias de ordenes fueron creadas correctamente' );
            header("Location:" . Route::names('categoria-ordenes.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Categoria de Ordenes',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('categoria-ordenes.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new CategoriaOrdenes);

        $categoriaOrdenes = New CategoriaOrdenes;

        if ($categoriaOrdenes->consultar(null,$id)) {

            $contenido = array('modulo' => 'vistas/modulos/categoria-ordenes/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', new CategoriaOrdenes);

        $request = SaveCategoriaOrdenesRequest::validated();

        $categoriaOrdenes = New CategoriaOrdenes;

        $datos = array(
            'id' => $id,
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion']
        );

        $respuesta = $categoriaOrdenes->actualizar($datos);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Categoria de Ordenes',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'Las categorias de ordenes fueron actualizadas correctamente' );
            header("Location:" . Route::names('categoria-ordenes.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Categoria de Ordenes',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('categoria-ordenes.edit', ['id' => $id]));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', new CategoriaOrdenes);

        $categoriaOrdenes = New CategoriaOrdenes;
        $categoriaOrdenes->id = $id;

        $respuesta = $categoriaOrdenes->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Categoria de Ordenes',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'Las categorias de ordenes fueron eliminadas correctamente' );
            header("Location:" . Route::names('categoria-ordenes.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Categoria de Ordenes',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('categoria-ordenes.index'));

        }
        
        die();
    }
}
