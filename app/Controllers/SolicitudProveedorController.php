<?php

namespace App\Controllers;

require_once "app/Models/SolicitudProveedor.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveSolicitudProveedorRequest.php";

use App\Conexion;
use App\Models\SolicitudProveedor;
use App\Requests\SaveSolicitudProveedorRequest;
use App\Route;

class SolicitudProveedorController
{
    public function index()
    {
        Autorizacion::authorize('view', new SolicitudProveedor);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/solicitud-proveedor/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new SolicitudProveedor);

        $solicitudProveedor = New SolicitudProveedor;
        $solicitudProveedor->id = $id;

        if ($solicitudProveedor->consultar(null,$id)) {

            $solicitudProveedor->consultarConstanciaFiscal();
            $solicitudProveedor->consultarOpinionCumplimiento();
            $solicitudProveedor->consultarComprobanteDomicilio();
            $solicitudProveedor->consultarDatosBancarios();

            $contenido = array('modulo' => 'vistas/modulos/solicitud-proveedor/editar.php');
            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {

        Autorizacion::authorize('delete', new SolicitudProveedor);

        // Sirve para validar el Token
        if ( !SaveSolicitudProveedorRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Solicitud',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('solicitud-proveedor.index'));
            die();

        }

        $solicitudProveedor = New SolicitudProveedor;
        $solicitudProveedor->id = $id;
        $respuesta = $solicitudProveedor->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Solicitud',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La solicitud fue eliminado correctamente' );

            header("Location:" . Route::names('solicitud-proveedor.index'));

        } else {             

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Solicitud',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo.' );
            header("Location:" . Route::names('solicitud-proveedor.index'));

        }
        
        die();

    }

}
