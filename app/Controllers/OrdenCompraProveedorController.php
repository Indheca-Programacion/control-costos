<?php

namespace App\Controllers;

require_once "app/Models/OrdenCompra.php";
require_once "app/Policies/OrdenCompraPolicy.php";
require_once "app/Requests/SaveOrdenCompraRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\OrdenCompra;
use App\Policies\OrdenCompraPolicy;
use App\Requests\SaveOrdenCompraRequest;
use App\Route;

class OrdenCompraProveedorController
{
    public function index()
    {
        // Autorizacion::authorize('view', new OrdenCompra);

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/orden-compra-proveedor/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
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

        require_once "app/Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisas = $divisa->consultar();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();

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

            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $ordenCompra->requisicionId);

            require_once "app/Models/Estatus.php";
            $estatus = New \App\Models\Estatus;
            $estatuses = $estatus->consultar();

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisas = $divisa->consultar();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultar();

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

            $usuarioNombre = mb_strtoupper($usuario->nombre);
            $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $solicitoFirma = $usuario->firma;
            unset($usuario);

            $reviso = '';
            $revisoFirma = null;
            
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $ordenCompra->usuarioIdAutorizacion);

            $reviso = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $revisoFirma = $usuario->firma;
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

            include "reportes/ordencompra.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

}

?>