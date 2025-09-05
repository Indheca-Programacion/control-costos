<?php

namespace App\Controllers;

require_once "app/Models/ProgramacionPagos.php";
require_once "app/Requests/SaveProgramacionPagosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\ProgramacionPagos;
use App\Requests\SaveProgramacionPagosRequest;
use App\Route;

class ProgramacionPagosController 
{

    public function index(){
        Autorizacion::authorize('view', new ProgramacionPagos);

        $programacionPagos = New ProgramacionPagos;
        $bloques = $programacionPagos->consultarBloques();
        $bloquesOrdenes = $programacionPagos->consultarBloquesOrdenesCompra();

        // Inicializar bloques con sus totales en 0 y vaciar ordenesCompra
        $programacionPagos->bloques = [];
        foreach ($bloques as $bloque) {
            $programacionPagos->bloques[$bloque['id']] = [
            'id' => $bloque['id'],
            'fecha' => $bloque['fecha_programada'],
            'total' => 0,
            'tipo' => $bloque['tipoPago'],
            'prioridad' => $bloque['prioridad'],
            'pagado' => $bloque['pagado'],
            'ordenesCompra' => []
            ];
        }

        require_once "app/Models/OrdenCompra.php";
        $ordenesCompra = New \App\Models\OrdenCompra;
        $ordenes = $ordenesCompra->consultar();

        // Relacionar ordenes de compra a su bloque correspondiente y sumar el total
        foreach ($bloquesOrdenes as $bloqueOrden) {
            $bloqueId = $bloqueOrden['programacion_pago'];
            $ordenId = $bloqueOrden['ordenCompraId'];
            // Buscar la orden de compra por id
            foreach ($ordenes as $key => $orden) {
                if ($orden['id'] == $ordenId && isset($programacionPagos->bloques[$bloqueId])) {

                    $ordenes[$key]["Bloque"]= $programacionPagos->bloques[$bloqueId]['tipo'].' '. $programacionPagos->bloques[$bloqueId]['id'];

                    $programacionPagos->bloques[$bloqueId]['ordenesCompra'][] = [
                        'id' => $orden['id'],
                        'folio' => $orden['folio'],
                        'total' => $orden['total'],
                        'obra' => $orden['obra.nombreCorto'],
                    ];
                    $programacionPagos->bloques[$bloqueId]['total'] += $orden['total'];
                    break;
                }
            }
        }
        // Reindexar los bloques para que sea un array secuencial si es necesario
        $programacionPagos->bloques = array_values($programacionPagos->bloques);

        if (!empty($ordenes)) {
            usort($ordenes, function ($a, $b) {
                $bloqueA = isset($a['Bloque']) ? $a['Bloque'] : '';
                $bloqueB = isset($b['Bloque']) ? $b['Bloque'] : '';
                return strcmp($bloqueA, $bloqueB);
            });
        }


        $contenido = array('modulo' => 'vistas/modulos/programacion-pagos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create(){
        Autorizacion::authorize('create', New ProgramacionPagos);

        $programacionPagos = New ProgramacionPagos;
        $programacionPagos->ordenesCompra = [];

        $contenido = array('modulo' => 'vistas/modulos/programacion-pagos/create.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store(){
        Autorizacion::authorize('create', New ProgramacionPagos);

        $request = SaveProgramacionPagosRequest::validated();

        $ProgramacionPagos = New ProgramacionPagos;
        $respuesta = $ProgramacionPagos->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear ProgramacionPagos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La ProgramacionPagos fue creada correctamente' );
            header("Location:" . Route::names('programacion-pagos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear ProgramacionPagos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('programacion-pagos.index'));

        }
        
        die();
    }

    public function update($id)
    {        
        Autorizacion::authorize('update', new ProgramacionPagos);
        
        $request = SaveProgramacionPagosRequest::validated($id);

        $ProgramacionPagos = New ProgramacionPagos;
        $ProgramacionPagos->id = $id;
        $respuesta = $ProgramacionPagos->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar ProgramacionPagos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La ProgramacionPagos fue actualizada correctamente' );
            header("Location:" . Route::names('programacion-pagos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar ProgramacionPagos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('programacion-pagos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', new ProgramacionPagos);

        // Sirve para validar el Token
        if ( !SaveProgramacionPagosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar ProgramacionPagos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('programacion-pagos.index'));
            die();

        }
        
        $ProgramacionPagos = New ProgramacionPagos;
        $ProgramacionPagos->id = $id;
        $respuesta = $ProgramacionPagos->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar ProgramacionPagos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La ProgramacionPagos fue eliminada correctamente' );

            header("Location:" . Route::names('programacion-pagos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar ProgramacionPagos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este permiso no se podrá eliminar ***' );
            header("Location:" . Route::names('programacion-pagos.index'));

        }
        
        die();
    }
}