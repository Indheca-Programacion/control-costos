<?php

namespace App\Controllers;

require_once "app/Models/OrdenCompraGlobales.php";
require_once "app/Models/Requisicion.php";

require_once "app/Policies/OrdenCompraGlobalesPolicy.php";
require_once "app/Requests/SaveOrdenCompraGlobalesRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Usuario.php";


use App\Models\OrdenCompraGlobales;
use App\Models\Requisicion;

use App\Policies\OrdenCompraGlobalesPolicy;
use App\Requests\SaveOrdenCompraGlobalesRequest;
use App\Models\Usuario;

use App\Route;

class OrdenCompraGlobalesController
{
    public function index()
    {
        Autorizacion::authorize('view', New OrdenCompraGlobales);

        $requisicion = New Requisicion;
        $requisiciones = $requisicion->consultar();

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar();
        
        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/OrdenCompraGlobales/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        Autorizacion::authorize('create', new OrdenCompraGlobales);

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar();

        require_once "app/Models/ConfiguracionOrdenCompra.php";
        $configuracionOrdenCompra = New \App\Models\ConfiguracionOrdenCompra;
        $configuracionOrdenCompra->consultar(null, 1);
        
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
    
        // Buscar permiso para Modificar Estatus
        $permitirModificarEstatus = false;
        if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "orden-compra-status", "ver") ) $permitirModificarEstatus = true;

        $servicioStatus = array();
        array_push($servicioStatus, $estatus->consultar(null, $configuracionOrdenCompra->inicialEstatusId));

        require_once "app/Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisas = $divisa->consultar();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();

        require_once "app/Models/ConfiguracionOrdenCompra.php";
        $configuracionOrdenCompra = New \App\Models\ConfiguracionOrdenCompra;
        $configuracionOrdenCompra->consultar(null, 1);

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar(null,$configuracionOrdenCompra->inicialEstatusId);



        $contenido = array('modulo' => 'vistas/modulos/OrdenCompraGlobales/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New OrdenCompraGlobales);

        $request = SaveOrdenCompraGlobalesRequest::validated();

        $detallesArray = json_decode($request["detalles"], true);


            if (empty($request['detalles'])) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase'     => 'bg-danger',
                'titulo'    => 'Crear Orden de compra',
                'subTitulo' => 'Error',  
                'mensaje'   => 'Debe capturar al menos una partida. Por favor intente de nuevo.'
            );

            header("Location:" . Route::names('orden-compra-globales.create'));
            die;
        }

        $ordenCompraGlobal = New OrdenCompraGlobales;
        $respuesta = $ordenCompraGlobal->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue creado correctamente' );
            header("Location:" . Route::names('orden-compra-globales.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('orden-compra-globales.create'));


        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new OrdenCompraGlobales);

        $ordenCompraGlobal = New OrdenCompraGlobales;

        if ( $ordenCompraGlobal->consultar(null , $id) ) {

            $ordenCompraGlobal->consultarDetalles();
            $ordenCompraGlobal->consultarObservaciones();
            $ordenCompraGlobal->consultarRequisicionesPorOrdenCompra();
            
            $ids = implode(',', array_column($ordenCompraGlobal->requisiciones, 'idRequisicion'));
            
            $ordenCompraGlobal->requisicionId = $ids;
            $ordenCompraGlobal->consultarComprobantes();

            require_once "app/Models/Estatus.php";
            $estatus = New \App\Models\Estatus;
            $estatuses = $estatus->consultar();

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisas = $divisa->consultar();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultar();

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            $usuario->consultarPerfiles();

            require_once "app/Models/ConfiguracionOrdenCompra.php";
            $configuracionOrdenCompra = New \App\Models\ConfiguracionOrdenCompra;
            $configuracionOrdenCompra->consultar(null, 1);
            $configuracionOrdenCompra->consultarPerfiles();
            $configuracionOrdenCompra->consultarFlujo();

            // OBTENER ESTATUS
             // Buscar permiso para Modificar Estatus
            $permitirModificarEstatus = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-status", "ver") ) $permitirModificarEstatus = true;

            $servicioStatus = array();
            array_push($servicioStatus, $ordenCompraGlobal->estatus);

            $cambioAutomaticoEstatus = false;

            if ( $permitirModificarEstatus ) {

                // Agregar estatus si es el Usuario que creó la Requisición (Pantalla Servicios - Estatus)
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || $ordenCompraGlobal->usuarioIdCreacion == $usuario->id ) {
                    $servicioStatusUsuarioCreacion = $estatus->consultar();

                    foreach ($servicioStatusUsuarioCreacion as $key => $nuevoEstatus) {
                        if ( $nuevoEstatus['requisicionUsuarioCreacion'] ) {
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionOrdenCompra->checkFlujo($ordenCompraGlobal->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) array_push($servicioStatus, $nuevoEstatus);
                        }
                    }
                }
                
                // Agregar estatus de acuerdo al Perfil (Pantalla Configuración - Ordenes)
                foreach ($configuracionOrdenCompra->perfiles as $key => $value) {
                    if ( Autorizacion::perfil($usuario, CONST_ADMIN) || in_array($key, $usuario->perfiles) ) {
                        foreach ($value as $key2 => $value2) {

                            $nuevoEstatus = $estatus->consultar(null, $value2['EstatusId']);

                            if ( !$configuracionOrdenCompra->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "modificar") ) continue;
                            
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionOrdenCompra->checkFlujo($ordenCompraGlobal->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) {

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
            }

        // Buscar permiso para Agregar Observaciones
         $permitirAgregarObservaciones = false;
         if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requi-observaciones", "ver") ) $permitirAgregarObservaciones = true;
        
         $formularioEditable = true;
        //  if ( $requisicion->estatus["requisicionAbierta"] ) $formularioEditable = true;


            $contenido = array('modulo' => 'vistas/modulos/OrdenCompraGlobales/editar.php');

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }

        include "vistas/modulos/plantilla.php";


    }

    public function update($id)
    {
        Autorizacion::authorize('update', new OrdenCompraGlobales);

        $request = SaveOrdenCompraGlobalesRequest::validated($id);

        $ordenCompra = New OrdenCompraGlobales;
        $ordenCompra->id = $id;
        $respuesta = $ordenCompra->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue actualizado correctamente' );
            header("Location:" . Route::names('orden-compra-globales.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('orden-compra-globales.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new OrdenCompraGlobales);

        $ordenCompra = New OrdenCompraGlobales;

        $ordenCompra->consultar(null , $id); // Para tener la ruta de la foto
        $respuesta = $ordenCompra->eliminar();


        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue eliminado correctamente' );

            header("Location:" . Route::names('orden-compra-globales.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este proveedor no se podrá eliminar ***' );
            header("Location:" . Route::names('orden-compra-globales.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New OrdenCompraGlobales);

        $ordenCompraGlobal = New OrdenCompraGlobales;

        if ( $ordenCompraGlobal->consultar(null , $id) ) {

            $ordenCompraGlobal->consultarDetalles();
            $ordenCompraGlobal->consultarRequisicionesPorOrdenCompra();
            

            // require_once "app/Models/Requisicion.php";
            // $requisicion = New \App\Models\Requisicion;
            // // $requisicion->consultar(null, $ordenCompraGlobal->requisicionId);
            // $requisicion->consultar(null, 1195);

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $ordenCompraGlobal->empresaId);

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisa->consultar(null, $ordenCompraGlobal->monedaId);

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedor->consultar(null, $ordenCompraGlobal->proveedorId);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $ordenCompraGlobal->usuarioIdCreacion);

            require_once "app/Models/DatosBancarios.php";
            $datosBancarios = New \App\Models\DatosBancarios;
            $datosBancarios->consultar(null, $ordenCompraGlobal->datoBancarioId);

            $usuarioNombre = mb_strtoupper($usuario->nombre);
            $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $solicitoFirma = $usuario->firma;
            unset($usuario);

            $reviso = '';
            $revisoFirma = null;
            $revisoSello = null;
            
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $ordenCompraGlobal->usuarioIdAutorizacion);

            $reviso = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $revisoFirma = $usuario->firma;
            $revisoSello = 12;

    
            unset($usuario);

            $almacenResponsable = '';
            $almacenFirma = null;

            if ( !is_null($ordenCompraGlobal->usuarioIdAprobacion) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $ordenCompraGlobal->usuarioIdAprobacion);

                $almacenResponsable = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
                if ( !is_null($usuario->apellidoMaterno) ) $almacenResponsable .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
                $almacenFirma = $usuario->firma;
                unset($usuario);
            }

            include "reportes/ordencompraglobal.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
