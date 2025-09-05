<?php

namespace App\Controllers;

require_once "app/Models/InformacionTecnicaTag.php";
require_once "app/Policies/InformacionTecnicaTagPolicy.php";
require_once "app/Requests/SaveInformacionTecnicaTagsRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\InformacionTecnicaTag;
use App\Policies\InformacionTecnicaTagPolicy;
use App\Requests\SaveInformacionTecnicaTagsRequest;
use App\Route;

class InformacionTecnicaTagsController
{
    public function index()
    {
        Autorizacion::authorize('view', new InformacionTecnicaTag);

        $informacionTecnicaTag = New InformacionTecnicaTag;
        $informacionTecnicaTags = $informacionTecnicaTag->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/informacion-tecnica-tags/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $informacionTecnicaTag = new InformacionTecnicaTag;
        Autorizacion::authorize('create', $informacionTecnicaTag);

        $contenido = array('modulo' => 'vistas/modulos/informacion-tecnica-tags/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New InformacionTecnicaTag);

        $request = SaveInformacionTecnicaTagsRequest::validated();

        $informacionTecnicaTag = New InformacionTecnicaTag;
        $respuesta = $informacionTecnicaTag->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tag de Información Técnica',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tag de información técnica fue creado correctamente' );
            header("Location:" . Route::names('informacion-tecnica-tags.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tag de Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('informacion-tecnica-tags.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new InformacionTecnicaTag);

        $informacionTecnicaTag = New InformacionTecnicaTag;

        if ( $informacionTecnicaTag->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/informacion-tecnica-tags/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new InformacionTecnicaTag);

        $request = SaveInformacionTecnicaTagsRequest::validated($id);

        $informacionTecnicaTag = New InformacionTecnicaTag;
        $informacionTecnicaTag->id = $id;
        $respuesta = $informacionTecnicaTag->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tag de Información Técnica',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tag de información técnica fue actualizado correctamente' );
            header("Location:" . Route::names('informacion-tecnica-tags.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tag de Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('informacion-tecnica-tags.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new InformacionTecnicaTag);

        // Sirve para validar el Token
        if ( !SaveInformacionTecnicaTagsRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tag de Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('informacion-tecnica-tags.index'));
            die();

        }

        $informacionTecnicaTag = New InformacionTecnicaTag;
        $informacionTecnicaTag->id = $id;
        $respuesta = $informacionTecnicaTag->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tag de Información Técnica',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tag de información técnica fue eliminado correctamente' );

            header("Location:" . Route::names('informacion-tecnica-tags.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tag de Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tag de información técnica no se podrá eliminar ***' );
            header("Location:" . Route::names('informacion-tecnica-tags.index'));

        }
        
        die();

    }
}
