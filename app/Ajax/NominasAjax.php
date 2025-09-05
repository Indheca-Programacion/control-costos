<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Nominas.php";
require_once "../Models/Asistencias.php";
require_once "../Requests/SaveNominasRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Nominas;
use App\Models\Asistencias;
use App\Requests\SaveNominasRequest;
use App\Controllers\Autorizacion;

class NominasAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
		$nomina = New Nominas;
        $nominas = $nomina->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "semana" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($nominas as $key => $value) {
        	$creo = $value['nombre'] . ' ' . $value['apellidoPaterno'];
            $rutaEdit = Route::names('nominas.edit', $value['id']);
            $folio = mb_strtoupper(fString($value["semana"]));
        	$rutaDestroy = Route::names('nominas.destroy', $value['id']);
        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
				"obra" => mb_strtoupper(fString($value["descripcion"])),
				"semana" => mb_strtoupper(fString($value["semana"])),
				"fecha" => mb_strtoupper(fString(date("d/m/Y", strtotime($value["fechaCreacion"])))),
                "creo" => mb_strtoupper(fString($creo)),
                "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
								<form method='POST' action='{$rutaDestroy}' style='display: inline'>
									<input type='hidden' name='_method' value='DELETE'>
									<input type='hidden' name='_token' value='{$token}'>
									<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
										<i class='far fa-times-circle'></i>
									</button>
								</form>"
			] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
	/*=============================================
	Obtener Semana
	=============================================*/
	public function getSemana()
	{
		try {

            require_once "../Models/Obra.php";
			$obras = New \App\Models\Obra;
			
            $obras->consultar(null,$_GET["obraId"]);
            $semana = floatval($obras->periodos)+floatval($obras->semanaExtra);

            $nomina = New Nominas;
            $nominas = $nomina->consultar('fk_obraId',$_GET["obraId"]);

            $arrayNomina = array();
            foreach ($nominas as $key => $value) {
                array_push($arrayNomina,intval($value["semana"]));
            }
            
            $arraySemanas = array();
            for ($i=1; $i <= $semana; $i++) { 
                if (!in_array($i,$arrayNomina)) {
                    array_push($arraySemanas,$i);
                }
            }

			$respuesta = [
				'error' => false,
				'respuesta' => $arraySemanas,
                'fecha' => $obras->fechaInicio,
				'respuestaMessage' => "Se ha agregado con exito."
			];
		}  catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }
		echo json_encode($respuesta);
		
	}
    /*=============================================
	Obtiene las asistencias correspondientes a las fechas de una obra
	=============================================*/
    public function getAsistencias($fechaInicial,$fechaFinal,$obra)
    {
        try {
            require_once "../Models/Asistencias.php";
			$asistencia = New \App\Models\Asistencias;
			
            $asistencias = $asistencia->consultarAsistencias($fechaInicial,$fechaFinal,$obra);

            $arrayAsistencias = array();
            foreach ($asistencias as $key => $value) {
                $hrsExtras = $value["horasExtras"];
                $empleado = $value["fk_empleado"];
                $fecha = $value["fecha"];
                $nombre = $value["nombreCompleto"];
                $incidencia = $value["incidencia"];
                array_push($arrayAsistencias,["hrsExtras"=>$hrsExtras,"empleado" => $empleado,"fecha" => $fecha,"nombre" => $nombre,"incidencia" => $incidencia]);
            }
            
            $arrayNomina = array();
            
            foreach ($arrayAsistencias as $key => $value) {
                $timestamp  = strtotime($value["fecha"]); 
                $diaSemana = date('w', $timestamp);
                $domingo = 0;
                if ($diaSemana == 0) $domingo = 1;

                if($value["incidencia"] == '0')
                if (!isset($arrayNomina[$value["empleado"]])) {
                    $arrayNomina[$value["empleado"]] = array(
                        "hrsExtras" => intval($value["hrsExtras"]),
                        "dias" => 1,
                        "domingo" => $domingo,
                        "empleado" => $value["empleado"],
                        "nombre" => mb_strtoupper($value["nombre"]),
                        "primas" => 0,
                        "comida" => 0,
                        "prestamos" => 0,
                        "descuentos" => 0,
                        "pension" => 0

                    );
                } else {
                    $arrayNomina[$value["empleado"]]["hrsExtras"] += intval($value["hrsExtras"]);
                    $arrayNomina[$value["empleado"]]["domingo"] += $domingo;
                    $arrayNomina[$value["empleado"]]["dias"] += 1;
                }
            }

            require_once "../Models/RequisicionPersonal.php";
			$requisicionP = New \App\Models\RequisicionPersonal;

            $requisicion = $requisicionP->consultarPorObra($obra);

            $arrayFinal = array();
            foreach ($arrayNomina as $key => $value) {
                $nomina = $value;
                foreach ($requisicion as $key => $value) {
                    $arrayTrabajadores = json_decode($value["trabajadores"]);
                    if (in_array($nomina["empleado"],$arrayTrabajadores)) {
                        $nomina["salario"] = floatval($value["salario_semanal"]);
                        $nomina["obraDetalle"] = floatval($value["fk_obraDetalleId"]);
                        $nomina["puesto"] = mb_strtoupper($value["descripcion"]);
                        array_push($arrayFinal,$nomina);
                        break;
                    }
                }
            }

            $respuesta = [
				'error' => false,
				'respuesta' => $arrayFinal,
				'respuestaMessage' => "Se ha obtuvieron con exito los registros."
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
}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $NominasAjax = New NominasAjax();
    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "crear" ) {

            /*=============================================
            CREAR INDIRECTO
            =============================================*/
            // $NominasAjax->getPuesto();

        } else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }
    }
    else if (isset($_GET["obraId"])) {

		$NominasAjax->getSemana();

    } 
    else if (isset($_GET["fechaInicial"])) {

		$NominasAjax->getAsistencias($_GET["fechaInicial"],$_GET["fechaFinal"],$_GET["obra"]);

    } else {
        /*=============================================
        TABLA DE ASISTENCIAS
        =============================================*/
        $NominasAjax->mostrarTabla();
    }


} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}


