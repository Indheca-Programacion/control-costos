<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/ProgramacionPagos.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveProgramacionPagosRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\ProgramacionPagos;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveProgramacionPagosRequest;



class ProgramacionPagosAjax
{

    /*=============================================
    ASIGNAR ORDENES DE COMPRA A PROGRAMACION DE PAGOS
    =============================================*/
    public function asignarOrdenes()
    {
        try{
            
            Autorizacion::authorize('create', new ProgramacionPagos);

            $programacionPago = new ProgramacionPagos;
            $response = $programacionPago->asignarOrdenesCompra($_POST['programacion_pago'], $_POST['ordenes']);

            $respuesta = [
				'error' => false,
				'respuesta' => $response,
				'respuestaMessage' => "Se ha agregado con exito."
			];
		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }
		echo json_encode($respuesta);
    }

    /*=============================================
    ACTUALIZAR PRIORIDADES DE LOS BLOQUES
    =============================================*/
    public function actualizarPrioridades(){
        try {
            Autorizacion::authorize('update', new ProgramacionPagos);
            $prioridades = $_POST['prioridades'] ?? [];
            if (empty($prioridades)) {
                throw new \Exception("No se han proporcionado prioridades para actualizar.");
            }
            $programacionPagos = new ProgramacionPagos;
            $response = $programacionPagos->actualizarPrioridades($prioridades);
            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Prioridades actualizadas con éxito."
            ];
        } catch (\Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }
        echo json_encode($respuesta);
        exit;
    }

    /*=============================================
    ELIMINAR ORDEN DE UN BLOQUE
    =============================================*/
    public function eliminarOrdenBloque(){
        try {
            Autorizacion::authorize('delete', new ProgramacionPagos);
            $bloqueId = $_POST['bloqueId'] ?? null;
            $ordenId = $_POST['ordenId'] ?? null;
            if (empty($bloqueId) || empty($ordenId)) {
                throw new \Exception("Faltan datos para eliminar la orden del bloque.");
            }
            $programacionPagos = new ProgramacionPagos;
            $response = $programacionPagos->eliminarOrdenDeBloque($bloqueId, $ordenId);
            $respuesta = [
                'error' => false,
                'codigo' => 200,
                'respuestaMessage' => "Orden eliminada del bloque con éxito."
            ];
        } catch (\Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }
        echo json_encode($respuesta);
        exit;
    }
    /*=============================================
    MARCAR PAGO DE UN BLOQUE
    =============================================*/
    public function marcarPagado(){
        try {
            Autorizacion::authorize('update', new ProgramacionPagos);
            $bloqueId = $_POST['bloque_id'] ?? null;
            if (empty($bloqueId)) {
                throw new \Exception("Falta el ID del bloque para marcar como pagado.");
            }
            $programacionPagos = new ProgramacionPagos;
            $response = $programacionPagos->marcarPagado($bloqueId);
            $respuesta = [
                'error' => false,
                'codigo' => 200,
                'respuestaMessage' => "Bloque marcado como pagado con éxito."
            ];
        } catch (\Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }
        echo json_encode($respuesta);
        exit;
    }
}

/*=============================================
TABLA DE PROVEEDORES
=============================================*/
$ProgramacionPagosAjax = new ProgramacionPagosAjax;
try {
    if (isset($_POST["accion"]))
    {
        if( $_POST['accion'] == 'asignarOrdenes'){
            $ProgramacionPagosAjax->asignarOrdenes();
        } elseif ( $_POST["accion"] == 'actualizarPrioridades' ) {
            
            /*=============================================
            ACTUALIZAR PRIORIDADES DE LOS BLOQUES
            =============================================*/
            $ProgramacionPagosAjax->actualizarPrioridades();

        } elseif ( $_POST["accion"] == 'eliminarOrdenBloque') {

            /*=============================================
            ELIMINAR ORDEN DE UN BLOQUE
            =============================================*/
            $ProgramacionPagosAjax-> eliminarOrdenBloque();
        } elseif ( $_POST["accion"] == 'marcarPagado') {
            /*=============================================
            MARCAR PAGO DE UN BLOQUE
            =============================================*/
            $ProgramacionPagosAjax->marcarPagado();
            
        } else {
                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errorMessage' => "Realizó una petición desconocida."
                ];

            echo json_encode($respuesta);
        }
    }
} catch (Exception $e) {
    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];
    echo json_encode($respuesta);
    exit;
}

