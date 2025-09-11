<?php

namespace App\Controllers;

require_once "app/Models/ConfiguracionCorreoElectronico.php";
require_once "app/Models/Mensaje.php";
require_once "app/Models/Perfil.php";
require_once "app/Models/Requisicion.php";
require_once "app/Models/correoProveedor.php";
require_once "app/Models/Usuario.php";
require_once "app/Policies/RequisicionPolicy.php";
require_once "app/Requests/SaveRequisicionRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Controllers/MailController.php";

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;
use App\Models\Perfil;
use App\Models\correoProveedor;
use App\Models\Requisicion;
use App\Models\Usuario;
use App\Policies\RequisicionPolicy;
use App\Requests\SaveRequisicionRequest;
use App\Requests\Request;
use App\Route;

class RequisicionesController
{
    public function index()
    {

        Autorizacion::authorize('view', new Requisicion);

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, \usuarioAutenticado()["id"]);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Estatus.php";
        $Estatus = New \App\Models\Estatus;
        $Status = $Estatus->consultar();

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        if ( $usuario->empresaId == 4 ) $obras = [["descripcion" => "371.Proyecto Viviendas -Edificación", "id" => 109]];
        else $obras = $obra->consultarObraActivas();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();
        
        require_once "app/Models/CategoriaOrdenes.php";
        $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
        $categoriasOrdenCompra = $categoriaOrdenes->consultar();

        $contenido = array('modulo' => 'vistas/modulos/requisiciones/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        Autorizacion::authorize('update', New Requisicion);

        $contenido = array('modulo' => 'vistas/modulos/requisiciones/crear.php');

        $usuario = New Usuario;
        $usuario->consultar(null, \usuarioAutenticado()["id"]);

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        if ( $usuario->empresaId == 4 ) $obras = [["descripcion" => "371.Proyecto Viviendas -Edificación", "id" => 109]];
        else $obras = $obra->consultarObraActivas();

        require_once "app/Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisas = $divisa->consultar();

        require_once "app/Models/CategoriaOrdenes.php";
        $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
        $categoriasOrdenCompra = $categoriaOrdenes->consultar();

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
    }

