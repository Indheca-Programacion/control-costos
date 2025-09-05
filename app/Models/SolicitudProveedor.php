<?php

namespace App\Models;

if ( file_exists ( "app/Policies/SolicitudProveedorPolicy.php" ) ) {
    require_once "app/Policies/SolicitudProveedorPolicy.php";
} else {
    require_once "../Policies/SolicitudProveedorPolicy.php";
}

use App\Conexion;
use PDO;
use App\Route;

class SolicitudProveedor extends SolicitudProveedorPolicy
{
    static protected $fillable = [
        'razonSocial', 'rfc', 'correoElectronico', 'nombreApellido', 'telefono','origenProveedor','tipoProveedor','claveProveedor','entregaMaterial','diasCredito','terminosCondiciones','constanciaFiscal','opinionCumplimiento','comprobanteDomicilio','datosBancarios','estatusSolicitudProveedorId','detalles'
    ];

    static protected $type = [
        'id' => 'integer',

        'razonSocial' => 'string',
        'rfc' => 'string',
        'correoElectronico' => 'string|email',
        'nombreApellido' => 'string',
        'telefono' => 'string',

        'estatusSolicitudProveedorId' => 'integer', 

        'origenProveedor' => 'string',
        'tipoProveedor' => 'string',
        'claveProveedor' => 'string',
        'entregaMaterial' => 'string',

        'diasCredito' => 'integer',
        'terminosCondiciones' => 'string',
        
        'constanciaFiscal' => 'string',
        'opinionCumplimiento' => 'string',
        'comprobanteDomicilio' => 'string',
        'datosBancarios' => 'string',

        // DATOS TABLA OBSERVACIONES_SOLICITUD_PROVEEDOR
        'observacion' => 'string',
        'usuarioIdCreacion' => 'string',
        'solicitudProveedorId' => 'integer',
        'categoriaId' => 'string',
        'tipoObservacion' => 'string',
        'solicitudProveedorArchivoId' => 'integer',

        //DATOS SOLICITUD ARCHIVOS
        'ruta' => 'string',

    ];


    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "solicitud_proveedor";

    protected $keyName = "id";

    public $id = null;

