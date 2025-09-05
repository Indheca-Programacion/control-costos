<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Mensaje.php";
require_once "../Models/Obra.php";
require_once "../Models/Proveedor.php";
require_once "../Models/Empresa.php";
require_once "../Models/Perfil.php";
require_once "../Models/Requisicion.php";
require_once "../Models/Partida.php";
require_once "../Models/Tarea.php";
require_once "../Models/PuestoUsuario.php";
require_once "../Models/InventarioDetalles.php";
require_once "../Models/RequisicionArchivo.php";
require_once "../Models/correoProveedor.php";
require_once "../Models/NotaInformativa.php";
require_once "../Requests/SaveRequisicionRequest.php";
require_once "../Requests/SaveNotaInformativaRequest.php";
require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Controllers/MailController.php";
require_once "../Controllers/Autorizacion.php";
require_once "../../vendor/autoload.php";

use App\Route;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use iio\libmergepdf\Driver\TcpdiDriver;
use App\Models\Usuario;
use App\Models\Requisicion;
use App\Models\Perfil;
use App\Models\Partida;
use App\Models\Mensaje;
use App\Models\Obra;
use App\Models\Tarea;
use App\Models\correoProveedor;
use App\Models\Proveedor;
use App\Models\PuestoUsuario;
use App\Models\NotaInformativa;

