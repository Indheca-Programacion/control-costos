<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Gastos.php";
require_once "../Models/GastoDetalles.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveGastoDetallesRequest.php";
require_once "../Models/Usuario.php";
require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Models/GastoArchivo.php";
require_once "../Models/Mensaje.php";
require_once "../Models/Perfil.php";
require_once "../Controllers/MailController.php";
use ZipArchive;
use App\Route;
use App\Models\GastoArchivo;
use App\Models\Mensaje;
use App\Models\Perfil;
use App\Models\ConfiguracionCorreoElectronico;
use App\Controllers\Autorizacion;
use App\Controllers\MailController;
use App\Models\Usuario;
use App\Models\Gastos;
use App\Models\GastoDetalles;
use App\Controllers\Validacion;
use App\Requests\SaveGastoDetallesRequest;
use Exception;

class GastosAjax
{

	/*=============================================
	TABLA DE GASTOS
	=============================================*/
	public function mostrarTabla()
	{
		$gasto = New Gastos;

        require_once "../Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null,usuarioAutenticado()["id"]);
		$usuario->consultarPerfiles();

        if (Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::perfil($usuario, 'pagos') ) {
            $gastos = $gasto->consultar();
		}else{
            $gastos = $gasto->consultarPorUsuario($usuario->id);
        }

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fecha_envio" ]);
        array_push($columnas, [ "data" => "encargado" ]);
        array_push($columnas, [ "data" => "tipoGasto" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($gastos as $key => $value) {
        	$rutaEdit = Route::names('gastos.edit', $value['id']);
        	$rutaDestroy = Route::names('gastos.destroy', $value['id']);
            $rutaPrint = Route::names('gastos.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "obra" => mb_strtoupper(fString($value["descripcion"])),
        							  "estatus" => mb_strtoupper(fString($value["estatus"])),
        							  "fecha_envio" => fFechaLarga($value["fecha_envio"]),
        							  "encargado" => mb_strtoupper(fString($value["nombreCompleto"])),
        							  "tipoGasto" => mb_strtoupper(fString($value["tipoGasto"])),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>
                                                     <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
    /*=============================================
	AGREGAR PARTIDAS
	=============================================*/
	public function agregarPartidas()
	{
		try {
			// Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "crear") ) throw new \Exception("No está autorizado a crear nuevos Indirectos.");

			$_POST["costo"] = floatval(str_replace(',', '', $_POST["costo"]));
			$request = SaveGastoDetallesRequest::validated();

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }

            $GastoDetalles = New GastoDetalles;

            // Crear el nuevo registro
            if ( !$GastoDetalles->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $GastoDetalles,
                'respuestaMessage' => "La partida fue agregada correctamente."
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
	OBTIENE PARTIDAS
	=============================================*/
    public function obtenerPartidas()
    {
        $gastoDetalle = New GastoDetalles;
        $gastoDetalles = $gastoDetalle->consultarPorGasto($this->gastoId);
        $gasto = New Gastos;
        $gasto->consultar(null,$this->gastoId);
        $partidas = array();
        foreach ($gastoDetalles as $key => $value) {
            $id = $value["id"];
            $token = token();
            $rutaDestroy = Route::names('gasto-detalles.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value["observaciones"]));
            $eliminar = ($gasto->cerrada == 0 ) ? "<form method='POST' action='{$rutaDestroy}' style='display: inline'>
                    <input type='hidden' name='_method' value='DELETE'>
                    <input type='hidden' name='_token' value='{$token}'>
                    <input type='hidden' name='gastoId' value='{$this->gastoId}'>
                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                    <i class='far fa-times-circle'></i>
                    </button>
            </form>" : ''  ; 
            array_push($partidas,[ "consecutivo" => ($key + 1),
                                   "fecha" => fFechaLarga($value["fecha"]),
                                   "gasto" => mb_strtoupper(fString($value["tipoGasto"])),
                                   "costo" => '$ '.$value["costo"],
                                   "obra" => mb_strtoupper(fString($value["obra"])),
                                   "economico" => mb_strtoupper(fString($value["economico"])),
                                   "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                                   "observaciones" => mb_strtoupper(fString($value["observaciones"])),
                                   "acciones" => "<button type='button' folio='{$id}' data-toggle='modal' data-target='#modalVerArchivos' class='btn btn-info btn-xs btn-mostrar-modal'><i class='fas fa-file'></i></button> <button type='button' folio='{$id}' id='btn-subirArchivo' class='btn btn-success btn-xs btn-subirArchivo'><i class='fas fa-file-upload'></i></button> <button type='button' folio='{$id}' class='btn btn-warning btn-xs btn-editar'><i class='fas fa-pencil-alt'></i></button>".$eliminar
            ]);
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos'] = $partidas;

        echo json_encode($respuesta);
    }
    /*=============================================
	OBTIENE ARCHIVOS
	=============================================*/
    public function obtenerArchivos(){

        $gasto = New GastoDetalles;

        $respuesta["archivos"] = array();

        // Consultar los archivos
        $respuesta["archivos"] = $gasto->consultarArchivos($this->gastoDetalleId);

        echo json_encode($respuesta);

    }
    /*=============================================
	CREAR REQUISICIONES
	=============================================*/
    public function crearRequisicion(){
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisiciones", "crear") ) throw new \Exception("No está autorizado a crear reqquisiciones.");
            
            $gasto = New GastoDetalles;
            $gastoDetalles = $gasto->consultarPorGasto($this->gastoId);

            require_once "../../app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;

            $datosReq = [
                "folio" => $this->folio,
                "periodo" => $this->periodo,
                "fk_IdObra" => $this->obraId,
                "usuarioIdCreacion" => usuarioAutenticado()["id"],
            ];

            if ( !$requisicion->crear($datosReq) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            require_once "../../app/Models/Partida.php";
            $partida = New \App\Models\Partida;

            $partidas = array();

            //Se crean los datos para ingreas a las partidas
            foreach ($gastoDetalles as $key => $value) {
                $costo_unitario = $value["costo"] / $value["cantidad"];
                array_push($partidas,[  "obraDetalleId" => $value["obraDetalle"],
                                        "requisicionId" => $requisicion->id,
                                        "cantidad" => $value["cantidad"],
                                        "costo" => $value["costo"],
                                        "periodo" => $this->periodo,
                                        "concepto" => mb_strtoupper(fString($value["observaciones"])),
                                        "unidadId" => $value["unidadId"],
                                        "costo_unitario" => $costo_unitario,
                ]);
            }
            //Se hacen insert de las partidas
            foreach($partidas as $datos) {
                $partida->crear($datos,[]);
            }
            
            $gasto = new Gastos;
            $gasto->id = intval($this->gastoId);
            $datosGasto = [
                "requisicionId" => $requisicion->id
            ];
            $gasto->actualizarRequisicionId($datosGasto);

            $respuesta = [
                'error' => false,
                'respuesta' => $requisicion,
                'respuestaMessage' => "La requisicion fue creada correctamente."
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
	CERRAR GASTOS DE CAJA CHICA
	=============================================*/
    public function cerrarGasto(){
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "editar") ) throw new \Exception("No está autorizado a cerrar gasto.");

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->cerrarGasto() ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $this->sendMailCerrarGasto($gasto);

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "La partida fue agregada correctamente."
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
    ENVIAR EMAIL PARA CERRAR GASTO
    =============================================*/
    public function sendMailCerrarGasto(Gastos $gastos)
    {
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $configuracionCorreoElectronico->consultarPerfilesCerrarGasto();    
            if ( $configuracionCorreoElectronico->perfilesCerrarGasto ) {

                $perfil = New Perfil;
                $perfil->consultarUsuarios($configuracionCorreoElectronico->perfilesCerrarGasto);

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

                $liga = Route::names('gastos.edit', $gastos->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>GASTO </h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha sido creada el gasto</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de un nuevo gasto, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Nueva gasto ",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "Se ha cerrado el gasto , entre a la aplicación para ver el detalle de la misma.",
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
    /*=============================================
    ELIMINAR ARCHIVO
    =============================================*/
    public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;

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

        $gastoArchivo = New GastoArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $gastoArchivo->id = $this->archivoId;
        $gastoArchivo->gastoDetalleId = $this->gastoDetalleId;
        if ( !$gastoArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $gastoArchivo->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);
    }
    /*=============================================
    AÑADIR ARCHIVO
    =============================================*/
    public function addArchivo()
    {
        try {
            $gastoDetalles = New GastoDetalles;
            $gastoDetalles->id = $this->gastoDetalleId;
            $gastoDetalles->tipo = $this->tipo;

            $response = $gastoDetalles->insertarArchivos($_FILES["archivos"]);

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Se añadio correctamente el documento."
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
	public $token;
    public $gastoDetalleId;

    public function descargarTodo()
    {
        $gastos = new Gastos();
        $archivos = $gastos->consultarArchivosDescargar($this->gastoId);

        if (empty($archivos)) {
            $respuesta = [
            'codigo' => 404,
            'error' => true,
            'errorMessage' => 'No hay archivos disponibles para descargar.'
            ];
            echo json_encode($respuesta);
            exit;
        }

        $zip = new ZipArchive();
        $zipFilename = 'archivos.zip';

        // Verificar si existe un archivo ZIP existente
        if (file_exists($zipFilename)) {
            unlink($zipFilename); // Eliminar el archivo ZIP existente
        }

        if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
            foreach ($archivos as $key => $file) {
                $filePath = '../../' . $file["ruta"];
                $fileName = $file["titulo"];
                if ($file["tipo"] == 2) {
                    $zip->addFile($filePath, 'soportes/' . $fileName);
                } else {
                    $zip->addFile($filePath, $fileName);
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
            header('Content-Length: ' . filesize($zipFilename));
            // Enviar el archivo
            readfile($zipFilename);
            unlink($zipFilename);
            exit;
        } else {
            $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => 'No se pudo crear el archivo ZIP.'
            ];
            echo json_encode($respuesta);
            exit;
        }
        
    }

    public function obtenerGastoDetalles()
    {
        try {
            $gastoDetalles = new GastoDetalles;
            $respuesta = $gastoDetalles->consultar(null, $this->gastoDetalleId);
            if (empty($respuesta)) {
                $respuesta = [
                    'codigo' => 404,
                    'error' => true,
                    'errorMessage' => 'No se encontraron detalles de gasto.'
                ];
                echo json_encode($respuesta);
                exit;
            }
            $respuesta["fecha"] = fFechaLarga($respuesta["fecha"]);
            $respuesta = [
                'codigo' => 200,
                'error' => false,
                'datos' => $respuesta
            ];
        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
            echo json_encode($respuesta);
            exit;
        }
        echo json_encode($respuesta);
    }

    public function actualizarGastoDetalle()
    {
        try {
            $gastoDetalles = new GastoDetalles;
            $request = SaveGastoDetallesRequest::validated();
            
            if ( errors() ) {
                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];
                unset($_SESSION[CONST_SESSION_APP]["errors"]);
                echo json_encode($respuesta);
                return;
            }
            
            if ( !$gastoDetalles->actualizar($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gastoDetalles,
                'respuestaMessage' => "La partida fue actualizada correctamente."
            ];

        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
            echo json_encode($respuesta);
            exit;
        }
        echo json_encode($respuesta);
    }

    public function descargarFacturas()
    {
        $gastos = new Gastos;
        $archivos = $gastos->consultarArchivosDescargar($this->gastoId);

        if (empty($archivos)) {
            $respuesta = [
                'codigo' => 404,
                'error' => true,
                'errorMessage' => 'No hay archivos disponibles para descargar.'
            ];
            echo json_encode($respuesta);
            exit;
        }

        $ruta = $this->crearFormatoGCC($this->gastoId);

        $pdfFiles = [];

        if (file_exists($ruta)) {
            $pdfFiles[] = escapeshellarg($ruta);
        } else {
            $respuesta = [
                'codigo' => 404,
                'error' => true,
                'errorMessage' => 'No se pudo generar el formato de gastos.'
            ];
            echo json_encode($respuesta);
            exit;
        }

        // Unir los archivos PDF usando pdfunite
        foreach ($archivos as $file) {
            if ($file["formato"] == "application/pdf") { // Solo facturas
                $filePath = '../../' . $file["ruta"];
                if (file_exists($filePath)) {
                    $pdfFiles[] = escapeshellarg($filePath);
                }
            }
        }

        if (empty($pdfFiles)) {
            $respuesta = [
            'codigo' => 404,
            'error' => true,
            'errorMessage' => 'No hay facturas PDF disponibles para unir.'
            ];
            echo json_encode($respuesta);
            exit;
        }

        $outputPdf = 'facturas_unidas.pdf';
        if (file_exists($outputPdf)) {
            unlink($outputPdf);
        }

        $cmd = 'pdfunite ' . implode(' ', $pdfFiles) . ' ' . escapeshellarg($outputPdf);
        exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($outputPdf)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $outputPdf . '"');
            header('Content-Length: ' . filesize($outputPdf));
            readfile($outputPdf);
            unlink($outputPdf);
            if (file_exists($ruta)) {
                unlink($ruta);
            }
            exit;
        } else {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => 'No se pudo crear el archivo ZIP.'
            ];
            echo json_encode($respuesta);
            exit;
        }
    }

    function crearFormatoGCC($id){
        $gastos = New Gastos;

        if ( $gastos->consultar(null , $id) ) {

            require_once "../../app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null, $gastos->obra);

            require_once "../../app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;

            $usuarioAutorizacion = null;
            $autorizacionFirma = null;
            if ( !is_null($gastos->usuarioIdAutorizacion) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $gastos->usuarioIdAutorizacion);

                $usuarioAutorizacion = mb_strtoupper($usuario->nombreCompleto);
                $autorizacionFirma = $usuario->firma;
                unset($usuario);
            }

            $usuarioEncargado = null;
            $encargadoFirma = null;
            if ( !is_null($gastos->encargado) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $gastos->encargado);

                $usuarioEncargado = mb_strtoupper($usuario->nombreCompleto);
                $encargadoFirma = $usuario->firma;
                unset($usuario);
            }

            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $gastos->encargado);

            require_once "../../app/Models/GastoDetalles.php";
            $gastosDetalles = New \App\Models\GastoDetalles;

            $detallesGastos = $gastosDetalles->consultarPorGasto($gastos->id);

            if($gastos->tipoGasto == 1){
                include "../../reportes/gastos-deducibles-conjunto.php";
                return "/var/www/html/reportes/tmp/GastosDeducibles.pdf";
            }else{
                include "../../reportes/gastos-no-deducibles-conjunto.php";
                return "/var/www/html/reportes/tmp/GastosNoDeducibles.pdf";
            }

        }

    }

    public function autorizarGasto()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "autorizar") ) throw new \Exception("No está autorizado a autorizar gastos.");

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->autorizarGasto() ) throw new \Exception("Hubo un error al intentar autorizar el gasto, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "El gasto fue autorizado correctamente."
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

    public function enProcesoGasto()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "ver") ) throw new \Exception("No está autorizado a marcar gastos como en proceso.");

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->actualizarEstatus(["procesado" => 1]) ) throw new \Exception("Hubo un error al intentar marcar el gasto como en proceso, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "El gasto fue marcado como en proceso correctamente."
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

    public function procesarGasto()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "ver") ) throw new \Exception("No está autorizado a marcar gastos como procesados.");

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->actualizarEstatus(["procesado" => 2]) ) throw new \Exception("Hubo un error al intentar marcar el gasto como procesado, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "El gasto fue marcado como procesado correctamente."
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

    public function marcarPagado()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "ver") ) throw new \Exception("No está autorizado a marcar gastos como pagados.");

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->actualizarEstatus(["procesado" => 3]) ) throw new \Exception("Hubo un error al intentar marcar el gasto como pagado, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "El gasto fue marcado como pagado correctamente."
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

    public function enlazarRequisicion()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "ver") ) throw new \Exception("No está autorizado a enlazar requisiciones.");

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;
            $gasto->requisicionId = $this->requisicionId;

            if ( !$gasto->enlazarRequisicion() ) throw new \Exception("Hubo un error al intentar enlazar la requisición, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "La requisición fue enlazada correctamente."
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

$gastos = new GastosAjax();

if ( isset($_POST["accion"]) ) {

    if ( $_POST["accion"] == "verArchivos" ){
        /*=============================================
        OBTIENE ARCHIVOS
        =============================================*/
        $gastos->gastoDetalleId = $_POST["gastoDetalleId"];
        $gastos->obtenerArchivos();
    
    } elseif ( $_POST["accion"] == "crearRequisicion") {
        
        /*=============================================
        OBTIENE ARCHIVOS
        =============================================*/
        $gastos->gastoId = $_POST["gastoId"];
        $gastos->obraId = $_POST["obraId"];
        $gastos->periodo = $_POST["periodo"];
        $gastos->folio = $_POST["folio"];
        $gastos->crearRequisicion();

    } elseif ( $_POST["accion"] == 'eliminarArchivo' && isset($_POST["archivoId"]) ) {

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $gastos->token = $_POST["_token"];
        $gastos->archivoId = $_POST["archivoId"];
        $gastos->gastoDetalleId = $_POST["gastoDetalleId"];
        $gastos->eliminarArchivo();
    
    } elseif ( $_POST["accion"] == 'subir-archivo'){
        /*=============================================
        SUBIR ARCHIVO
        =============================================*/
        $gastos->token = $_POST["_token"];
        $gastos->gastoDetalleId = $_POST["gastoDetalleId"];
        $gastos->tipo = $_POST["tipo"];
        $gastos->addArchivo();
    } elseif ($_POST["accion"] == "cerrarGasto"){
        /*=============================================
        CERRAR GASTO 
        =============================================*/	
        $gastos->gastoId = $_POST['gastoId'];
        $gastos->cerrarGasto();
    } elseif ( $_POST["accion"] == "actualizarGasto"){
        /*=============================================
        ACTUALIZAR GASTO 
        =============================================*/
        $gastos->actualizarGastoDetalle();
    } elseif ( $_POST["accion"] == "autorizarGasto" ) {
        /*=============================================
        AUTORIZAR GASTO
        =============================================*/
        $gastos->gastoId = $_POST['gastoId'];
        $gastos->autorizarGasto();
    } elseif ( $_POST["accion"] == "enProcesoGasto" ) {
        /*=============================================
        MARCAR GASTO COMO EN PROCESO
        =============================================*/
        $gastos->gastoId = $_POST['gastoId'];
        $gastos->enProcesoGasto();
    } elseif ( $_POST["accion"] == "procesarGasto" ) {
        /*=============================================
        MARCAR GASTO COMO PROCESADO
        =============================================*/
        $gastos->gastoId = $_POST['gastoId'];
        $gastos->procesarGasto();
    } elseif ( $_POST["accion"] == "marcarPagado"){
        /*=============================================
        MARCAR GASTO COMO PAGADO
        =============================================*/
        $gastos->gastoId = $_POST['gastoId'];
        $gastos->marcarPagado();
    }  elseif ( $_POST["accion"] == "enlazarRequisicion"){
        /*=============================================
        ENLAZAR REQUISICIÓN
        =============================================*/
        $gastos->gastoId = $_POST['gastoId'];
        $gastos->requisicionId = $_POST['requisicionId'];
        $gastos->enlazarRequisicion();
    } else {

        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => "Realizó una petición desconocida."
        ];

        echo json_encode($respuesta);

    }
    
} else if ( isset($_POST["costo"]) ) {
    /*=============================================
    AGREGAR DETALLES DE GASTOS
    =============================================*/	
    $gastos->agregarPartidas();

} elseif ( isset($_GET["gasto"]) ) {
    /*=============================================
    OBTIENE LOS DETALLES DE GASTOS
    =============================================*/
    $gastos->gastoId = $_GET["gasto"];
    $gastos->obtenerPartidas();

} elseif ( isset($_GET["accion"]) && $_GET["accion"] == "descargarFacturas" ) {

    /*=============================================
    DESCARGA LAS FACTURAS DE LOS GASTOS
    =============================================*/
    $gastos->gastoId = $_GET["gastoId"];
    $gastos -> descargarFacturas();
} else if ( isset($_GET["gastoId"]) ){
    /*=============================================
    DESCARGA TODOS LOS ARCHIVOS EN CONJUNTO
    =============================================*/
    $gastos->gastoId = $_GET["gastoId"];
    $gastos->descargarTodo();
} elseif ( isset($_GET["gastoDetalleId"]) ){
    /*=============================================
    OBTIENE LOS DETALLES DE GASTOS
    =============================================*/
    $gastos->gastoDetalleId = $_GET["gastoDetalleId"];
    $gastos->obtenerGastoDetalles();
} else {

	/*=============================================
    OBTIENE LA TABLA DE GASTOS
	=============================================*/
	$gastos -> mostrarTabla();

}