    public function edit($id)
    {
        {
            Autorizacion::authorize('update', New Requisicion);
    
            $requisicion = New Requisicion;
    
            if ( $requisicion->consultar(null , $id) ) {
    
                $requisicion->consultarObservaciones();
                $requisicion->consultarDetalles();
                $requisicion->consultarComprobantes();
                $requisicion->consultarOrdenes();
                $requisicion->consultarFacturas();
                $requisicion->consultarCotizaciones();
                $requisicion->consultarVales();
                $requisicion->consultarResguardos();
                $requisicion->consultarOrdenesCompra();
                $requisicion->consultarSoporte();

                require_once "app/Models/Proveedor.php";
                $proveedor = New \App\Models\Proveedor;
                $proveedor->consultar(null, $requisicion->proveedorId);

                require_once "app/Models/Cotizacion.php";
                $cotizacion = New \App\Models\Cotizacion;
                $cotizaciones = $cotizacion->consultarPorRequisicion( $requisicion->id );

                require_once "app/Models/Empresa.php";
                $empresa = New \App\Models\Empresa;
                $empresas = $empresa->consultar();

                require_once "app/Models/Estatus.php";
                $estatus = New \App\Models\Estatus;
                $status = $estatus->consultar();

                $proveedores = $proveedor->consultarActivos();

                require_once "app/Models/Obra.php";
                $obras = New \App\Models\Obra;
                $obra = $obras->consultar(null,$requisicion->idObra);
                $listadoObras = $obras->consultarObraActivas();
                $presupuestos = $obras->consultarLotes();

                require_once "app/Models/ObraDetalles.php";
                $obraDetalles = New \App\Models\ObraDetalles;
                $descripciones = $obraDetalles->consultarPorObra($requisicion->idObra);

                require_once "app/Models/Unidad.php";
                $unidad = New \App\Models\Unidad;
                $unidades = $unidad->consultar();

                require_once "app/Models/Divisa.php";
                $divisa = New \App\Models\Divisa;
                $divisas = $divisa->consultar();

                require_once "app/Models/CategoriaOrdenes.php";
                $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
                $categoriasOrdenCompra = $categoriaOrdenes->consultar();

                require_once "app/Models/NotaInformativa.php";
                $notaInformativa = New \App\Models\NotaInformativa;
                $notaInformativa->consultar('requisicionId', $requisicion->id);
                $permitirEditar = true;
                if ( is_null($notaInformativa->id) ) {
                    $permitirEditar = false;
                }

                require_once "app/Models/Inventario.php";
                $inventario = New \App\Models\Inventario;
                $inventario->requisicionId = $requisicion->id;
                $entradasAlmacen = $inventario->consultarPorRequisicion();
    
                require_once "app/Models/ConfiguracionRequisicion.php";
                $configuracionRequisicion = New \App\Models\ConfiguracionRequisicion;
                $configuracionRequisicion->consultar(null, 1);
                $configuracionRequisicion->consultarPerfiles();
                $configuracionRequisicion->consultarFlujo();
    
                $usuario = New Usuario;
                $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
                $usuario->consultarPerfiles();
    
                // Buscar permiso para Modificar Estatus
                $permitirModificarEstatus = false;
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-status", "ver") ) $permitirModificarEstatus = true;
    
                $servicioStatus = array();
                array_push($servicioStatus, $requisicion->estatus);

                $permitirAutorizar = false;
                if( (Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "vb-adicional-rq", "ver")) && is_null($requisicion->usuarioIdAutorizacionAdd ) ) {
                    $permitirAutorizar = true;                
                }
    
                $cambioAutomaticoEstatus = false;
                if ( $permitirModificarEstatus ) {
                    // Agregar estatus si es el Usuario que creó la Requisición (Pantalla Servicios - Estatus)
                    if ( Autorizacion::perfil($usuario, CONST_ADMIN) || $requisicion->usuarioIdCreacion == $usuario->id ) {
                        $servicioStatusUsuarioCreacion = $estatus->consultar();
                        foreach ($servicioStatusUsuarioCreacion as $key => $nuevoEstatus) {
                            if ( $nuevoEstatus['requisicionUsuarioCreacion'] ) {
                                if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionRequisicion->checkFlujo($requisicion->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) array_push($servicioStatus, $nuevoEstatus);
                            }
                        }
                    }
    
                    // Agregar estatus de acuerdo al Perfil (Pantalla Configuración - Requisiciones)
                    foreach ($configuracionRequisicion->perfiles as $key => $value) {
                        if ( Autorizacion::perfil($usuario, CONST_ADMIN) || in_array($key, $usuario->perfiles) ) {
                            foreach ($value as $key2 => $value2) {
                                $nuevoEstatus = $estatus->consultar(null, $value2['EstatusId']);
    
                                if ( !$configuracionRequisicion->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "modificar") ) continue;
                                
                                if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionRequisicion->checkFlujo($requisicion->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) {
    
                                    if( mb_strtolower($nuevoEstatus["descripcion"])=='autorizado' && ($obras->almacen == 371) && usuarioAutenticado()["id"] != 88 )  continue; // Condición para que no aparezca el estatus Autorizado en el combo, si ya existe una autorización adicional

                                    if ( $configuracionRequisicion->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "automatico") ) {
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

                $subtotal = 0;
                foreach ($requisicion->detalles as $detalle) {
                    $subtotal += $detalle["costo"];
                }

                // Buscar permiso para Agregar Observaciones
                $permitirAgregarObservaciones = false;
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requi-observaciones", "ver") ) $permitirAgregarObservaciones = true;
    
                // Buscar permiso para Subir Archivos
                $permitirSubirArchivos = false;
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-subir", "ver") ) $permitirSubirArchivos = true;

                // Buscar permiso para Eliminar Archivos
                $permitirEliminarArchivos = false;
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-subir", "eliminar") ) $permitirEliminarArchivos = true;
    
                // Buscar permiso para Agregar Partidas
                $permitirAgregarPartida = false; //Cambiar a false
                if ( $requisicion->estatus['requisicionAgregarPartidas'] ) $permitirAgregarPartida = true;
    
                // Buscar permiso para Eliminar Partidas
                $permitirEliminarPartida = false;
                if ( $permitirAgregarPartida && !$configuracionRequisicion->usuarioCreacionEliminarPartidas ) $permitirEliminarPartida = true;
                if ( ( Autorizacion::perfil($usuario, CONST_ADMIN) || $requisicion->usuarioIdCreacion == $usuario->id ) && $permitirAgregarPartida && $configuracionRequisicion->usuarioCreacionEliminarPartidas ) $permitirEliminarPartida = true;
    
                $formularioEditable = false;
                if ( $requisicion->estatus["requisicionAbierta"] ) $formularioEditable = true;
    
                $contenido = array('modulo' => 'vistas/modulos/requisiciones/editar.php');
    
                include "vistas/modulos/plantilla.php";
    
            } else {
                $contenido = array('modulo' => 'vistas/modulos/errores/404.php');
    
                include "vistas/modulos/plantilla.php";
            }
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Requisicion);

        $request = SaveRequisicionRequest::validated($id);

        if ( !isset($request['estatusId']) && !isset($request['detalles']) && !isset($request['facturaArchivos']) && !isset($request['cotizacionArchivos']) && !isset($request['valeArchivos']) && !isset($request['resguardoArchivos']) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida o subir un documento, de favor intente de nuevo' );
            header("Location:" . Route::names('requisiciones.edit', $id));

        }

        $mensaje = 'La requisición fue actualizada correctamente';

        $requisicion = New Requisicion;
        $requisicion->id = $id;

        $respuesta = $requisicion->actualizar($request);

        if ($respuesta) {

            if ( !is_null($requisicion->estatusId) && $requisicion->estatusId != $request['actualEstatusId'] ) {

                $requisicion->consultar(null , $id);

                $this->sendMailCambiarEstatus($requisicion);

            }
            //Funcion para enviar email
            $uploadDocumentos = array();
            if ( isset($request['comprobanteArchivos']) ) 
                array_push($uploadDocumentos, [
                    'id' => 1,
                    'tipoDocumento' => 'Comprobante de Pago',
                    'documentos' => $request['comprobanteArchivos']['name']
                ]);
            if ( isset($request['ordenesArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 2,
                    'tipoDocumento' => 'Orden de Compra',
                    'documentos' => $request['ordenesArchivos']['name']
                ]);
            if ( isset($request['facturaArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 3,
                    'tipoDocumento' => 'Factura',
                    'documentos' => $request['facturaArchivos']['name']
                ]);
            if ( isset($request['cotizacionArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 4,
                    'tipoDocumento' => 'Cotización',
                    'documentos' => $request['cotizacionArchivos']['name']
                ]);
            if ( isset($request['valeArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 5,
                    'tipoDocumento' => 'Vale de Almacén',
                    'documentos' => $request['valeArchivos']['name']
                ]);
            if ( isset($request['resguardoArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 6,
                    'tipoDocumento' => 'Resguardo',
                    'documentos' => $request['resguardoArchivos']['name']
                ]);
                
            if ( $uploadDocumentos ) {

                if ( is_null($requisicion->folio) ) $requisicion->consultar(null , $id);

                $this->sendMailUploadDocumento($requisicion, $uploadDocumentos);

            }

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'OK',
                                                           // 'mensaje' => 'La requisición fue actualizada correctamente' );
                                                           'mensaje' => $mensaje );
            header("Location:" . Route::names('requisiciones.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('requisiciones.edit', $id));

        }
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Requisicion);

        // Sirve para validar el Token
        if ( !SaveRequisicionRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('requisiciones.index'));
            die();

        }

        $requisicion = New Requisicion;
        $requisicion->id = $id;
        $respuesta = $requisicion->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Requisicion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La requisicion fue eliminada correctamente' );

            header("Location:" . Route::names('requisiciones.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este insumo no se podrá eliminar ***' );
            header("Location:" . Route::names('requisiciones.index'));

        }
        
        die();
    }

    public function download($id, $tipo)
    {
        Autorizacion::authorize('view', New Requisicion);

        $requisicion = New Requisicion;

        $respuesta = array();

        if ( $requisicion->consultar(null , $id) ) {

            $requisicion->consultarComprobantes();
            $requisicion->consultarOrdenes();
            $requisicion->consultarFacturas();
            $requisicion->consultarCotizaciones();
            $requisicion->consultarVales();
            $requisicion->consultarResguardos();

            if ( $tipo == 1 ) $archivos = $requisicion->comprobantesPago;
            elseif ( $tipo == 2 ) $archivos = $requisicion->ordenesCompra;
            elseif ( $tipo == 3 ) $archivos = $requisicion->facturas;
            elseif ( $tipo == 4 ) $archivos = $requisicion->cotizaciones;
            elseif ( $tipo == 5 ) $archivos = $requisicion->valesAlmacen;
            elseif ( $tipo == 6 ) $archivos = $requisicion->resguardos;


            $respuesta = array( 'codigo' => ( count($archivos) > 0 ) ? 200 : 204,
                                'error' => false,
                                'cantidad' => count($archivos),
                                'archivos' => $archivos );

        } else {
            $respuesta = array( 'codigo' => 500,
                                'error' => true,
                                'errorMessage' => 'No se logró consultar la Requisición' );
        }

        echo json_encode($respuesta);
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Requisicion);

        $requisicion = New Requisicion;

        if ( $requisicion->consultar(null , $id) ) {

            $requisicion->consultarDetalles();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedor->consultar(null, $requisicion->proveedorId);

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $requisicion->empresaId);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null, $requisicion->idObra);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdCreacion);

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisa->consultar(null, $requisicion->divisa);

            $usuarioNombre = mb_strtoupper($usuario->nombre);
            $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $solicitoFirma = $usuario->firma;
            unset($usuario);

            $reviso = '';
            $revisoFirma = null;
            $revisoSello = null;
            
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdAutorizacion);

            if  ( !is_null($requisicion->usuarioIdAutorizacion) ) {
                $reviso = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
                if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
                $revisoFirma = $usuario->firma;
                $revisoSello = $usuario->sello;
                unset($usuario);
            }

            $almacenResponsable = '';
            $almacenFirma = null;

            if ( !is_null($requisicion->usuarioIdAlmacen) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $requisicion->usuarioIdAlmacen);

                $almacenResponsable = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
                if ( !is_null($usuario->apellidoMaterno) ) $almacenResponsable .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
                $almacenFirma = $usuario->firma;
                unset($usuario);
            }

            $autorizoAdicional = '';
            $firmaAutorizoAdicional = null;

            if ( !is_null($requisicion->usuarioIdAutorizacionAdd)) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $requisicion->usuarioIdAutorizacionAdd);

                $autorizoAdicional = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
                if ( !is_null($usuario->apellidoMaterno) ) $autorizoAdicional .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
                $firmaAutorizoAdicional = $usuario->firma;
                unset($usuario);
            } 

            if ( $empresa->id == 2) include "reportes/requisicion_indheca.php";
            else include "reportes/requisicion_heca.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function sendMailCreacion(Requisicion $requisicion)
    {
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $configuracionCorreoElectronico->consultarPerfilesCrear();    
            if ( $configuracionCorreoElectronico->perfilesCrear ) {

                $perfil = New Perfil;
                $perfil->consultarUsuarios($configuracionCorreoElectronico->perfilesCrear,null);

                $arrayDestinatarios = array();
                foreach ($perfil->usuarios as $key => $value) {
                    if ( in_array($value["usuarioId"], array_column($arrayDestinatarios, "usuarioId")) ) continue;

                    $destinatario = [
                        "usuarioId" => $value["usuarioId"],
                        "correo" => $value["correo"]
                    ];

                    array_push($arrayDestinatarios, $destinatario);
                }

                $mensaje = New Mensaje;

                $folio = mb_strtoupper($requisicion->folio);
                $liga = Route::names('requisiciones.edit', $requisicion->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>NUEVA REQUISICION</h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha sido creada la requisición {$folio}</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de una nueva requisición, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Nueva requisición {$folio}",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "Ha sido creada la requisición {$folio}, entre a la aplicación para ver el detalle de la misma.",
                           "liga" => $liga,
                           "destinatarios" => $arrayDestinatarios
                ];

                if ( $mensaje->crear($datos) ) {
                    $mensaje->consultar(null , $mensaje->id);
                    $mensaje->mensajeHTML = $mensajeHTML;

                    $enviar = MailController::send($mensaje);
                    if ( $enviar["error"] ) $mensaje->noEnviado([ "error" => $enviar["errorMessage"] ]);
                    else $mensaje->enviado();
                }

            }

        }        
    }

