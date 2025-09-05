<?php

namespace App\Controllers;

require_once "app/Models/ConfiguracionCorreoElectronico.php";
require_once "app/Models/Mensaje.php";
require_once "app/Models/Perfil.php";
require_once "app/Controllers/MailController.php";
require_once "app/Models/Requisicion.php";
require_once "app/Models/OrdenCompra.php";
require_once "app/Policies/OrdenCompraPolicy.php";
require_once "app/Requests/SaveOrdenCompraRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Usuario.php";

use App\Models\OrdenCompra;
use App\Policies\OrdenCompraPolicy;
use App\Requests\SaveOrdenCompraRequest;

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;
use App\Models\Perfil;
use App\Models\Requisicion;

use App\Route;
use App\Models\Usuario;


class OrdenCompraController
{
    public function index()
    {
        Autorizacion::authorize('view', new OrdenCompra);

        $usuario = New Usuario;
        $usuario->consultar(null, \usuarioAutenticado()["id"]);

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        if ( $usuario->empresaId == 4 ) $obras = [["descripcion" => "371.Proyecto Viviendas -Edificación", "id" => 109]];
        else $obras = $obra->consultar();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();

        require_once "app/Models/CategoriaOrdenes.php";
        $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
        $categoriasOrdenCompra = $categoriaOrdenes->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/orden-compra/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create($id)
    {
        $ordenCompra = new OrdenCompra;
        Autorizacion::authorize('create', $ordenCompra);

        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null, $id);
        $requisicion->consultarDetalles();

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

        require_once "app/Models/CategoriaOrdenes.php";
        $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
        $categoriasOrdenCompra = $categoriaOrdenes->consultar();

        $contenido = array('modulo' => 'vistas/modulos/orden-compra/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New OrdenCompra);

        $request = SaveOrdenCompraRequest::validated();

        if ( !isset($request['detalles']) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Orden de compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida de favor intente de nuevo' );
            $requisicionId = $request['requisicionId'];
            header("Location:" . Route::routes('requisiciones.crear-orden-compra', $requisicionId));

            die();
        }

        $ordenCompra = New OrdenCompra;
        $respuesta = $ordenCompra->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue creado correctamente' );
            header("Location:" . Route::names('orden-compra.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('requisiciones.crear-orden-compra', $request['requisicionId']));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $ordenCompra = New OrdenCompra;

        if ( $ordenCompra->consultar(null , $id) ) {

            $ordenCompra->consultarDetalles();
            $ordenCompra->consultarObservaciones();
            $ordenCompra->consultarCotizaciones();

            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $ordenCompra->requisicionId);

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

            require_once "app/Models/Estatus.php";
            $estatus = New \App\Models\Estatus;
            $estatuses = $estatus->consultar();

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisas = $divisa->consultar();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultar();

            require_once "app/Models/CategoriaOrdenes.php";
            $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
            $categoriasOrdenCompra = $categoriaOrdenes->consultar();

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
            array_push($servicioStatus, $ordenCompra->estatus);

            $cambioAutomaticoEstatus = false;

            if ( $permitirModificarEstatus ) {

                // Agregar estatus si es el Usuario que creó la Requisición (Pantalla Servicios - Estatus)
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || $ordenCompra->usuarioIdCreacion == $usuario->id ) {
                    $servicioStatusUsuarioCreacion = $estatus->consultar();

                    foreach ($servicioStatusUsuarioCreacion as $key => $nuevoEstatus) {
                        if ( $nuevoEstatus['requisicionUsuarioCreacion'] ) {
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionOrdenCompra->checkFlujo($ordenCompra->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) array_push($servicioStatus, $nuevoEstatus);
                        }
                    }
                }
                
                // Agregar estatus de acuerdo al Perfil (Pantalla Configuración - Ordenes)
                foreach ($configuracionOrdenCompra->perfiles as $key => $value) {
                    if ( Autorizacion::perfil($usuario, CONST_ADMIN) || in_array($key, $usuario->perfiles) ) {
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
            }

            // Buscar permiso para Agregar Observaciones
            $permitirAgregarObservaciones = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requi-observaciones", "ver") ) $permitirAgregarObservaciones = true;
            
            $formularioEditable = false;
            if ( $requisicion->estatus["requisicionAbierta"] ) $formularioEditable = true;

            $permitirAutorizarAdicional = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "orden-auth-adicional", "ver") ) $permitirAutorizarAdicional = true;

            $contenido = array('modulo' => 'vistas/modulos/orden-compra/editar.php');

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }

        include "vistas/modulos/plantilla.php";


    }

    public function update($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $request = SaveOrdenCompraRequest::validated($id);

        $ordenCompra = New OrdenCompra;
        $ordenCompra->id = $id;
        $respuesta = $ordenCompra->actualizar($request);

        if ( $respuesta ) {

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

                $requisicion = new Requisicion;
                $requisicion->consultar(null , $request['requisicionId']);

                $this->sendMailUploadDocumento($requisicion, $uploadDocumentos);

            }

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue actualizado correctamente' );
            header("Location:" . Route::names('orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('orden-compra.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new OrdenCompra);

        $ordenCompra = New OrdenCompra;
        
        $ordenCompra->consultar(null , $id); // Para tener la ruta de la foto
        $respuesta = $ordenCompra->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue eliminado correctamente' );

            header("Location:" . Route::names('orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este proveedor no se podrá eliminar ***' );
            header("Location:" . Route::names('orden-compra.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New OrdenCompra);

        $ordenCompra = New OrdenCompra;

        if ( $ordenCompra->consultar(null , $id) ) {

            $ordenCompra->consultarDetalles();

            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $ordenCompra->requisicionId);

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $requisicion->empresaId);

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisa->consultar(null, $ordenCompra->monedaId);

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedor->consultar(null, $ordenCompra->proveedorId);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null, $requisicion->idObra);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $ordenCompra->usuarioIdCreacion);

            require_once "app/Models/DatosBancarios.php";
            $datosBancarios = New \App\Models\DatosBancarios;
	    $datosBancarios->consultar(null, $ordenCompra->datoBancarioId);
	    $datosBancariosProveedor = $datosBancarios->consultarDatosBancariosProveedor($proveedor->id);

            $usuarioNombre = mb_strtoupper($usuario->nombre);
            $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $solicitoFirma = $usuario->firma;
            unset($usuario);

            $reviso = '';
            $revisoFirma = null;
            $revisoSello = null;
            
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $ordenCompra->usuarioIdAutorizacion);

            $reviso = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $revisoFirma = $usuario->firma;
            $revisoSello = $usuario->sello ?? '';
            unset($usuario);

            $almacenResponsable = '';
            $almacenFirma = null;

            if ( !is_null($ordenCompra->usuarioIdAprobacion) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $ordenCompra->usuarioIdAprobacion);

                $almacenResponsable = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
                if ( !is_null($usuario->apellidoMaterno) ) $almacenResponsable .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
                $almacenFirma = $usuario->firma;
                unset($usuario);
            }

            if ( !is_null($ordenCompra->usuarioAutorizacionAdicional) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $ordenCompra->usuarioAutorizacionAdicional);

                $autorizacionAdicional = mb_strtoupper($usuario->nombreCompleto);
                $autorizacionAdicionalFirma = $usuario->firma;
                $autorizacionAdicionalPuesto = mb_strtoupper($usuario->puesto);
                
                $autorizacionAdicionalEmpresa = mb_strtoupper("ROAL");
                unset($usuario);
            }

            if ( $empresa->id == 2) include  "reportes/ordencompra.php";
            else include "reportes/ordencompra_heca.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
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

                //Esta distincion es que si se una comprobante de pago y es de la obra de viviendas se le mandara un correo a teresa perez
                if ( $requisicion->obras["id"] == 85 || $requisicion->obras["id"] == 109 ) {
                    $usuario->consultar(null, 25); // Teresa
                    array_push($arrayDestinatarios, [
                        "usuarioId" => 25,
                        "correo" => $usuario->correo
                    ]);
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

}

?>
