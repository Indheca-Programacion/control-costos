<?php

namespace App\Controllers;

require_once "app/Models/InformacionTecnica.php";
require_once "app/Policies/InformacionTecnicaPolicy.php";
require_once "app/Requests/SaveInformacionTecnicaRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\InformacionTecnica;
use App\Policies\InformacionTecnicaPolicy;
use App\Requests\SaveInformacionTecnicaRequest;
use App\Route;

class InformacionTecnicaController
{
    public function index()
    {
        Autorizacion::authorize('view', new InformacionTecnica);

        $informacionTecnica = New InformacionTecnica;
        $informaciones = $informacionTecnica->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/informacion-tecnica/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $informacionTecnica = new InformacionTecnica;
        Autorizacion::authorize('create', $informacionTecnica);

        require_once "app/Models/InformacionTecnicaTag.php";
        $informacionTecnicaTag = New \App\Models\InformacionTecnicaTag;
        $informacionTecnicaTags = $informacionTecnicaTag->consultar();

        require_once "app/Models/Perfil.php";
        $perfil = New \App\Models\Perfil;
        $perfilesVisualizar = $perfil->consultar();

        $contenido = array('modulo' => 'vistas/modulos/informacion-tecnica/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New InformacionTecnica);

        $request = SaveInformacionTecnicaRequest::validated();

        $informacionTecnica = New InformacionTecnica;
        $respuesta = $informacionTecnica->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Información Técnica',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La información técnica fue creada correctamente' );
            header("Location:" . Route::names('informacion-tecnica.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('informacion-tecnica.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new InformacionTecnica);

        $informacionTecnica = New InformacionTecnica;

        if ( $informacionTecnica->consultar(null , $id) ) {
            require_once "app/Models/InformacionTecnicaTag.php";
            $informacionTecnicaTag = New \App\Models\InformacionTecnicaTag;
            $informacionTecnicaTags = $informacionTecnicaTag->consultar();

            require_once "app/Models/Perfil.php";
            $perfil = New \App\Models\Perfil;
            $perfilesVisualizar = $perfil->consultar();
        
            $contenido = array('modulo' => 'vistas/modulos/informacion-tecnica/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new InformacionTecnica);

        $request = SaveInformacionTecnicaRequest::validated($id);

        $informacionTecnica = New InformacionTecnica;
        $informacionTecnica->id = $id;
        $informacionTecnica->consultar(null , $id); // Consultar para tener el archivo a eliminar físicamente en caso de que venga un nuevo archivo
        $respuesta = $informacionTecnica->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Información Técnica',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La información técnica fue actualizada correctamente' );
            header("Location:" . Route::names('informacion-tecnica.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('informacion-tecnica.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new InformacionTecnica);

        // Sirve para validar el Token
        if ( !SaveInformacionTecnicaRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('informacion-tecnica.index'));
            die();

        }

        $informacionTecnica = New InformacionTecnica;
        $informacionTecnica->id = $id;
        $informacionTecnica->consultar(null , $id); // Consultar para tener el archivo a eliminar físicamente
        $respuesta = $informacionTecnica->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Información Técnica',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La información técnica fue eliminada correctamente' );

            header("Location:" . Route::names('informacion-tecnica.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Información Técnica',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta información técnica no se podrá eliminar ***' );
            header("Location:" . Route::names('informacion-tecnica.index'));

        }
        
        die();

    }

    public function download($id)
    {      
        Autorizacion::authorize('view', new InformacionTecnica);

        $informacionTecnica = New InformacionTecnica;

        if ( $informacionTecnica->consultar(null , $id) ) {

            if ( file_exists($informacionTecnica->ruta) ) {
				header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                // header('Content-Disposition: attachment; filename="'.basename($informacionTecnica->ruta).'"');
                header('Content-Disposition: attachment; filename="'.$informacionTecnica->archivo.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($informacionTecnica->ruta));
                readfile($informacionTecnica->ruta);
				exit;
            }

            $contenido = array('modulo' => 'vistas/modulos/informacion-tecnica/index.php');

        	include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