use App\Models\Empresa;
use App\Models\InventarioDetalles;
use App\Controllers\MailController;
use App\Models\ConfiguracionCorreoElectronico;
use App\Models\RequisicionArchivo;
use App\Requests\SaveRequisicionRequest;
use App\Requests\SaveNotaInformativaRequest;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class RequisiconAjax
{
    public $obraId;
	/*=============================================
	TABLA DE REQUISICIONES
	=============================================*/
	public function mostrarTabla()
	{
		
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        $usuario->consultarPerfiles();

        $requisicion = New Requisicion;

        if ( $usuario->empresaId == 4 ) {

            $requisiciones = $requisicion->consultarRoal();

        }else{
            
            if ( in_array(CONST_ADMIN, $usuario->perfiles) || in_array('gerente', $usuario->perfiles) || in_array('contratos', $usuario->perfiles) || in_array('contador', $usuario->perfiles) || in_array('pagos', $usuario->perfiles) || in_array('COMPRAS', $usuario->perfiles)  ) {
                $requisiciones = $requisicion->consultar();
            }else{
                $requisiciones = $requisicion->consultarPorObra();
            }
        }

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo", "title" => "#" ]);
        array_push($columnas, [ "data" => "obra", "title" => "Obra", "width" => "300px" ]);
        array_push($columnas, [ "data" => "folio", "title" => "Folio" ]);
        array_push($columnas, [ "data" => "estatus", "title" => "Estatus" ]);
        array_push($columnas, [ "data" => "fechaRequisicion", "title" => "Fecha Requisición" ]);
        array_push($columnas, [ "data" => "fechaActualizacion", "title" => "Fecha Ultima Actualizacion" ]);
        array_push($columnas, [ "data" => "costo", "title" => "Monto" ]);
        array_push($columnas, [ "data" => "oc", "title" => "Ordenes de Compra" ]);
        array_push($columnas, [ "data" => "categoria", "title" => "Categoria" ]);
        array_push($columnas, [ "data" => "estatusOC", "title" => "Estatus de la OC" ]);
        array_push($columnas, [ "data" => "proveedor", "title" => "Proveedor" ]);
        array_push($columnas, [ "data" => "solicito" , "title" => "Solicitó" ]);
        array_push($columnas, [ "data" => "acciones" , "title" => "Acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($requisiciones as $consecutivo => $value) {
        	$rutaEdit = Route::names('requisiciones.edit', $value['id']);
        	$rutaDestroy = Route::names('requisiciones.destroy', $value['id']);
            $rutaPrint = Route::names('requisiciones.print', $value['id']);
        	$folio =  mb_strtoupper(fString($value["prefijo"].'-'.$value['folio']));
            $solicito = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            $requisicion->id = $value["id"];
            $requisicion->consultarOrdenesCompra();
            $oc =' ';
            $proveedor = ' ';
            $estatusOC = ' ';
            foreach ($requisicion->ordenes_compra as $key => $orden) {
                $oc ="OC".$orden["folio"].' | '.$oc;
                $proveedor = $orden["proveedor"].' | '.$proveedor;
                $estatusOC = $orden["estatus.descripcion"].' | '. $estatusOC;
            }
            $proveedor = " | ".$value["nombreProveedor"];

            if ( !is_null($value['usuarios.apellidoMaterno']) ) $solicito .= ' ' . $value['usuarios.apellidoMaterno'];
            $eliminar="";
            if (Autorizacion::permiso($usuario, "requisiciones", "eliminar")) {
                $eliminar = "
                            <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='_token' value='{$token}'>
                                <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                    <i class='far fa-times-circle'></i>
                                </button>
                            </form>";
            }

            require_once "../Models/CategoriaOrdenes.php";
            $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
            $categoriaOrdenes->consultar(null, $value['categoriaId']);

            $categoria = $categoriaOrdenes->descripcion ?? 'Otros';

            $total = ($value["costo"] - $value["descuento"] + round($value["iva"],2)) - $value["retencionIva"] - ($value["retencionIsr"] ?? 0);

        	array_push( $registros, [
                "consecutivo" => ($consecutivo + 1),
                "id" => $value["id"],
        		"obra" => fString($value["obra"]),
        		"folio" => $folio,
        		"estatus" => fString($value["estatus.descripcion"]),
                "colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
        		"fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
                "fechaActualizacion" => fFechaLargaHora($value["fechaActualizacion"]),
                "solicito" => fString($solicito),
                "oc" => $oc = $oc ? $oc : "Sin Ordenes de Compra",
                "categoria" => fString($categoria),
                "costo" => formatMoney($total),
                "estatusOC" => $estatusOC,
                "proveedor" => $proveedor,
        		"acciones" =>  "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>".$eliminar ] );
        }
        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
    /*=============================================
	TABLA DE REQUISICIONES EN ORDENES DE COMPRA GLOBAL
	=============================================*/
	public function mostrarTablaEnOrdenCompraGlobal()
	{
		
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        $usuario->consultarPerfiles();

        $requisicion = New Requisicion;

        $arrayFiltros = array();
        $requisiciones = $requisicion->consultarFiltros($arrayFiltros);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo", "title" => "#" ]);
        array_push($columnas, [ "data" => "obra", "title" => "Obra", "width" => "300px" ]);
        array_push($columnas, [ "data" => "folio", "title" => "Folio" ]);
        array_push($columnas, [ "data" => "estatus", "title" => "Estatus" ]);
        array_push($columnas, [ "data" => "fechaRequisicion", "title" => "Fecha Requisición" ]);
        array_push($columnas, [ "data" => "proveedor", "title" => "Proveedor" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($requisiciones as $consecutivo => $value) {
        	$rutaEdit = Route::names('requisiciones.edit', $value['id']);
        	$rutaDestroy = Route::names('requisiciones.destroy', $value['id']);
            $rutaPrint = Route::names('requisiciones.print', $value['id']);
        	$folio =  mb_strtoupper(fString($value["prefijo"].'-'.$value['folio']));
            $solicito = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];

            $categoria = '';
            $requisicion->consultarCategorias();

        	array_push( $registros, [
                "consecutivo" => ($consecutivo + 1),
                "id" => $value["id"],
        		"obra" => fString($value["obra"]),
        		"folio" => $folio,
        		"estatus" => fString($value["estatus.descripcion"]),
        		"fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
                "solicito" => fString($solicito),
                "proveedor" => $value["nombreProveedor"],
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
    CONSULTAR FILTROS
    =============================================*/
    public $empresaId;
    public $ubicacionId;
    public $maquinariaId;
    public $servicioEstatusId;
    public $fechaInicial;
    public $fechaFinal;
    public $concepto;
    public $categoria;
    public $proveedor;

    public function consultarFiltros()
    {
        $arrayFiltros = array();

        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "E.id", "operador" => "=", "valor" => $this->empresaId ]);
        if ( $this->estatusId > 0 ) array_push($arrayFiltros, [ "campo" => "R.estatusId", "operador" => "=", "valor" => $this->estatusId ]);
        if ( $this->categoria > 0 ) array_push($arrayFiltros, [ "campo" => "R.categoriaId", "operador" => "=", "valor" => $this->categoria ]);
        if ( $this->obraId > 0 ) array_push($arrayFiltros, [ "campo" => "O.id", "operador" => "=", "valor" => $this->obraId ]);
        if ( $this->fechaInicial > 0 ) array_push($arrayFiltros, [ "campo" => "R.fechaCreacion", "operador" => ">=", "valor" => "'".fFechaSQL($this->fechaInicial)." 00:00:00'" ]);
        if ( $this->fechaFinal > 0 ) array_push($arrayFiltros, [ "campo" => "R.fechaCreacion", "operador" => "<=", "valor" => "'".fFechaSQL($this->fechaFinal)." 23:59:59'" ]);
        if ( $this->concepto !== '' ) array_push($arrayFiltros, [ "campo" => "lower(P.concepto)", "operador" => "like", "valor" => "'%".$this->concepto."%'" ]);

        $requisicion = New Requisicion;

        // ASIGNAR PROVEEDOR AL OBJETO REQUISICION
        if ( $this->proveedor > 0 ) $requisicion->proveedor = $this->proveedor; 

        $requisiciones = $requisicion->consultarFiltros($arrayFiltros);
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo", "title" => "#" ]);
        array_push($columnas, [ "data" => "obra", "title" => "Obra", "width" => "300px" ]);
        array_push($columnas, [ "data" => "folio", "title" => "Folio" ]);
        array_push($columnas, [ "data" => "estatus", "title" => "Estatus" ]);
        array_push($columnas, [ "data" => "fechaRequisicion", "title" => "Fecha Requisición" ]);
        array_push($columnas, [ "data" => "fechaActualizacion", "title" => "Fecha Ultima Actualizacion" ]);
        array_push($columnas, [ "data" => "costo", "title" => "Monto" ]);
        array_push($columnas, [ "data" => "oc", "title" => "Ordenes de Compra" ]);
        array_push($columnas, [ "data" => "categoria", "title" => "Categoria" ]);
        array_push($columnas, [ "data" => "estatusOC", "title" => "Estatus de la OC" ]);
        array_push($columnas, [ "data" => "proveedor", "title" => "Proveedor" ]);
        array_push($columnas, [ "data" => "solicito" , "title" => "Solicitó" ]);
        array_push($columnas, [ "data" => "acciones" , "title" => "Acciones" ]);

        $token = createToken();
        $registros = array();
        foreach ($requisiciones as $key2 => $value) {
            $rutaEdit = Route::names('requisiciones.edit', $value['id']);
            $rutaPrint = Route::names('requisiciones.print', $value['id']);
            $rutaDestroy = Route::names('requisiciones.destroy', $value['id']);
        	$folio =  mb_strtoupper(fString($value["prefijo"].'-'.$value['folio']));
            $solicito = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            if ( !is_null($value['usuarios.apellidoMaterno']) ) $solicito .= ' ' . $value['usuarios.apellidoMaterno'];
            $requisicion->id = $value["id"];

            $requisicion->consultarOrdenesCompra();

            $oc = '';
            $proveedor = ' ';
            $estatusOC = ' ';

            foreach ($requisicion->ordenes_compra as $key => $orden) {
                $oc ="OC".$orden["folio"].' | '.$oc;
                $proveedor = $orden["proveedor"].' | '.$proveedor;
                $estatusOC = $orden["estatus.descripcion"].' | '. $estatusOC;
            }

            require_once "../Models/CategoriaOrdenes.php";
            $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
            $categoriaOrdenes->consultar(null, $value['categoriaId']);

            $categoria = $categoriaOrdenes->descripcion ?? 'Otros';

            $eliminar="";
            if (Autorizacion::permiso($usuario, "requisiciones", "eliminar")) {
                $eliminar = "
                            <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='_token' value='{$token}'>
                                <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                    <i class='far fa-times-circle'></i>
                                </button>
                            </form>";
            }

            $total = ($value["costo"] - $value["descuento"] + round($value["iva"],2)) - $value["retencionIva"] - ($value["retencionIsr"] ?? 0);

        	array_push( $registros, [
                "consecutivo" => ($key2 + 1),
                "id" => $value["id"],
        		"obra" => fString($value["obra"]),
        		"folio" => $folio,
        		"estatus" => fString($value["estatus.descripcion"]),
                "colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
        		"fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
                "fechaActualizacion" => fFechaLargaHora($value["fechaActualizacion"]),
                "solicito" => fString($solicito),
                "oc" => $oc = $oc ? $oc : "Sin Ordenes de Compra",
                "categoria" => fString($categoria),
                "costo" => formatMoney($total),
                "estatusOC" => $estatusOC,
                "proveedor" => $proveedor,
        		"acciones" =>  "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>".$eliminar ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }
    /*=============================================
    VER IMÁGENES
    =============================================*/
    public $token;
    public $detalleId;

    public function verImagenes()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisiciones", "ver") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a ver Requisiciones.";

            }
        
        } else {

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

        $requisicion = New Requisicion;

        $respuesta["imagenes"] = array();

        // Consultar las imágenes
        $respuesta["imagenes"] = $requisicion->consultarImagenes($this->detalleId);

        echo json_encode($respuesta);
    }

    /*=============================================
    ELIMINAR ARCHIVO
    =============================================*/
    public $archivoId;
    public $requisicionId;

    public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisiciones-subir", "eliminar") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a eliminar Archivos.";

            }
        
        } else {

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

        // Validar existencia del campo requisicionId
        if ( !Validacion::validar("requisicionId", $this->requisicionId, ['exists', CONST_BD_APP.'.requisiciones', 'id']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "La requisición no existe.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $requisicionArchivo = New RequisicionArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $requisicionArchivo->id = $this->archivoId;
        $requisicionArchivo->requisicionId = $this->requisicionId;
        if ( !$requisicionArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $requisicionArchivo->eliminar() ) {

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
    CREAR REQUISICION
    =============================================*/
    public function crear()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisiciones", "crear") ) throw new \Exception("No está autorizado a crear reqquisiciones.");
            $request = SaveRequisicionRequest::validated($_POST["fk_IdObra"]);

        
            
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
            
            $requisicion = New Requisicion;
            
            // $detalles = $_POST['detalles'];
            $periodo = ($request['periodo']??0);
            $this->obraId = $request['fk_IdObra'];
            // // $fotos = $_POST['detalle_imagenes'];
            // //TODO || Cambiar al folio
            $folio = $request['folio'];
            
            $datosReq = [
                "folio" => $folio,
                "divisa" => $request["divisa"],
                "periodo" => $periodo,
                "fk_IdObra" => $this->obraId,
                "usuarioIdCreacion" => usuarioAutenticado()["id"],
                "fechaRequerida" => fFechaSQL($request["fechaRequerida"]),
                "tipoRequisicion" => $request["tipoRequisicion"],
                "direccion" => $request["direccion"],
                "especificaciones" => $request["especificaciones"],
                "categoriaId" => $request["categoriaId"],
                "presupuesto" => $request["presupuesto"],
                "proveedorId" => $request["proveedorId"]?? null,
                "cotizacionArchivos" => $request["cotizacionArchivos"] ?? null,
                "justificacion" => $request["justificacion"] ?? '',
            ];
            
            $puestoUsuario = new PuestoUsuario;
            $usuariosSuperIntendente = $puestoUsuario->obtenerSuperIntendente($this->obraId);

            $tarea = new Tarea;


            if ( !$requisicion->crear($datosReq) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
            
            $this->sendMailCreacion($requisicion);

            $arrayDetalles = $request["detalles"];
            $imagenes = $request["detalle_imagenes"];
            
            $datosOrdenados = array();
            foreach ($arrayDetalles["cantidad"] as $index => $value) {
                $concepto = $arrayDetalles['concepto'][$index];
                $costo = str_replace(',', '', $arrayDetalles['costo'][$index]);
                $costo_unitario = str_replace(',', '', $arrayDetalles['costo_unitario'][$index]);
                $partida = $arrayDetalles['partida'][$index];
                $obraDetalleId = $arrayDetalles['obraDetalleId'][$index];
                $unidadId = $arrayDetalles['unidadId'][$index];
                $datosOrdenados[] = array(
                    'requisicionId' => $requisicion->id,
                    'partida' => $partida,
                    'obraDetalleId' => $obraDetalleId,
                    'costo' => floatval($costo),
                    'cantidad' => str_replace(',','',$value),
                    'periodo' => $periodo,
                    'concepto' => $concepto,
                    'unidadId' => intval($unidadId),
                    'costo_unitario' => floatval($costo_unitario)
                );
            }

            foreach ($usuariosSuperIntendente as $index => $value) {
                $datos = [
                    "fk_usuario"=>$value["idUsuario"],  
                    "idRequisicion"=>$requisicion->id
                ];
                $tarea->crearTareaSuperIntendente($datos);
            }

            $partida = new Partida;

            foreach($datosOrdenados as $datos) {
                $partida->crear($datos,$imagenes);
            }

            $requisicion->consultar("id", $requisicion->id);
            
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
    AGREGAR REQUISICION
    =============================================*/
    public function agregar(){
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "insumos-indirectos", "crear") ) throw new \Exception("No está autorizado a agregar nuevos Insumos.");

            $request = SaveInsumosRequest::validated();

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

            $insumo = New Insumo;
            $response = $insumo->agregar($request);

            // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "El Insumo fue agregador correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/

    public function getLastId(){
        
        $requisicion = new Requisicion;

        $requisicionId =$requisicion->consultarId( $this->obraId);

        $LastId= 1;
        if ($requisicionId) {
            $LastId = floatval($requisicionId[0]["folio"])+1;
        }
        echo json_encode($LastId);
    }

    public function sendMailCreacion(Requisicion $requisicion)
    {
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        $obra = New Obra;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {
            $configuracionCorreoElectronico->consultarPerfilesCrear();
            $obra->consultar(null, $this->obraId);
            
            if ( $configuracionCorreoElectronico->perfilesCrear && $obra->usuariosCompras ) {
                $perfil = New Perfil;
                $perfil->consultarUsuarios($configuracionCorreoElectronico->perfilesCrear,$obra->usuariosCompras);
                
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

                $folio = mb_strtoupper($requisicion->folio);
                $liga = Route::names('requisiciones.edit', $requisicion->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>NUEVA REQUISICION</h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha sido creada la requisición {$folio}</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de una nueva requisición, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Nueva requisición {$folio}",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "Ha sido creada la requisición {$folio}, entre a la aplicación para ver el detalle de la misma.",
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

    public function actualizar(){
        try {

            $datos = array();
            foreach ($_POST as $key => $value) {
                if($key !== "accion"){
                    $row = [
                        "costo_total" => str_replace(',', '', $value),
                        "id" => $key
                    ];
                    array_push($datos,$row);
                }
            }
            $requisicion = new Requisicion;

            foreach ($datos as $key => $value) {
                $response =$requisicion->actualizarCostoFinal( $value);
            }

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Los costos fueron actualizados correctamente."
            ];
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);

    }

    public function descargarTodo()
    {
        Autorizacion::authorize('view', New Requisicion);

        $requisicion = New Requisicion;

        $respuesta = array();

        if ( $requisicion->consultar(null , $_GET["requisicionId"]) ) {

            $requisicion->consultarComprobantes();
            $requisicion->consultarOrdenes();
            $requisicion->consultarFacturas();
            $requisicion->consultarCotizaciones();
            $requisicion->consultarVales();
            $requisicion->consultarDetalles();
            $requisicion->consultarSoporte();

            $rutaRequisicion = $this->crearRequisicion($requisicion);

            // AGREGAR CONSULTA DE ORDENES DE COMPRAS SOLO SON DATOS Y PARTIDAS
            $ordenesDeCompraDatos = $requisicion->consultarOrdenesDeCompra();
            
            $rutasOrdenDeCompra = $this->crearPDFOrdenesDeCompra($ordenesDeCompraDatos, $requisicion->empresaId);

            $comprobantesPago = $requisicion->comprobantesPago;
            $ordenesCompra = $requisicion->ordenesCompra;
            $facturas = $requisicion->facturas;
            $cotizaciones = $requisicion->cotizaciones;
            $vales = $requisicion->valesAlmacen;
            $soportes = $requisicion->soportes;

            $archivos = [];

            foreach ($requisicion->comprobantesPago as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            $archivos = array_merge($archivos, $rutasOrdenDeCompra);
            $archivos[] = $rutaRequisicion;

            foreach ($requisicion->ordenesCompra as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            foreach ($requisicion->cotizaciones as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            foreach ($requisicion->facturas as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            foreach ($requisicion->soportes as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            foreach ($requisicion->soportes as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            $nombreArchivo = "requisicion_".$requisicion->obras["prefijo"]. $requisicion->folio . ".pdf";

            $rutaSalida = "/tmp/" . $nombreArchivo;

            $comando = "qpdf --empty --pages " . implode(" ", $archivos) . " -- " . $rutaSalida;
            
            // COMANDO DEV LINUX
            // $comando = "env -u LD_LIBRARY_PATH pdfunite " . implode(" ", $archivos) . " " . $rutaSalida;

            $salida = shell_exec($comando);

            if (file_exists(str_replace("'", "", $rutaSalida))) { //Verificar que el archivo se creo.

                header("Content-type:application/pdf");
                header("Content-Disposition:attachment;filename=$nombreArchivo");   
                readfile(str_replace("'", "", $rutaSalida)); //Leer el archivo y enviarlo.
                unlink(str_replace("'", "", $rutaSalida)); //Borrar el archivo temporal. ARCHIVO GENERADP
                unlink(str_replace("'", "", $rutaRequisicion)); //Borrar el archivo temporal REQUISICION

                foreach ($rutasOrdenDeCompra as $ruta) {
                    $rutaLimpia = str_replace("'", "", $ruta); 
                    if (file_exists($rutaLimpia)) {
                        unlink($rutaLimpia); // Eliminar rutas de las ordenes de compra
                    }
                }
                
                exit();
            } else {
                echo "Error al fusionar los archivos PDF.";
            }   


            $respuesta = array( 
                                'error' => false,
                                'rutas' => 'reportes/tmp/merged.pdf' ,
                            );
                exit();
                            

        } else {
            // Si hubo un error al generar el archivo
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Error al fusionar los archivos PDF.',
            );
        }        

        echo json_encode($respuesta);
    }

    public function mostrarExistencias()
    {
        try {
            
            $requisicion = new Requisicion;
            $requisicion->id = $_GET["requisicionId"];
            $requisicion->consultarDetalles();
            
            $indirectos = [];
            $directos = [];
            $partidas = [];
            // Se obtienen los id de los detalles de la requisición
            foreach ($requisicion->detalles as $detalle) {
                if ($detalle['indirecto.id'] !== null) {
                    $indirectos[] = $detalle['indirecto.id'];
                    $partidas["indirectos"][$detalle['indirecto.id']] = $detalle['id'];
                } else {
                    $directos[] = $detalle['insumo.id'];
                    $partidas["directos"][$detalle['insumo.id']] = $detalle['id'];
                }
                $partidas["cantidad"][$detalle['id']] = $detalle['cantidad']-$detalle['cantidadEntrada'];;
            }

            $columnas = array();
            array_push($columnas, [ "data" => "consecutivo" ]);
            array_push($columnas, [ "data" => "concepto" ]);
            array_push($columnas, [ "data" => "cantidad" ]);
            array_push($columnas, [ "data" => "existencia" ]);
            array_push($columnas, [ "data" => "unidad" ]);
            array_push($columnas, [ "data" => "almacen" ]);

            $registros = array();
            $inventario = new InventarioDetalles;

            $data = $inventario->consultarExistencias(implode(',',$indirectos), implode(',',$directos));

            foreach ($data as $key => $value) {
                $partida = $partidas["indirectos"][$value["indirecto"]] ?? $partidas["directos"][$value["directo"]];
                $cantidadPartidasRestante = $partidas["cantidad"][$partida];
                $cantidadDisponible = $value["cantidad"] - $value["cantidadSalidas"] - $value["cantidadTraslados"];

                $cantidad = $cantidadPartidasRestante < $cantidadDisponible ? $cantidadPartidasRestante : $cantidadDisponible;
                $cantidadLimite = $cantidadPartidasRestante < $cantidadDisponible ? $cantidadPartidasRestante : $cantidadDisponible;

                if ($cantidadPartidasRestante == 0) continue;
                array_push($registros, [
                    "id" => $value["id"],
                    "partidaTraslado" => $value["id"],
                    "consecutivo" => ($key + 1),
                    "concepto" => $value["descripcion"],
                    "cantidad" => $cantidad ,
                    "cantidad_disponible" => $cantidadLimite ,
                    "existencia" => $cantidadDisponible,
                    "unidad" => $value["unidad.descripcion"],
                    "almacen" => $value["almacen.descripcion"],
                    'almacenId' => $value['almacen.id'],
                    'numeroParte' => $value['numeroParte'],
                    'indirecto' => $value['indirecto'],
                    'directo' => $value['directo'],
                    "partida" => $partida
                ]);
            }


            $existencias = array();

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos']['columnas'] = $columnas;
            $respuesta['datos']['registros'] = $registros;

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    function crearPDFOrdenesDeCompra($datos, $empresa = 1)
    {
        if ( $empresa == 2 ) include "../../reportes/pdfPrueba.php";
        else  include "../../reportes/ordencompra_hecaConjunto.php";
        
        // Llamar a la función
        $rutasArchivos = generarPDFOrdenes($datos);

        return $rutasArchivos;
    }

    function crearRequisicion($requisicion)
    {

        $requisicion->consultarDetalles();

        require_once "../Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedor->consultar(null, $requisicion->proveedorId);

        require_once "../Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null, $requisicion->empresaId);

        require_once "../Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obra->consultar(null, $requisicion->idObra);

        require_once "../Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $requisicion->usuarioIdCreacion);

        require_once "../Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisa->consultar(null, $requisicion->divisa);

        $usuarioNombre = mb_strtoupper($usuario->nombre);
        $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
        if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
        $solicitoFirma = $usuario->firma;
        unset($usuario);

        $reviso = '';
        $revisoFirma = null;
        
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $requisicion->usuarioIdAutorizacion);

        $reviso = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
        if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
        $revisoFirma = $usuario->firma;
        unset($usuario);

        $almacenResponsable = '';
        $almacenFirma = null;

        if ( !is_null($requisicion->usuarioIdAlmacen) ) {
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdAlmacen);

            $almacenResponsable = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $almacenResponsable .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $almacenFirma = $usuario->firma;
            unset($usuario);
        }

        $autorizoAdicional = '';
        $firmaAutorizoAdicional = null;

        if ( !is_null($requisicion->usuarioIdAutorizacionAdd)) {
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdAutorizacionAdd);

            $autorizoAdicional = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $autorizoAdicional .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $firmaAutorizoAdicional = $usuario->firma;
            unset($usuario);
        } 

        if ( $requisicion->empresaId == 3) include "../../reportes/requisicion_hecaConjunto.php";
        else include "../../reportes/requisicionConjunto.php";

        return $rutasArchivos;
    }

    public function solicitarFacturas()
    {
        try {
            $requisicion = new Requisicion;
            
            $requisicion->consultar(null, $_POST["requisicionId"]);

            $proveedor = new Proveedor;
            $proveedor->consultar(null, $requisicion->proveedorId);

            $this->sendMailFacturas($requisicion, $proveedor->correo);

            $respuesta = [
                'error' => false,
                'respuesta' => $proveedor->correo,
                'respuestaMessage' => "Se ha solicitado la factura al proveedor."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    public function sendMailFacturas(Requisicion $requisicion, $correo)
    {
        $obra = New Obra;
        $obra->consultar(null, $requisicion->idObra);

        $empresa = new Empresa;
        $empresa->consultar(null, $obra->empresaId);

        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        $configuracionCorreoElectronico->consultar(null , 1);

        $oc = $_POST["ordenCompra"];
        if ( $correo !== null ) {
            
                $mensaje = New Mensaje;

                $correoProveedor = new correoProveedor;
                $correoProveedor->requisicionId = $requisicion->id;
                $correoProveedor->empresaId = $empresa->id;
                $correoProveedor->ordenCompra = $oc;
                $correoProveedor->correo = $correo;
                if(!$correoProveedor->crear()) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
                
                $liga = Route::names('requisiciones.upload', $correoProveedor->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>PAGO</h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha recibido un pago de parte de {$empresa->nombreCorto} correspondiente a la OC. {$oc}. Favor de adjuntar la factura en PDF y XML</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para subir los soportes</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Pago de Orden de Compra",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "Ha recibido un pago de parte de {$empresa->nombreCorto} correspondiente a la OC. {$oc}. Favor de adjuntar la factura en PDF y XML",
                           "liga" => $liga,
                           "destinatarios" => $correo
                ];

                if ( $mensaje->crear($datos) ) {
                    $mensaje->consultar(null , $mensaje->id);
                    $mensaje->mensajeHTML = $mensajeHTML;

                    $enviar = MailController::sendProveedor($mensaje,false,$correo);
                    if ( $enviar["error"] ) $mensaje->noEnviado([ "error" => $enviar["errorMessage"] ]);
                    else $mensaje->enviado();
                }

        }     
    }

    public function subirFacturas()
    {
        try {

            $requisicion = new Requisicion;
            $requisicion->id = $_POST["requisicion"];

            $requisicion->insertarArchivos($_FILES["facturaArchivosPDF"],3,'../../');
            $requisicion->insertarArchivos($_FILES["facturaArchivosXML"],3,'../../');

            $correoProveedor = new correoProveedor;
            $correoProveedor->id = $_POST["correoProveedor"];
            $correoProveedor->actualizar();

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "La factura fue subida correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    public function obtenerOrdenesCompra()
    {
        try {

            $requisicion = new Requisicion;
            $requisicion->id = $_POST["requisicionId"];
            $response = $requisicion->consultarOrdenesDeCompra();

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Las órdenes de compra fueron obtenidas correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    public function verArchivos()
    {
        try {

            $requisicion = new Requisicion;

            $respuesta = array();

            if ( $requisicion->consultar(null , $_GET["requisicionId"]) ) {

                $requisicion->consultarComprobantes();
                $requisicion->consultarOrdenes();
                $requisicion->consultarFacturas();
                $requisicion->consultarCotizaciones();
                $requisicion->consultarVales();
                $requisicion->consultarDetalles();
                $requisicion->consultarSoporte();

                $rutaRequisicion = $this->crearRequisicion($requisicion);

                // AGREGAR CONSULTA DE ORDENES DE COMPRAS SOLO SON DATOS Y PARTIDAS
                $ordenesDeCompraDatos = $requisicion->consultarOrdenesDeCompra();

                $rutasOrdenDeCompra = $this->crearPDFOrdenesDeCompra($ordenesDeCompraDatos, $requisicion->empresaId);

                $comprobantesPago = $requisicion->comprobantesPago;
                $ordenesCompra = $requisicion->ordenesCompra;
                $facturas = $requisicion->facturas;
                $cotizaciones = $requisicion->cotizaciones;
                $vales = $requisicion->valesAlmacen;
                $soportes = $requisicion->soportes;

                $archivos = [];
                $temp_dir = '/tmp/processed_pdfs/';

                // Asegúrate de que el directorio temporal exista y sea escribible
                if (!is_dir($temp_dir)) {
                    mkdir($temp_dir, 0777, true);
                }

                foreach ($requisicion->comprobantesPago as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                $archivos = array_merge($archivos, $rutasOrdenDeCompra);
                $archivos[] = $rutaRequisicion;

                foreach ($requisicion->ordenesCompra as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                foreach ($requisicion->cotizaciones as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                foreach ($requisicion->facturas as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }
                
                foreach ($requisicion->soportes as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                $nombreArchivo = "requisicion_".$requisicion->obras["prefijo"]. $requisicion->folio . ".pdf";

                $rutaSalida = "/tmp/" . $nombreArchivo;

                // Verificar si ya existe el archivo y eliminarlo
                if (file_exists($rutaSalida)) {
                    unlink($rutaSalida);
                }

                $comando = "qpdf --empty --pages " . implode(" ", $archivos) . " -- " . $rutaSalida;
                // COMANDO DEV LINUX
                
                $salida = shell_exec($comando);
                // Mover el archivo a la carpeta deseada después de crearlo
                $rutaDestino = __DIR__ . "/../../reportes/requisiciones/" . $nombreArchivo;
                if (file_exists(str_replace("'", "", $rutaSalida))) {
                    // Crear el directorio si no existe
                    if (!is_dir(dirname($rutaDestino))) {
                        mkdir(dirname($rutaDestino), 0777, true);
                    }
                    // Mover el archivo generado al destino
                    rename(str_replace("'", "", $rutaSalida), $rutaDestino);
                }

                if (file_exists(str_replace("'", "", $rutaDestino))) { //Verificar que el archivo se creo.

                    unlink(str_replace("'", "", $rutaRequisicion)); //Borrar el archivo temporal REQUISICION
                    
                } else {
                    echo "Error al fusionar los archivos PDF.";
                }

                $respuesta = array( 
                                    'error' => false,
                                    'ruta' => '/reportes/requisiciones/'.$nombreArchivo ,
                                );

                echo json_encode($respuesta);


            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

    }

    /* 
    OBTENER ORDENES DE COMPRA DE LA REQUISICIÓN PARA 
    TABLA REQUISICIONES 
    */
    public function ordenCompraRequisicion(){
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $requisicion = New Requisicion;
            $ordenesCompra = $requisicion->obtenerOrdenCompraRequisicion($this->requisicionId);

            $columnas = array();
			array_push($columnas, [ "data" => "folio" ]);
			array_push($columnas, [ "data" => "tiempoEntrega" ]);
			array_push($columnas, [ "data" => "nombreEstatus" ]);

			$registros = array();
			foreach ($ordenesCompra as $key => $value) {

			array_push( $registros, [ 
										"folio" => $value["folio"],
										"tiempoEntrega" => $value["tiempoEntrega"],
										"nombreEstatus" => $value["nombreEstatus"],
										]);
			}

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos']['columnas'] = $columnas;
            $respuesta['datos']['registros'] = $registros;


        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    /* 
    OBTENER PARTIDAS POR REQUISICION
    */
    public function partidaPorRequisicion(){
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");
 
            $partida = New Partida;
            $partidas = $partida->consultarPartidaPorRequisicion($this->requisicionId);

            $columnas = array();
			array_push($columnas, [ "data" => "requisicionId" ]);
			array_push($columnas, [ "data" => "cantidad" ]);
			array_push($columnas, [ "data" => "descripcion" ]);
			array_push($columnas, [ "data" => "valorUnitario" ]);
			array_push($columnas, [ "data" => "importe" ]);

			$registros = array();
			foreach ($partidas as $key => $value) {

			array_push( $registros, [ 
										"requisicionId" => $value["requisicionId"],
                                        "partidaId" => $value["id"],
										"cantidad" => $value["cantidad"],
										"descripcion" => $value["concepto"],
										"valorUnitario" => $value["costo_unitario"],
										"importe" => $value["costo_unitario"],
										]);
			}


            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;

            $respuesta['datos']['columnas'] = $columnas;
            $respuesta['datos']['registros'] = $registros;


        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

      public function enviarNotaInformativa()
    {
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");
            $request = SaveNotaInformativaRequest::validated();

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

            $NotaInformativa = New NotaInformativa;
            if (!$NotaInformativa->crear($request)) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "La nota informativa fue enviada correctamente.";

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }
    
    public function autorizarRequisicion()
    {
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $requisicion = New Requisicion;
            $requisicion->id = $_POST["requisicionId"];
            $requisicion->consultar(null, $requisicion->id);

            if ( !$requisicion->autorizar() ) throw new \Exception("Hubo un error al intentar autorizar la requisición, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "La requisición fue autorizada correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    function processPdfWithGhostscript($input_path, $output_dir) {
        $input_filename = basename($input_path);
        $output_filename = 'gs_processed_' . uniqid() . '_' . $input_filename; // Nombre único para evitar colisiones
        $output_path = $output_dir . $output_filename;

        // Asegurarse de que las rutas estén correctamente escapadas para el shell
        $escaped_input_path = escapeshellarg($input_path);
        $escaped_output_path = escapeshellarg($output_path);

        // Comando de Ghostscript para "aplanar" el PDF y eliminar restricciones
        $command = "gs -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile={$escaped_output_path} {$escaped_input_path} 2>&1";
        
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        if ($return_var === 0) {
            // Retorna la ruta del archivo procesado si fue exitoso
            return $output_path;
        } else {
            // Loguea el error o maneja la excepción
            error_log("Error al procesar PDF con Ghostscript: " . implode("\n", $output));
            // Si Ghostscript falla, intenta usar el archivo original, aunque puede fallar la unión
            return $input_path; 
        }
    }

        
    public function cambiarObra()
    {
    try {

        if (!usuarioAutenticado()) {
            throw new \Exception("Usuario no Autenticado, intente de nuevo.");
        }

        if (!$this->obraId) {
            throw new \Exception("La obra es requerida");
        }

        $requisicion = new Requisicion;
        $requisicion->id = $this->requisicionId;
        $requisicion->obraId = $this->obraId;
        $requisicion->cambiarObra();

        $respuesta = [
            'error' => false,
            'respuesta' => true,
            'respuestaMessage' => "El cambio de obra en la requisición fue exitoso."
        ];

    } catch (\Exception $e) {
        $respuesta = [
            'error' => true,
            'errorMessage' => $e->getMessage()
        ];
    }

    echo json_encode($respuesta);
    }

    public function eliminarDetalle()
    {
        try {

            if (!usuarioAutenticado()) {
                throw new \Exception("Usuario no Autenticado, intente de nuevo.");
            }

            if (!$this->detalleId) {
                throw new \Exception("El detalle es requerido");
            }

            $requisicion = new Requisicion;
            $requisicion->id = $this->requisicionId;
            $requisicion->detalleId = $this->detalleId;
            $requisicion->eliminarDetalle();

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "El detalle fue eliminado correctamente."
            ];

        } catch (\Exception $e) {
            $respuesta = [
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }

        echo json_encode($respuesta);
    }
}

try {

    $requisicionAjax = New RequisiconAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "verImagenes" && isset($_POST["detalleId"]) ) {

            /*=============================================
            VER IMÁGENES
            =============================================*/
            $requisicionAjax->token = $_POST["_token"];
            $requisicionAjax->detalleId = $_POST["detalleId"];
            $requisicionAjax->verImagenes();
    
        } elseif ( $_POST["accion"] == "crear" ) {

            /*=============================================
            CREAR REQUISICION
            =============================================*/
            $requisicionAjax->crear();

        } else if( $_POST["accion"] == "agregar" ){
            /*=============================================
            AGREGAR REQUISICION
            =============================================*/
            $requisicionAjax->agregar();
        } else if($_POST["accion"] == "eliminarArchivo"){
             /*=============================================
            ELIMINAR ARCHIVO
            =============================================*/
            $requisicionAjax->token = $_POST["_token"];
            $requisicionAjax->archivoId = $_POST["archivoId"];
            $requisicionAjax->requisicionId = $_POST["requisicionId"];
            $requisicionAjax->eliminarArchivo();
        } else if($_POST["accion"] == "actualizar"){
            /*=============================================
            AGREGAR REQUISICION
            =============================================*/
            $requisicionAjax->actualizar();
        } else if($_POST["accion"] == "solicitarFacturas"){
            /*=============================================
            MANDA CORREO Y SOLICITA FACTURAS AL PROVEEDOR
            =============================================*/
            $requisicionAjax->solicitarFacturas();
        } else if($_POST["accion"] == "subirFacturas"){
            /*=============================================
            SUBE LAS FACTURAS
            =============================================*/
            $requisicionAjax->subirFacturas();
        } else if($_POST["accion"] == "obtenerOrdenesCompra"){
            /*=============================================
            OBTENER ORDENES DE COMPRA
            =============================================*/
            $requisicionAjax->requisicionId = $_POST["requisicionId"];
            $requisicionAjax->obtenerOrdenesCompra();

        } elseif ($_POST["accion"] == "enviarNotaInformativa"){
            /*=============================================
            
            =============================================*/
            $requisicionAjax->token = $_POST["_token"];
            $requisicionAjax->requisicionId = $_POST["requisicionId"];
            $requisicionAjax->enviarNotaInformativa();

        } elseif ($_POST["accion"] == "autorizarRequisicion"){
            /*=============================================
            AUTORIZAR REQUISICION
            =============================================*/
            $requisicionAjax->token = $_POST["_token"];
            $requisicionAjax->requisicionId = $_POST["requisicionId"];
            $requisicionAjax->autorizarRequisicion();

        } elseif ($_POST["accion"] == "cambiarObra"){
            /*=============================================
            CAMBIAR OBRA
            =============================================*/
            $requisicionAjax->token = $_POST["_token"];
            $requisicionAjax->requisicionId = $_POST["requisicionId"];
            $requisicionAjax->obraId = $_POST["obraId"];

            $requisicionAjax->cambiarObra();

        } elseif ($_POST["accion"] == "eliminarDetalle") {
            /*=============================================
            ELIMINAR DETALLE
            =============================================*/
            $requisicionAjax->token = $_POST["_token"];
            $requisicionAjax->detalleId = $_POST["detalleId"];
            $requisicionAjax->eliminarDetalle();
        } else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }

    } elseif ( isset($_GET["accion"]) ) {

        if ( $_GET["accion"] == "verArchivos"  ) {
            /*=============================================
            VER ARCHIVOS
            =============================================*/
            $requisicionAjax->requisicionId = $_GET["requisicionId"];
            $requisicionAjax->verArchivos();

        }else if ( $_GET["accion"] == "tablaRequisicionOrdenCompraGlobal") {
            /*=============================================
            TABLA DE REQUISICIONES EN ORDEN DE COMPRA GLOBALES
            =============================================*/
            $requisicionAjax->mostrarTablaEnOrdenCompraGlobal();
        }
        else if ( $_GET["accion"] == "partidaPorRequisicion") {
            /*=============================================
            TABLA DE PARTIDAS DE LAS REQUISICIONES
            =============================================*/
            $requisicionAjax->requisicionId = $_GET["requisicionId"];
            $requisicionAjax->partidaPorRequisicion();
        }
        else{

            /*=============================================
            TABLA DE REQUISICIONES
            =============================================*/
            $requisicionAjax->mostrarExistencias();   
        }
        
    } elseif (isset($_GET["empresaId"])) {
        /*=============================================
        CONSULTAR FILTROS
        =============================================*/

        $requisicionAjax->empresaId = $_GET["empresaId"];
        $requisicionAjax->estatusId = $_GET["estatusId"];
        $requisicionAjax->fechaInicial = $_GET["fechaInicial"];
        $requisicionAjax->obraId = $_GET["obraId"];
        $requisicionAjax->fechaFinal = $_GET["fechaFinal"];
        $requisicionAjax->concepto = $_GET["concepto"];
        $requisicionAjax->categoria = $_GET["categoria"];
        $requisicionAjax->proveedor = $_GET["proveedor"];

        $requisicionAjax->consultarFiltros();

    } elseif ( isset($_GET["obraId"]) ) {

        /*=============================================
        OBTIENE ULTIMO ID
        =============================================*/
        $requisicionAjax->obraId = $_GET["obraId"];
        $requisicionAjax->getLastId();

    } elseif ( isset($_GET["requisicionId"]) ){
        /*=============================================
        DESCARGA TODOS LOS ARCHIVOS EN CONJUNTO
        =============================================*/
        $requisicionAjax->descargarTodo();
    } elseif ( isset($_GET["ordenCompra"]) ){
        /*=============================================
        OBTENER ORDENES DE COMPRA DE LA REQUISICION
        =============================================*/
        $requisicionAjax->requisicionId = $_GET["ordenCompra"];
        $requisicionAjax->ordenCompraRequisicion();
    } elseif ( isset($_GET["partidasRequisicion"]) ){
        /*=============================================
        OBTENER PARTIDAS DE LA REQUISICION
        =============================================*/
        $requisicionAjax->requisicionId = $_GET["partidasRequisicion"];
        $requisicionAjax->ordenCompraRequisicion();
    } else {

        /*=============================================
        TABLA DE REQUISICIONES
        =============================================*/
        $requisicionAjax->mostrarTabla();

    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
