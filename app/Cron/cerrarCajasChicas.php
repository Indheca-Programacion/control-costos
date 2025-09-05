<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/app/Cron/php_error_log');

chdir('/var/www/html/app/Cron/');
define('CONST_SESSION_APP', "appControlCostos");
$_SESSION[CONST_SESSION_APP]["modoProduccion"] = true;
require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Gastos.php";
require_once "../Models/GastoDetalles.php";
require_once "../Models/Partida.php";
require_once "../Models/Requisicion.php";
require_once "../Models/Obra.php";

use App\Conexion;
use App\Models\Gastos;
use App\Models\GastoDetalles;
use App\Models\Partida;
use App\Models\Requisicion;
use App\Models\Obra;

$query="SELECT g.* 
FROM gastos g
INNER JOIN gasto_detalles gd ON g.id = gd.gastoId
WHERE g.requisicionId IS null and g.cerrada = 0
GROUP BY g.id
HAVING COUNT(gd.id) > 0;";

$respuesta = Conexion::queryAll(CONST_BD_APP, $query, $error);

if (count($respuesta)>0) {
    
    foreach ($respuesta as $key => $value) {
        
        $gasto = new Gastos;
        $gasto->id = $value["id"];

        $gasto->cerrarGasto();
        
        // $gastoDetalle = New GastoDetalles;
        
        // $requisicion = New Requisicion;
        // $lastId = $requisicion->consultarId($value["obra"]);

        // $lastId = isset($lastId[0]["folio"]) ? (int) $lastId[0]["folio"] + 1 : 1;
        // $obra = New Obra;
        // $obra->consultar(null,$value["obra"]);
        // $gastoDetalles = $gastoDetalle->consultarPorGasto($value["id"]);

        // $numeroSemana = calcularNumeroSemana($obra->fechaInicio, date("Y-m-d"));
        
        // $datosReq = [
        //     "folio" => $lastId,
        //     "periodo" => $numeroSemana,
        //     "fk_IdObra" => $value["obra"],
        //     "usuarioIdCreacion" => $value["usuarioIdCreacion"],
        //     "divisa" => 1,
        //     "tipoRequisicion" => 1,
        //     "fechaRequerida " => "",
        //     "direccion" => "",
        //     "especificaciones" => "",
        //     "categoriaId" => 1,
        //     "presupuesto" => 0,
        //     "proveedorId" => 0,
        //     "justificacion" => "",
            
        // ];

        // if ( !$requisicion->crear($datosReq) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

        // $partida = New Partida;

        // $partidas = array();

        // //Se crean los datos para ingreas a las partidas
        // foreach ($gastoDetalles as $key => $value) {
        //     $costo_unitario = $value["costo"] / $value["cantidad"];
        //     array_push($partidas,[  "obraDetalleId" => $value["obraDetalle"],
        //                             "requisicionId" => $requisicion->id,
        //                             "cantidad" => $value["cantidad"],
        //                             "costo" => $value["costo"],
        //                             "periodo" => $numeroSemana,
        //                             "concepto" => mb_strtoupper(fString($value["observaciones"])),
        //                             "unidadId" => $value["unidadId"],
        //                             "costo_unitario" => $costo_unitario,
        //     ]);
        // }
        // //Se hacen insert de las partidas
        // foreach($partidas as $datos) {
        //     $partida->crear($datos,[]);
        // }
        
        // $datosGasto = [
        //     "requisicionId" => $requisicion->id
        // ];

        // $gasto->actualizarRequisicionId($datosGasto);

    }

}

?>