    public function sendMailCambiarEstatus(Requisicion $requisicion)
    {        
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $arrayDestinatarios = array();

            // Agregar al usuario que creó la requisición (si el estatus corresponde)
            $configuracionCorreoElectronico->consultarEstatusModificarUsuarioCreacion();
            if ( $configuracionCorreoElectronico->estatusModificarUsuarioCreacion ) {
                if ( in_array($requisicion->estatusId, $configuracionCorreoElectronico->estatusModificarUsuarioCreacion) ) {

                    $usuario = New Usuario;
                    $usuario->consultar(null, $requisicion->usuarioIdCreacion);

                    $destinatario = [
                        "usuarioId" => $usuario->id,
                        "correo" => $usuario->correo
                    ];

                    array_push($arrayDestinatarios, $destinatario);

                }
            }

            // Agregar a los usuarios de los perfiles seleccionados (si el estatus corresponde)
            $configuracionCorreoElectronico->consultarEstatusModificarPerfiles();
            if ( isset($configuracionCorreoElectronico->estatusModificarPerfiles[$requisicion->estatusId]) ) {

                $perfil = New Perfil;
                $perfil->consultarUsuarios($configuracionCorreoElectronico->estatusModificarPerfiles[$requisicion->estatusId],null);

                foreach ($perfil->usuarios as $key => $value) {
                    if ( in_array($value["usuarioId"], array_column($arrayDestinatarios, "usuarioId")) ) continue;

                    $destinatario = [
                        "usuarioId" => $value["usuarioId"],
                        "correo" => $value["correo"]
                    ];

                    array_push($arrayDestinatarios, $destinatario);
                }
            }

            if ( count($arrayDestinatarios) > 0 ) {

                $mensaje = New Mensaje;

                $folio = mb_strtoupper($requisicion->folio);
                $estatusDescripcion = mb_strtoupper($requisicion->estatus["descripcion"]);
                $liga = Route::names('requisiciones.edit', $requisicion->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>REQUISICION ACTUALIZADA</h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>El estatus de la requisición {$folio} ha sido actualizado a '{$estatusDescripcion}'</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado deL cambio de estatus de la requisición, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Estatus actualizado en requisición {$folio}",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "El estatus de la requisición {$folio} ha sido actualizado a '{$estatusDescripcion}', entre a la aplicación para ver el detalle de la misma.",
                           "liga" => $liga,
                           "destinatarios" => $arrayDestinatarios
                ];

                if ( $mensaje->crear($datos) ) {
                    $mensaje->consultar(null , $mensaje->id);
                    $mensaje->mensajeHTML = $mensajeHTML;

                    $enviar = MailController::send($mensaje);
                    if ( $enviar["error"] ) $mensaje->noEnviado([ "error" => $enviar["errorMessage"] ]);
                    else $mensaje->enviado();
                }

            }

        }
    }

    public function sendMailUploadDocumento(Requisicion $requisicion, $uploadDocumentos)
    {
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $configuracionCorreoElectronico->consultarDocumentos();

            $arrayUploadDocumentos = array();

            foreach ($uploadDocumentos as $key => $value) {

                if ( in_array($value['id'], $configuracionCorreoElectronico->documentos->uploadDocumentos) )
                    array_push($arrayUploadDocumentos, $value);

            }

            if ( count($arrayUploadDocumentos) > 0 ) {
                // var_dump($arrayUploadDocumentos);
                // var_dump( array_column($arrayUploadDocumentos, "tipoDocumento") );
                // var_dump( implode(", ", array_column($arrayUploadDocumentos, "tipoDocumento")) );

                $arrayDestinatarios = array();

                // Agregar al usuario que subió el documento (si está seleccionado)
                if ( $configuracionCorreoElectronico->documentos->usuarioUploadDocumento ) {

                    $usuario = New Usuario;
                    $usuario->consultar(null, usuarioAutenticado()['id']);

                    $destinatario = [
                        "usuarioId" => $usuario->id,
                        "correo" => $usuario->correo
                    ];

                    array_push($arrayDestinatarios, $destinatario);

                }

                // Agregar a los usuarios de los perfiles seleccionados
                if ( count($configuracionCorreoElectronico->documentos->perfilesUploadDocumento) > 0 ) {

                    $perfil = New Perfil;
                    $perfil->consultarUsuarios($configuracionCorreoElectronico->documentos->perfilesUploadDocumento,null);

                    foreach ($perfil->usuarios as $key => $value) {
                        if ( in_array($value["usuarioId"], array_column($arrayDestinatarios, "usuarioId")) ) continue;

                        $destinatario = [
                            "usuarioId" => $value["usuarioId"],
                            "correo" => $value["correo"]
                        ];

                        array_push($arrayDestinatarios, $destinatario);
                    }
                }

                if ( count($arrayDestinatarios) > 0 ) {

                    $mensaje = New Mensaje;
                    $folio = mb_strtoupper($requisicion->folio);
                    $tipoDocumentos = implode(", ", array_column($arrayUploadDocumentos, "tipoDocumento"));
                    $liga = Route::names('requisiciones.edit', $requisicion->id);
                    $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                            <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                                <center>

                                    <h3 style='font-weight: 100; color: #999'>DOCUMENTO CARGADO EN REQUISICION</h3>

                                    <hr style='border: 1px solid #ccc; width: 80%'>
                                    
                                    <br>

                                    <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                        <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Se han cargado documentos en la requisición {$folio} : '{$tipoDocumentos}'</div>
                                    </a>

                                    <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                    <hr style='border: 1px solid #ccc; width: 80%'>

                                    <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado que se han cargado documentos en la requisición, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                                </center>

                            </div>
                                
                        </div>";

                    $datos = [ "mensajeTipoId" => 3,
                               "mensajeEstatusId" => 1,
                               "asunto" => "Documento cargado en requisición {$folio}",
                               "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                               "mensaje" => "Se han cargado documentos en la requisición {$folio} : '{$tipoDocumentos}', entre a la aplicación para ver el detalle de la misma.",
                               "liga" => $liga,
                               "destinatarios" => $arrayDestinatarios
                    ];

                    if ( $mensaje->crear($datos) ) {
                        $mensaje->consultar(null , $mensaje->id);
                        $mensaje->mensajeHTML = $mensajeHTML;

                        $enviar = MailController::send($mensaje);
                        if ( $enviar["error"] ) $mensaje->noEnviado([ "error" => $enviar["errorMessage"] ]);
                        else $mensaje->enviado();
                    }

                }

            }

        }
    }

    public function upload($id)
    {

        $correoProveedor = New correoProveedor;
        
        if ( $correoProveedor->consultar(null , $id) && $correoProveedor->estatus == 0 ) {
            
            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $correoProveedor->empresaId);
            
            $contenido = array('modulo' => 'vistas/modulos/proveedores/subir-archivos.php');

            include "vistas/modulos/plantilla_proveedores.php";
            
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/proveedor.php');

            include "vistas/modulos/plantilla_proveedores.php";
        }
    }
}
