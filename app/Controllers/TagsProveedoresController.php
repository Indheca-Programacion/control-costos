<?php

namespace App\Controllers;

require_once "app/Models/TagProveedor.php";
require_once "app/Policies/InformacionTecnicaTagPolicy.php";
require_once "app/Requests/SaveTagsProveedoreRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\TagProveedor;
use App\Policies\InformacionTecnicaTagPolicy;
use App\Requests\SaveTagsProveedoreRequest;
use App\Route;

class TagsProveedoresController
{
    public function index()
    {
        Autorizacion::authorize('view', new TagProveedor);

        $tagProveedor = New TagProveedor;
        $tagProveedores = $tagProveedor->consultar();


        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/tags-proveedores/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $tagProveedor = new TagProveedor;
        Autorizacion::authorize('create', $tagProveedor);

        $contenido = array('modulo' => 'vistas/modulos/tags-proveedores/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New TagProveedor);

        $request = SaveTagsProveedoreRequest::validated();

        $tagProveedor = New TagProveedor;
        $respuesta = $tagProveedor->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tag de Proveedores',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tag de proveedores fue creado correctamente' );
            header("Location:" . Route::names('tags-proveedores.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tag de Proveedores',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('tags-proveedores.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new TagProveedor);

        $tagProveedor = New TagProveedor;

        if ( $tagProveedor->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/tags-proveedores/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new TagProveedor);

        $request = SaveTagsProveedoreRequest::validated($id);

        $tagProveedor = New TagProveedor;
        $tagProveedor->id = $id;
        $respuesta = $tagProveedor->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tag de Proveedres',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tag de proveedores fue actualizado correctamente' );
            header("Location:" . Route::names('tags-proveedores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tag de Proveedores',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('tags-proveedores.edit', $id));
        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new TagProveedor);

        // Sirve para validar el Token
        if ( !SaveTagsProveedoreRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tag Proveedores',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('tags-proveedores.index'));
            die();

        }

        $tagProveedor = New TagProveedor;
        $tagProveedor->id = $id;
        $respuesta = $tagProveedor->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tag de Proveedores',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tag de Proveedores fue eliminado correctamente' );

            header("Location:" . Route::names('tags-proveedores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tag de Proveedores',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tag de proveedores no se podrá eliminar ***' );
            header("Location:" . Route::names('tags-proveedores.index'));

        }
        
        die();

    }
}
