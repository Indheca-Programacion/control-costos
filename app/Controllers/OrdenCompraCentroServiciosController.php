<?php

namespace App\Controllers;

require_once "control-mantenimiento/app/Models/Perfil.php";
require_once "control-mantenimiento/app/Controllers/MailController.php";
require_once "app/Models/OrdenCompraCS.php";
require_once "control-mantenimiento/app/Requests/SaveOrdenCompraRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\OrdenCompra;
use App\Requests\SaveOrdenCompraRequest;

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;
use App\Models\Perfil;


use App\Route;

class OrdenCompraCentroServiciosController
{
    public function index()
    {
        Autorizacion::authorize('view', new OrdenCompra);

        // require_once "app/Models/Usuario.php";
        // $usuario = New \App\Models\Usuario;
        // $usuario->consultar(null, \usuarioAutenticado()["id"]);

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar();
        
        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/orden-compra-cs/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $ordenCompra = New OrdenCompra;

        if ( $ordenCompra->consultar(null , $id) ) {

            $ordenCompra->consultarDetalles();
            $ordenCompra->consultarObservaciones();

            require_once "app/Models/RequisicionCS.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $ordenCompra->requisicionId);
            
            require_once "app/Models/EstatusOrdenCompra.php";
            $estatus = New \App\Models\EstatusOrdenCompra;
            $estatuses = $estatus->consultar();

            $requisicion->consultarComprobantes();
            $requisicion->consultarOrdenes();
            $requisicion->consultarFacturas();
            $requisicion->consultarCotizaciones();
            $requisicion->consultarVales();
            $requisicion->consultarOrdenesCompra();

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisas = $divisa->consultar();

            require_once "app/Models/ProveedorCS.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultar();

            require_once "app/Models/UsuarioCS.php";
            $usuario = New \App\Models\UsuarioCS;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            $usuario->consultarPerfiles();

            require_once "app/Models/ConfiguracionOrdenCompraCS.php";
            $configuracionOrdenCompra = New \App\Models\ConfiguracionOrdenCompra;
            $configuracionOrdenCompra->consultar(null, 1);
            $configuracionOrdenCompra->consultarPerfiles();
            $configuracionOrdenCompra->consultarFlujo();

            // OBTENER ESTATUS

            $servicioStatus = array();

            array_push($servicioStatus, $ordenCompra->estatus);

            $cambioAutomaticoEstatus = false;

            // Agregar estatus si es el Usuario que creó la Requisición (Pantalla Servicios - Estatus)
            
            // Agregar estatus de acuerdo al Perfil (Pantalla Configuración - Ordenes)
            $permitirModificarEstatus = true;

            
            foreach ($configuracionOrdenCompra->perfiles as $key => $value) {
                
                if ( in_array($key, $usuario->perfiles) ) {
                    foreach ($value as $key2 => $value2) {
                        
                        $nuevoEstatus = $estatus->consultar(null, $value2['EstatusId']);

                        if ( !$configuracionOrdenCompra->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "modificar") ) continue;
                        
                        
                        if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionOrdenCompra->checkFlujo($ordenCompra->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) {

                            
                            if ( $configuracionOrdenCompra->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "automatico") ) {
                                $servicioStatus = array();
                                array_push($servicioStatus, $nuevoEstatus);
                                $cambioAutomaticoEstatus = true;
                                break;
                            }

                            array_push($servicioStatus, $nuevoEstatus);

                        } 
                    }
                }
            }

            // Buscar permiso para Agregar Observaciones
            $permitirAgregarObservaciones = true;
            
            $formularioEditable = false;
            
            if ( $requisicion->estatus["requisicionAbierta"] ) $formularioEditable = true;

