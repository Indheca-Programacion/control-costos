<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Asistencias.php";
require_once "../Models/RequisicionPersonal.php";

require_once "../Requests/SaveNominasRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;

use App\Models\Asistencias;
use App\Models\RequisicionPersonal;
use App\Requests\SaveNominasRequest;
use App\Controllers\Autorizacion;

class AsistenciasAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
		$asistencia = New Asistencias;
        $asistencias = $asistencia->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "nombre" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "horaEntrada" ]);
        array_push($columnas, [ "data" => "horaSalida" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "incidencia" ]);
        // array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($asistencias as $key => $value) {
            switch ($value["incidencia"]) {
                case 0:
                    $value["incidencia"] = 'N/A';
                    break;
                case 1:
                    $value["incidencia"] = 'Falta';
                    break;
                case 2:
                    $value["incidencia"] = 'Incapacidad';
                    break;
                case 3:
                    $value["incidencia"] = 'Vacaciones';
                    break;
                default:
                    break;
            }
        	$rutaEdit = Route::names('nominas.edit', $value['id']);
        	$rutaDestroy = Route::names('nominas.destroy', $value['id']);
        	// $folio = mb_strtoupper(fString($value['descripcion']));
        	$creo = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            $horaEntrada = date("h:i a", strtotime($value["horaEntrada"]));
            $horaSalida = date("h:i a", strtotime($value["horaSalida"]));
            if($value["horaEntrada"] == '00:00:00') $horaEntrada = '00:00';
            if($value["horaSalida"] == '00:00:00') $horaSalida = '00:00';

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"fecha" => mb_strtoupper(fString(date("d/m/Y", strtotime($value["fecha"])))),
				"nombre" => mb_strtoupper(fString($value["nombreCompleto"])),
				"obra" => mb_strtoupper(fString($value["obra.nombre"])),
				"horaEntrada" => mb_strtoupper(fString($horaEntrada)),
                "horaSalida" => mb_strtoupper(fString($horaSalida)),
				"creo" => mb_strtoupper(fString($creo)),
				"incidencia" => mb_strtoupper(fString($value["incidencia"])),
				"acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"
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
	Obtener Puestos
	=============================================*/
	public function getPuesto()
	{
		try {
            
			$asistencia = new Asistencias;
            $resultados = $asistencia->getPuestos($_GET["obraId"]);
			// Crear el nuevo registro

            $registros = array();

            foreach ($resultados as $key => $value) {
                if (is_null($value["descripcionI"])) {
                    $descripcion = $value["descripcionD"];
                }else{
                    $descripcion = $value["descripcionI"];
                }
                array_push($registros,[
                    "obraDetalleId" => $value["id"],
                    "descripcion" => $descripcion,
                ]);
            }


			$respuesta = $registros;
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
	Obtener Puestos
	=============================================*/
	public function getEmpleados()
	{
		try {
            $requisicion = new RequisicionPersonal;
            $empleados = $requisicion->getEmpleados($_GET["reqPersonalId"]);
            $fecha = $_GET["fecha"];
            $array = json_decode($empleados["trabajadores"]);
            $resultados = "";
            if (!empty($array)){
                $asistencia = new Asistencias;
                // Convertir la cadena de texto a un array
                $empleadosId = implode(', ', $array);
                $resultados = $asistencia->getEmpleados($empleadosId,$fecha);
            } 

			$respuesta = [
                'codigo' => 500,
                'error' => false,
                'registros' => $resultados
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
}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $AsistenciasAjax = New AsistenciasAjax();
    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "obtener" ) {

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
        /*=============================================
        OBTIENE LOS PUESTOS DE ACUERDO A LA OBRA
        =============================================*/
		$AsistenciasAjax->getPuesto();
    }
    else if (isset($_GET["reqPersonalId"])){
        /*=============================================
        OBTIENE LOS EMPLEADOS DE ACUERDO AL ID
        =============================================*/
        $AsistenciasAjax->getEmpleados();
    } else {
        /*=============================================
        TABLA DE ASISTENCIAS
        =============================================*/
        $AsistenciasAjax->mostrarTabla();
    }


} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}