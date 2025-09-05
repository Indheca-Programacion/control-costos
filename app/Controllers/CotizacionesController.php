<?php

namespace App\Controllers;

require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Cotizacion.php";
require_once "app/Models/Usuario.php";
require_once "app/Policies/CotizacionPolicy.php";
require_once "app/Requests/SaveCotizacionesRequest.php";

use App\Models\Cotizacion;
use App\Models\Usuario;
use App\Policies\CotizacionPolicy;
use App\Requests\SaveCotizacionesRequest;
use App\Route;


class CotizacionesController
{
    public function index()
    {

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/cotizaciones/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }

    public function create($id)
    {
        $cotizacion = new Cotizacion;
        Autorizacion::authorize('create', $cotizacion);

        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null, $id);
        $requisicion->consultarDetalles();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();

        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null , $id);
        $requisicion->consultarDetalles();

        $requerimientos = $requisicion->detalles;

        // var_dump($requerimientos); die();

        $contenido = array('modulo' => 'vistas/modulos/cotizaciones/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store($id)
    {
        
        Autorizacion::authorize('create', New Cotizacion);
        
        $request = SaveCotizacionesRequest::validated();
        
        $cotizacion = New Cotizacion;
        $request["requisicionId"] = $id;
        $respuesta = true;

        if ( count($_POST["proveedorId"]) < 1 ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Cotizacion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos un proveedor, de favor intente de nuevo' );
            header("Location:" . Route::names('requisiciones.edit', $id));

        }

        foreach ($request["proveedorId"] as $proveedorId) {
            $request["proveedorId"] = $proveedorId;
            if (!$cotizacion->crear($request)) {
            $respuesta = false;
            break;
            }
        }

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Cotizacion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue creado correctamente' );
            header("Location:" . Route::names('requisiciones.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Cotizacion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('requisiciones.edit', $id));

        }
        
        die();

    }

    public function edit($id)
    {

        $cotizacion = New Cotizacion;

        if ( $cotizacion->consultar(null , $id) ) {

            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $cotizacion->requisicionId);
            $requisicion->consultarDetalles();
            $requisicion->consultarCotizacionesProveedor(\usuarioAutenticadoProveedor()["id"]);
            $requerimientos = $requisicion->detalles;

            $contenido = array('modulo' => 'vistas/modulos/cotizaciones/editar.php');

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }

        include "vistas/modulos/plantilla_proveedores.php";


    }

    public function update($id)
    {

        $request = SaveCotizacionesRequest::validated($id);

        $cotizacion = New Cotizacion;
        $cotizacion->id = $id;
        $respuesta = $cotizacion->insertarArchivos($_POST["requisicionId"], $_FILES["cotizacionArchivos"]);

        $respuesta = $cotizacion->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Cotizacion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La cotizacion fue actualizado correctamente' );
            header("Location:" . Route::names('cotizaciones.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Cotizacion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('cotizaciones.edit', $id));

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

}

?>