<?php

namespace App\Ajax;
use Exception;

session_start();

// Configuración de Errores
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/control-costos/php_error_log');

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/QrCarga.php";
require_once "../Models/Carga.php";
require_once "../Models/QrCargaArchivo.php";

require_once "../Models/MaquinariaTraslado.php";
require_once "../Controllers/Autorizacion.php";

require_once "../Requests/SaveCargasRequest.php";

require_once "../../vendor/autoload.php";

use App\Route;
use App\Models\Usuario;
use App\Models\QrCarga;
use App\Models\Carga;
use App\Models\QrCargaArchivo;
use App\Models\MaquinariaTraslado;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

use App\Requests\SaveCargasRequest;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class QrCargaAjax
{

	/*=============================================
	TABLA DE QR
	=============================================*/
	public function mostrarTabla()
	{
		$qrCargas = New QrCarga;
        $qrs = $qrCargas->consultar();


		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "id" ]);
        array_push($columnas, [ "data" => "placa" ]);
        array_push($columnas, [ "data" => "operador" ]);
        array_push($columnas, [ "data" => "capacidadCarga", "title" => "Capacidad de Carga" ]);
        array_push($columnas, [ "data" => "estatus", "title" => "Estatus" ]);
        array_push($columnas, [ "data" => "acciones", "title" => "Acciones", "orderable" => false, "searchable" => false ]);

        $token = createToken();
        
        $registros = array();
        foreach ($qrs as $key => $value) {

        	$rutaEdit = Route::names('qr-cargas.edit', $value['nId01Qr']);
        	$rutaDestroy = Route::names('qr-cargas.destroy', $value['nId01Qr']);

            $folio = $value["nId01Qr"];

            $disabled = $value["sEstatus"] == "DISPONIBLE" ? "disabled" : "";

        	array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "id" => sprintf("C%04d", $value["nId01Qr"]),
                "placa" => $value["sPlaca"],
                "operador" => $value["nombreOperador"],
                "capacidadCarga" => mb_strtoupper($value["sCapacidad"]. " Toneladas"),
                "estatus" =>  $value["sEstatus"],
                "acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-edit'></i></a>
								<button type='button' class='btn btn-xs btn-info' data-toggle='modal' data-target='#qrModal' data-qr='{$value['nId01Qr']}'>
									<i class='fas fa-qrcode'></i>
								</button>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>
								"
								
			]);
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

    /*=============================================
	REGISTRAR CARGA
	=============================================*/
	public $archivo;
	public $datos;

    public function registrarCarga()
    {

        try {

            $archivo = $this->archivo;
    
            if ($archivo['error'] != 0) {
                throw new Exception('Error al subir el archivo');
            }
    
            $directorio = '../../vistas/uploaded-files/cargas/ticket-carga/';
    
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
    
            $nombreArchivo = uniqid() . '_' . 'ticket-carga';

            $destino = $directorio . $nombreArchivo;
    
            if (file_exists($destino)) {
                $mensajeArchivo = 'El archivo ya existe';
            } else {
                if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
                    throw new Exception('No se pudo mover el archivo');
                }
                $mensajeArchivo = 'Archivo subido correctamente';

                $directorio = './vistas/uploaded-files/cargas/ticket-carga/';
                $destino = $directorio . $nombreArchivo;

                $this->datos["sUrlTicket"] = $destino;
            }

            $carga = new Carga;
            $carga->crear($this->datos);
            
            $respuesta = [
                'codigo' => 200,
                'respuestaMessage' => 'Se creó con éxito el registro'
            ];
    
        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }
    
        echo json_encode($respuesta);
    }
    
	/*=============================================
	VER QR
	=============================================*/
	public function verQr()
	{
		try {
			$qrCarga = new QrCarga;
			$qr = $qrCarga->consultar(null,$_POST["qr"]);

			if ( !$qr ) throw new \Exception("No se encontró el QR.");

			$qr_code = QrCode::create(Route::names('qr-cargas.edit',$_POST["qr"]))
            ->setSize(600)
            ->setMargin(40)
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh);
            
            $writer = new PngWriter;
            
			$result = $writer->write($qr_code);

            // $nombreArchivo = "qr-code.png";
            // $filePath = "/tmp/" . $nombreArchivo;

			$filePath = "../../tmp/qr-code.png";
			$result->saveToFile($filePath);

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['qrUrl'] = 'tmp/qr-code.png';

		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}

		echo json_encode($respuesta);
	}

	public function darBajaQR()
	{
		try {

			$qrCarga = new QrCarga;
			$qrCarga->id = $this->idQrCarga;
            $qrCarga->idMaquinaria = $this->idMaquinaria;
            $qrCarga->idCarga = $this->idCarga;

			// DAR BAJA EQUIPO
			 $qrCarga->darBaja();

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;

		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}

		echo json_encode($respuesta);
	}

    public function buscarPlaca()
    {
        try {
            $placa = $_POST["placa"];
            
            $maquinariaTraslado = new MaquinariaTraslado;
            $maquinariaTraslado->consultar('sPlaca', $placa);

            if ( !$maquinariaTraslado->sPlaca ) throw new \Exception("No se encontró la placa.");

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['respuesta'] = $maquinariaTraslado;

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
    ELIMINAR ARCHIVO
    =============================================*/
    public $archivoId;
    public $idMaquinaria;

    public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( !usuarioAutenticado() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";
        } 

        // Validar Token
        if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $qrCargaArchivo = New QrCargaArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $qrCargaArchivo->id = $this->archivoId;
        $qrCargaArchivo->idMaquinaria = $this->idMaquinaria;

        if ( !$qrCargaArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $qrCargaArchivo->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);

    }

}


$qrCargaAjax = new QrCargaAjax;

if ( isset($_POST["accion"]) ) {
    if ( $_POST["accion"] == "registroCarga" ) {
        $qrCargaAjax->archivo = $_FILES['archivo'];
        $qrCargaAjax->datos = $_POST;

        $qrCargaAjax->registrarCarga();
    }
     elseif ( $_POST["accion"] == "obtenerQr" ) {
		$qrCargaAjax->verQr();
	}
	elseif ( $_POST["accion"] == "darBajaQr" ) {

        $qrCargaAjax->idQrCarga = $_POST["idQrCarga"];
        $qrCargaAjax->idMaquinaria = $_POST["idMaquinaria"];
        $qrCargaAjax->idCarga = $_POST["idCarga"];

		$qrCargaAjax->darBajaQR();
	}
    elseif ( $_POST["accion"] == "buscarPlaca" ) {
        $qrCargaAjax->buscarPlaca();
    }
    else if($_POST["accion"] == "eliminarArchivo"){
        /*=============================================
       ELIMINAR ARCHIVO
       =============================================*/
       $qrCargaAjax->token = $_POST["_token"];
       $qrCargaAjax->archivoId = $_POST["archivoId"];
       $qrCargaAjax->idMaquinaria = $_POST["idMaquinaria"];


       $qrCargaAjax->eliminarArchivo();
    }
	else {

        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => 'Acción no encontrada'
        ];
    }

}else{
    $qrCargaAjax->mostrarTabla();
}
