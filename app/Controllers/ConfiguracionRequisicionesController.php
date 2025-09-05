<?php

namespace App\Controllers;

require_once "app/Models/ConfiguracionRequisicion.php";
require_once "app/Policies/ConfiguracionRequisicionPolicy.php";
require_once "app/Requests/SaveConfiguracionRequisicionesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Conexion;
use App\Models\ConfiguracionRequisicion;
use App\Policies\ConfiguracionRequisicionPolicy;
use App\Requests\SaveConfiguracionRequisicionesRequest;
use App\Route;

class ConfiguracionRequisicionesController
{
    public function index()
    {
    }

    public function create()
    {
    }

    public function store()
    {
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New ConfiguracionRequisicion);

        $configuracionRequisicion = New ConfiguracionRequisicion;

        if ( $configuracionRequisicion->consultar(null , $id) ) {
            $configuracionRequisicion->consultarPerfiles();
            $configuracionRequisicion->consultarFlujo();

            require_once "app/Models/Estatus.php";
            $Estatus = New \App\Models\Estatus;
            $Status = $Estatus->consultar();

            $columnaOrden = array_column($Status, 'requisicionOrden');
            array_multisort($columnaOrden, SORT_ASC, $Status);

            $query = "SELECT    PE.nombre AS 'perfiles.nombre', P.nombre AS 'permisos.nombre',
                                PP.perfilId, PP.permisoId, PP.ver
                    FROM        perfil_permisos PP
                    INNER JOIN  permisos P ON PP.permisoId = P.id
                    INNER JOIN  perfiles PE ON PP.perfilId = PE.id
                    WHERE       P.nombre = 'requisiciones-status'
                    AND         PP.ver = 1";

            $perfilesPermisoModificarEstatus = Conexion::queryAll(CONST_BD_SECURITY, $query, $error);

            $contenido = array('modulo' => 'vistas/modulos/configuracion-requisiciones/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ConfiguracionRequisicion);
        
        $request = SaveConfiguracionRequisicionesRequest::validated();

        $configuracionRequisicion = New ConfiguracionRequisicion;
        $configuracionRequisicion->id = $id;
        
        $respuesta = $configuracionRequisicion->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Configuración',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La configuración fue actualizada correctamente' );

            header("Location:" . Route::routes('configuracion-requisiciones'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Configuración',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::routes('configuracion-requisiciones'));

        }
        
        die();
    }

    public function destroy($id)
    {
    }
}