    public $categoriaArchivos = [
        "constanciaFiscal",
        "opinionCumplimiento",
        "comprobanteDomicilio",
        "datosBancarios"
    ];

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR SOLICITUDES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll(
                $this->bdName, 
                "SELECT 
                    SP.*,
                    ESP.descripcion AS 'esp.descripcion'
                FROM 
                    $this->tableName SP
                INNER JOIN estatus_solicitud_proveedor ESP 
                    ON ESP.id = SP.estatusSolicitudProveedorId
                ORDER BY SP.created_at DESC
                ", 
                $error
            );

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                        SP.* ,
                                                                        ESP.descripcion AS 'esp.descripcion'
                                                                    FROM 
                                                                        $this->tableName SP
                                                                    INNER JOIN estatus_solicitud_proveedor ESP ON ESP.id = SP.estatusSolicitudProveedorId
                                                                    WHERE SP.$this->keyName = $valor"
                                                                    , $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                    SP.*,
                                                                    ESP.descripcion AS 'esp.descripcion'
                                                                FROM 
                                                                    $this->tableName SP
                                                                INNER JOIN estatus_solicitud_proveedor ESP ON ESP.id = SP.estatusSolicitudProveedorId
                                                                WHERE SP.$item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->rfc = $respuesta["rfc"];
                $this->razonSocial = $respuesta["razonSocial"];
                $this->correoElectronico = $respuesta["correoElectronico"];
                $this->nombreApellido = $respuesta["nombreApellido"];
                $this->telefono = $respuesta["telefono"];
                $this->origenProveedor = $respuesta["origenProveedor"];
                $this->tipoProveedor = $respuesta["tipoProveedor"];
                $this->claveProveedor = $respuesta["claveProveedor"];
                $this->entregaMaterial = $respuesta["entregaMaterial"];
                $this->diasCredito = $respuesta["diasCredito"];
                $this->terminosCondiciones = $respuesta["terminosCondiciones"];
                $this->estatusSolicitudProveedor = $respuesta["esp.descripcion"];
                $this->updated_at = $respuesta["updated_at"];
            }

            return $respuesta;

        }

    }
    
    /*=============================================
    CONSULTAR ARCHIVOS CONSTANCIA SITUACION FISCAL
    =============================================*/
    public function consultarConstanciaFiscal() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM solicitud_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 18 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->constancia_fiscal = $resultado;
    }

    /*=============================================
    CONSULTAR ARCHIVOS CONSULTAR OPINION DE CUMPLIMIENTO
    =============================================*/
    public function consultarOpinionCumplimiento() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM solicitud_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 19 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->opinion_cumplimiento = $resultado;
    }

    /*=============================================
    CONSULTAR ARCHIVOS COMPROBANTE DE DOMICILIO
    =============================================*/
    public function consultarComprobanteDomicilio() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM solicitud_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 20 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->comprobante_domicilio = $resultado;
    }

    /*=============================================
    CONSULTAR ARCHIVOS DATOS BANCARIOS
    =============================================*/
    public function consultarDatosBancarios() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM solicitud_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 21 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->datos_bancarios = $resultado;
    }

    /*=============================================
    VERIFICAR SI HAY ARCHIVOS RECHAZADO
    =============================================*/
    public function consultarArchivosRechazados() {
        
        return  Conexion::queryAll($this->bdName, "SELECT 
                                                        FPA.id,
                                                        FPA.formularioProveedorId,
                                                        CASE FPA.tipo
                                                            WHEN 18 THEN 'CONSTANCIA FISCAL'
                                                            WHEN 19 THEN 'OPINIÓN DE CUMPLIMIENTO'
                                                            WHEN 20 THEN 'COMPROBANTE DE DOMICILIO'
                                                            WHEN 21 THEN 'DATOS BANCARIOS'
                                                            ELSE 'SIN TIPO'
                                                        END AS tipo_descripcion,
                                                        FPA.categoriaId,
                                                        FPA.titulo,
                                                        FPA.archivo,
                                                        OSP.observacion
                                                    FROM 
                                                        solicitud_proveedor_archivos FPA 
                                                    INNER JOIN 
                                                        observaciones_solicitud_proveedor OSP 
                                                    ON 
                                                        OSP.solicitudProveedorArchivoId = FPA.id
                                                    WHERE 
                                                        FPA.formularioProveedorId = $this->id 
                                                    AND 
                                                        FPA.categoriaId = 'ARCHIVO RECHAZADO'
                                                    AND 
                                                        FPA.eliminado = 1 
                                                    ORDER BY FPA.id;", $error);;
    }

    /*=============================================
    VERIFICAR SI HAY ARCHIVOS QUE FALTAN CAMBIAR ESTATUS
    @params categoriaIsNull boolean VALIDA SI AL LLAMAR A LA FUNCION
    LE PASAN PARAMETROS PARA AGREGAR LA CONDICION CATEGORIA 

    return 
        Con Condicion (true/false) 
        Sin Condicion (Array) ARREGLO DE ARCHIVOS DE LA SOLICITUD
    =============================================*/
    public function consultarArchivosActualizados($categoriaIsNull = false) {
        $condicionCategoria = $categoriaIsNull ? "AND FPA.categoriaId IS NULL" : "";

        $sql = "SELECT 
                    FPA.*,
                    OSP.observacion AS detalleObservacion,
                    OSP.categoriaId AS categoriaIdObservacion,
                    OSP.tipoObservacion AS tipoObservacion
                FROM 
                    solicitud_proveedor_archivos FPA 
                LEFT JOIN 
                    observaciones_solicitud_proveedor OSP 
                ON 
                    OSP.solicitudProveedorArchivoId = FPA.id
                WHERE 
                    FPA.formularioProveedorId = $this->id 
                    $condicionCategoria
                AND 
                    FPA.eliminado = 1 
                ORDER BY 
                    FPA.id";

        return Conexion::queryAll($this->bdName, $sql, $error);
    }

    /*=============================================
    CREAR SOLICITUD
    =============================================*/
    public function crear($datos) {

            $arrayPDOParam = array();
            $arrayPDOParam["razonSocial"] = self::$type["razonSocial"];
            $arrayPDOParam["rfc"] = self::$type["rfc"];
            $arrayPDOParam["correoElectronico"] = self::$type["correoElectronico"];
            $arrayPDOParam["nombreApellido"] = self::$type["nombreApellido"];
            $arrayPDOParam["telefono"] = self::$type["telefono"];
            $arrayPDOParam["origenProveedor"] = self::$type["origenProveedor"];
            $arrayPDOParam["tipoProveedor"] = self::$type["tipoProveedor"];
            $arrayPDOParam["claveProveedor"] = self::$type["claveProveedor"];
            $arrayPDOParam["entregaMaterial"] = self::$type["entregaMaterial"];
            $arrayPDOParam["diasCredito"] = self::$type["diasCredito"];
            $arrayPDOParam["terminosCondiciones"] = self::$type["terminosCondiciones"];
            $arrayPDOParam["estatusSolicitudProveedorId"] = self::$type["estatusSolicitudProveedorId"];

            // ESTATUS SOLICITUD POR DEFECTO (REVISION : 20)
            $datos["estatusSolicitudProveedorId"] = 20;
            $datos["correoElectronico"] = strtolower($datos["correoElectronico"]);

            $campos = fCreaCamposInsert($arrayPDOParam);


            $lastId = 0;
            $respuesta = Conexion::queryExecute(
                $this->bdName,
                "INSERT INTO $this->tableName " . 
                $campos,
                $datos,
                $arrayPDOParam,
                $error,
                $lastId
            ); 

            if($respuesta){
                
                $this->id = $lastId;

                // INSERTAR ARCHIVOS A LA BASE DE DATOS 
                $respuesta = $this->archivoInsertar($datos);

                if($respuesta){
                    // BORRAR ARCHIVOS TEMPORALES
                    foreach ($_SESSION['archivos_anteriores'] as $key => $archivosAnteriores) {
                        foreach ($archivosAnteriores as $key => $item) {
                            $respuesta = eliminarArchivos($item["ruta"]);
                        }
                    }
                }
            }

            return $respuesta;
    }

    /*=============================================
    MANDAR EL TIPO DE ARCHIVO Y LOS DATOS SEGUN SU CATEGORIA
    =============================================*/
    public function archivoInsertar($datos){

        foreach ($this->categoriaArchivos as $key => $value) {
            if ( isset($datos[$value]) && $datos[$value]['name'][0] != '' ) {
                $respuesta = $this->insertarArchivos($datos[$value], $key+18);
            }
        }

        return $respuesta;
    }

    // DIRECTORIOS PARA LOS ARCHIVOS
    public function directorioArchivo($tipoArchivo){
        // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
        if ( $tipoArchivo == 18 ) $directorio =  "vistas/uploaded-files/formulario-proveedor/constancia-fiscal/";
        elseif ( $tipoArchivo == 19 ) $directorio = "vistas/uploaded-files/formulario-proveedor/opinion-cumplimiento/";
        elseif ( $tipoArchivo == 20 ) $directorio = "vistas/uploaded-files/formulario-proveedor/comprobante-domicilio/";
        elseif ( $tipoArchivo == 21 ) $directorio = "vistas/uploaded-files/formulario-proveedor/datos-bancarios/";
        else $directorio = "vistas/uploaded-files/formulario-proveedor/";

        return $directorio;
    }

    // INSERTAR ARCHIVOS FORMULARIO PROVEEDORES
    function insertarArchivos($archivos, $tipoArchivo) {
        // VALIDAR EXISTENCIA SI EXISTE LA RUTA ORIGINAL
        if ( $archivos["rutaOriginal"] != "" ) {

            $directorio = $this->directorioArchivo($tipoArchivo);

            // Crear el directorio si no existe
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
        
            $ruta = $directorio.$archivos["nombreGenerado"];

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["formularioProveedorId"] = $this->id;
            $insertar["tipo"] = $tipoArchivo; 
            $insertar["titulo"] = $archivos["name"];
            $insertar["archivo"] = $archivos["nombreGenerado"];
            $insertar["formato"] = "application/pdf";
            $insertar["ruta"] = $ruta;

            $arrayPDOParam = array();        
            $arrayPDOParam["formularioProveedorId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO solicitud_proveedor_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if($respuesta){
                // RUTA DESTINO
                $directorioDestino = $_SERVER['DOCUMENT_ROOT']. CONST_APP_FOLDER .$ruta;
                // MOVER EL ARCHIVO TEMPORAL A LAS CARPETAS
                try {
                    moverArchivos($archivos["rutaOriginal"],$directorioDestino);
                } catch (\Exception $e) {
                    echo "❗ Excepción capturada: " . $e->getMessage();
                }

            }
        }
        return $respuesta;
    }

    public function actualizar($datos) {
        
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        
        if(isset($datos["descripcion"])) $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        if(isset($datos["fk_usuario"])) $arrayPDOParam["fk_usuario"] = self::$type["fk_usuario"];
        if(isset($datos["fecha_inicio"])) {
            $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
            $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        }
        if(isset($datos["fecha_limite"])) {
            $arrayPDOParam["fecha_limite"] = self::$type["fecha_limite"];
            $datos["fecha_limite"] = fFechaSQL($datos["fecha_limite"]);

        } 
        
        if(isset($datos["estatus"])){
            $arrayPDOParam["estatus"] = self::$type["estatus"];
            if($datos["estatus"]==10){
                $fecha_actual = date("Y-m-d H:i:s");
                $datos["fecha_finalizacion"] = $fecha_actual;
                $arrayPDOParam["fecha_finalizacion"] = self::$type["fecha_finalizacion"];
            }
        }
        
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);


        if ( $respuesta ) {
            $arrayPDOParam["estatus"] = self::$type["estatus"];
        }

        return $respuesta;

    }

    /*=============================================
	AUTORIZAR SOLICITUD DEL PROVEEDOR
	=============================================*/
    public function autorizarSolicitudProveedor() {

        //TODO VERIFICACION ESTATUS EN LOS ARCHIVOS
        $archivosRechazados = $this->consultarArchivosRechazados();
            
        $datos = [];
        $datos[$this->keyName] = $this->id;
        
        if (empty($archivosRechazados)) {
            $datos["estatusSolicitudProveedorId"] = 21; //AUTORIZADO
        } else {
            $datos["estatusSolicitudProveedorId"] = 23; //PENDIENTE ARCHIVOS
        }

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatusSolicitudProveedorId"] = self::$type["estatusSolicitudProveedorId"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);

        if($respuesta){

            if( $datos["estatusSolicitudProveedorId"] == 21){
                $this->categoriaObservacion = "AUTORIZADO";
                    if (empty($this->observacionSolicitudProveedor)) {
                        $this->observacionSolicitudProveedor = "SOLICITUD AUTORIZADO";
                    }
            }
            else{
                $this->categoriaObservacion = "PENDIENTE ARCHIVOS";
                    if (empty($this->observacionSolicitudProveedor)) {
                        $this->observacionSolicitudProveedor = "SOLICITUD PENDIENTE";
                    }
            }
            $this->tipoObservacion = "SOLICITUD";

            $this->crearObservacionSolicitudProveedor();

            $respuesta = [
                [
                    "status" => "OK",
                    "estatusSolicitud" => $datos["estatusSolicitudProveedorId"]
                ]
            ];
        }

        return $respuesta;

    }
 
    /*=============================================
	RECHAZAR SOLICITUD DEL PROVEEDOR
	=============================================*/
    public function rechazarSolicitudProveedor() {
        
        //OBTIENE LOS ARCHIVOS RECHAZADOS PARA EL 
        // CAMBIO DE ESTATUS
        $archivosRechazados = $this->consultarArchivosRechazados();

        $datos = [];
        $datos[$this->keyName] = $this->id;
                if (empty($archivosRechazados)) {
            $datos["estatusSolicitudProveedorId"] = 22; //RECHAZADO
        } else {
            $datos["estatusSolicitudProveedorId"] = 23; //PENDIENTE ARCHIVOS
        }

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatusSolicitudProveedorId"] = self::$type["estatusSolicitudProveedorId"];

        $campos = fCreaCamposUpdate($arrayPDOParam);
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);

        if($respuesta){
            if ($this->observacionSolicitudProveedor != "") {

                if( $datos["estatusSolicitudProveedorId"] == 22){
                    $this->categoriaObservacion = "RECHAZADO";
                }else{
                    $this->categoriaObservacion = "PENDIENTE ARCHIVOS";
                }
                $this->tipoObservacion = "SOLICITUD";

                $this->crearObservacionSolicitudProveedor();
            } 
            $respuesta = [
                [
                    "status" => "OK",
                    "estatusSolicitud" => $datos["estatusSolicitudProveedorId"]
                ]
            ];
        }
        return $respuesta;
    }

    /*=============================================
	ESTADO DE LOS ARCHIVOS DEL PROVEEDOR (AUTORIZADO - RECHAZADO)
	=============================================*/
    public function estadoArchivo() {
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];

        $datos = [];
        $datos[$this->keyName] = $this->archivoId;

        if($this->estadoArchivo == "AUTORIZADO") {
            $datos["categoriaId"] = "ARCHIVO AUTORIZADO";
            if (empty($this->observacionSolicitudProveedor)) {
                $this->observacionSolicitudProveedor = "ARCHIVO AUTORIZADO";
            }
        }else{
            $datos["categoriaId"] = "ARCHIVO RECHAZADO";
        }

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE solicitud_proveedor_archivos SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);

        if($respuesta){

            // MANDAR LA CATEGORIA
            $this->categoriaObservacion = $datos["categoriaId"];
            $this->tipoObservacion = "ARCHIVO";

            $this->crearObservacionSolicitudProveedor();
        }
        return $respuesta;
    }

    /*=============================================
    CREAR OBSERVACIONES PARA LA SOLICITUD DEL PROVEEDOR
    =============================================*/
    public function crearObservacionSolicitudProveedor() {

        $datos = [];

        $arrayPDOParam = array();
        $arrayPDOParam["observacion"] = self::$type["observacion"];
        $arrayPDOParam["solicitudProveedorId"] = self::$type["solicitudProveedorId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["tipoObservacion"] = self::$type["tipoObservacion"];
        $arrayPDOParam["solicitudProveedorArchivoId"] = self::$type["solicitudProveedorArchivoId"];

        $datos["solicitudProveedorId"] = $this->id;
        $datos["observacion"] = $this->observacionSolicitudProveedor;
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["categoriaId"] = $this->categoriaObservacion;
        $datos["tipoObservacion"] = $this->tipoObservacion;

        if($this->tipoObservacion === "ARCHIVO"){
            $datos["solicitudProveedorArchivoId"] = $this->archivoId;
        }else{
            $datos["solicitudProveedorArchivoId"] = null;
        }

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute(
            $this->bdName,
            "INSERT INTO observaciones_solicitud_proveedor" . 
            $campos,
            $datos,
            $arrayPDOParam,
            $error,
            );
    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    /*=============================================
	ACTUALIZAR RUTA NUEVA EN LOS ARCHIVOS DE LA 
    SOLICITUD
	=============================================*/
    public function actualizarRutaArchivoSolicitud($archivo,$nuevaRuta){

        $datos = [];
        $datos[$this->keyName] = $archivo["id"];
        $datos["ruta"] = $nuevaRuta; 

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["ruta"] = self::$type["ruta"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE solicitud_proveedor_archivos SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

}