            $contenido = array('modulo' => 'vistas/modulos/orden-compra-cs/editar.php');

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }

        include "vistas/modulos/plantilla.php";


    }

    public function update($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $request = [
            "estatusId" => $_POST["estatusId"],
            "id" => $id,
            "observacion" => $_POST["observacion"]
        ];

        require_once "app/Models/UsuarioCS.php";
        $usuarioCS = New \App\Models\UsuarioCS;
        $usuarioCS->consultar("usuario", usuarioAutenticado()["usuario"]);

        $ordenCompra = New OrdenCompra;
        $ordenCompra->id = $id;
        $respuesta = $ordenCompra->actualizar($request,$usuarioCS->id);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue actualizado correctamente' );
            header("Location:" . Route::names('orden-compra-centro-servicios.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('orden-compra-centro-servicios.edit', $id));

        }
        
        die();
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New OrdenCompra);

        $ordenCompra = New OrdenCompra;
        $ordenCompra->ordenCompraId = $id;

        $ordenDeCompraDatos = $ordenCompra->consultarOrdenDeCompra();

        if ( $ordenDeCompraDatos ) {

            foreach ($ordenDeCompraDatos as $key => $value) {

                require_once "app/Models/RequisicionCS.php";
                $requisicion = New \App\Models\Requisicion;
                $requisicion->consultar(null, $value["requisicionId"]);

                require_once "app/Models/Empresa.php";
                $empresa = New \App\Models\Empresa;
                $empresa->consultar(null, $requisicion->servicio["empresaId"]);

                require_once "app/Models/Divisa.php";
                $divisa = New \App\Models\Divisa;
                $divisa->consultar(null, $value["monedaId"]);

                require_once "app/Models/ProveedorCS.php";
                $proveedor = New \App\Models\Proveedor;
                $proveedor->consultar(null, $value["proveedorId"]);

                require_once "app/Models/Obra.php";
                $obra = New \App\Models\Obra;
                $obra->consultar(null, $requisicion->servicio["obraId"]);

                require_once "app/Models/UsuarioCS.php";
                $usuario = New \App\Models\Usuario;
                $usuarioElabora = New \App\Models\Usuario;
                $usuarioAprueba = New \App\Models\Usuario;
                $usuarioAutoriza = New \App\Models\Usuario;

                require_once "app/Models/DatosBancarios.php";
                $datosBancarios = New \App\Models\DatosBancarios;
                $datosBancarios->consultar(null, $value["datoBancarioId"]);

                /********************** USUARIO ELABORA *****************************/
                $usuarioElabora->consultar(null, $value["usuarioIdCreacion"]);

                // NOMBRE COMPLETO USUARIO ELABORA
                $nombreCompletoUsuarioElabora = mb_strtoupper($usuarioElabora->nombre . ' ' . $usuarioElabora->apellidoPaterno);
                if ( !is_null($usuarioElabora->apellidoMaterno) ) $nombreCompletoUsuarioElabora .= ' ' . mb_strtoupper($usuarioElabora->apellidoMaterno);

                // FIRMA USUARIO ELABORA
                $firmaUsuarioElabora = $usuarioElabora->firma;
                /*****************************************************/
                
                /********************** USUARIO APRUEBA *****************************/
                $usuarioAprueba->consultar(null,$value["usuarioIdAprobacion"]);

                // NOMBRE COMPLETO USUARIO APRUEBA
                $nombreCompletoUsuarioAprueba = mb_strtoupper($usuarioAprueba->nombre . ' ' . $usuarioAprueba->apellidoPaterno);
                if ( !is_null($usuarioAprueba->apellidoMaterno) ) $nombreCompletoUsuarioAprueba .= ' ' . mb_strtoupper($usuarioAprueba->apellidoMaterno);

                // FIRMA USUARIO APRUEBA
                $firmaUsuarioAprueba = $usuarioAprueba->firma;
                /*****************************************************/

                /********************** USUARIO AUTORIZA *****************************/
                $usuarioAutoriza->consultar(null, $value["usuarioIdAutorizacion"]);

                // NOMBRE COMPLETO USUARIO ELABORA
                $nombreCompletoUsuarioAutoriza = mb_strtoupper($usuarioAutoriza->nombre . ' ' . $usuarioAutoriza->apellidoPaterno);
                if ( !is_null($usuarioAutoriza->apellidoMaterno) ) $nombreCompletoUsuarioAutoriza .= ' ' . mb_strtoupper($usuarioAutoriza->apellidoMaterno);

                // FIRMA USUARIO ELABORA
                $firmaUsuarioAutoriza = $usuarioAutoriza->firma;
                /*****************************************************/

                include "reportes/ordencompra_cs.php";

            }

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

}

?>