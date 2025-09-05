<?php

namespace App\Controllers;

require_once "app/Models/ConfiguracionCorreoElectronico.php";
require_once "app/Policies/ConfiguracionCorreoElectronicoPolicy.php";
require_once "app/Requests/SaveConfiguracionCorreoElectronicoRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Controllers/MailController.php";

use App\Conexion;
use App\Models\ConfiguracionCorreoElectronico;
use App\Policies\ConfiguracionCorreoElectronicoPolicy;
use App\Requests\SaveConfiguracionCorreoElectronicoRequest;
use App\Route;

class ConfiguracionCorreoElectronicoController
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
        Autorizacion::authorize('update', New ConfiguracionCorreoElectronico);

        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

        if ( $configuracionCorreoElectronico->consultar(null , $id) ) {
            $configuracionCorreoElectronico->consultarPerfilesCrear();
            $configuracionCorreoElectronico->consultarEstatusModificarUsuarioCreacion();
            $configuracionCorreoElectronico->consultarEstatusModificarPerfiles();
            $configuracionCorreoElectronico->consultarDocumentos();

            require_once "app/Models/Perfil.php";
            $perfil = New \App\Models\Perfil;
            $perfiles = $perfil->consultar();

            require_once "app/Models/Estatus.php";
            $Estatus = New \App\Models\Estatus;
            $Estatus = $Estatus->consultar();

            $columnaOrden = array_column($Estatus, 'requisicionOrden');
            array_multisort($columnaOrden, SORT_ASC, $Estatus);

            $contenido = array('modulo' => 'vistas/modulos/configuracion-correo-electronico/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ConfiguracionCorreoElectronico);
        
        $request = SaveConfiguracionCorreoElectronicoRequest::validated();

        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        $configuracionCorreoElectronico->id = $id;
        $respuesta = $configuracionCorreoElectronico->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Configuración',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La configuración fue actualizada correctamente' );

            header("Location:" . Route::routes('configuracion-correo-electronico'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Configuración',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::routes('configuracion-correo-electronico'));

        }
        
        die();
    }

    public function destroy($id)
    {
    }
}
