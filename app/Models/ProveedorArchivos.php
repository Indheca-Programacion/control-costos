<?php

namespace App\Models;

use App\Conexion;
use PDO;

class ProveedorArchivos
{
    static protected $fillable = [
        'id'
    ];

    static protected $type = [
        'id' => 'integer',
        'proveedorId' => 'integer',
        'titulo' => 'string',
        'archivo' => 'string',
        'formato' => 'string',
        'ruta' => 'string',
        'usuarioIdActualizacion' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'fechaCreacion' => 'date',
        'fechaActualizacion' => 'date',
        'eliminado' => 'integer',
        'categoriaId' => 'string',
        'observacion' => 'string',
        'tipoObservacion' => 'string',
        'archivoProveedorId' => 'string'

    ];

    // LISTADO DE DIRECCIONES PARA NUEVAS
    public $mapaDirectorios = [
        [
            "tipo" => 1,
            "path" => "vistas/uploaded-files/proveedores/cv/",
            "title" => "cv",
            "nombreArchivo" => "CV"
        ],
        [
            "tipo" => 2,
            "path" => "vistas/uploaded-files/proveedores/oc1/",
            "title" => "oc1",
            "nombreArchivo" => "OC1"
        ],     
        [
            "tipo" => 3,
            "path" => "vistas/uploaded-files/proveedores/oc2/",
            "title" => "oc2",
            "nombreArchivo" => "OC2"
        ],  
        [
            "tipo" => 4,
            "path" => "vistas/uploaded-files/proveedores/oc3/",
            "title" => "oc3",
            "nombreArchivo" => "OC3"
        ],
        [
            "tipo" => 5,
            "path" => "vistas/uploaded-files/proveedores/acta-constitutiva/",
            "title" => "acta-constitutiva",
            "nombreArchivo" => "ActaConstitutiva"
        ],
        [
            "tipo" => 6,
            "path" => "vistas/uploaded-files/proveedores/constancia-situacion-fiscal/",
            "title" => "constancia-situacion-fiscal",
            "nombreArchivo" => "ConstanciaSituacionFiscal"
        ],
                [
            "tipo" => 7,
            "path" => "vistas/uploaded-files/proveedores/cumplimiento-sat/",
            "title" => "cumplimiento-sat",
            "nombreArchivo" => "CumplimientoSAT"
        ],
                [
            "tipo" => 8,
            "path" => "vistas/uploaded-files/proveedores/cumpliento-imss/",
            "title" => "cumplimiento-imss",
            "nombreArchivo" => "CumplimientoIMSS"
        ],
                [
            "tipo" => 9,
            "path" => "vistas/uploaded-files/proveedores/cumplimiento-infonavit/",
            "title" => "cumplimiento-infonavit",
            "nombreArchivo" => "CumplimientoInfonavit"
        ],
                [
            "tipo" => 10,
            "path" => "vistas/uploaded-files/proveedores/alta-repse/",
            "title" => "alta-repse",
            "nombreArchivo" => "AltaRepse"
        ],
                [
            "tipo" => 11,
            "path" => "vistas/uploaded-files/proveedores/oc3/",
            "title" => "ultima-informativa",
            "nombreArchivo" => "UltimaInformativa"
        ],
        [
            "tipo" => 12,
            "path" => "vistas/uploaded-files/proveedores/estado-cuenta/",
            "title" => "estado-cuenta",
            "nombreArchivo" => "EstadoCuenta"
        ],
                [
            "tipo" => 13,
            "path" => "vistas/uploaded-files/proveedores/estado-financiero/",
            "title" => "estado-financiero",
            "nombreArchivo" => "EstadoFinanciero"
        ],  
        [
            "tipo" => 14,
            "path" => "vistas/uploaded-files/proveedores/ultima-declaracion-anual/",
            "title" => "ultima-declaracion-anual",
            "nombreArchivo" => "UltimaDeclaracionAnual"
        ],        
        [
            "tipo" => 15,
            "path" => "vistas/uploaded-files/proveedores/soporte/",
            "title" => "soporte",
            "nombreArchivo" => "Soporte"
        ],        
        [
            "tipo" => 16,
            "path" => "vistas/uploaded-files/proveedores/listado/",
            "title" => "listado",
            "nombreArchivo" => "Listado"
        ],        
        [
            "tipo" => 17,
            "path" => "vistas/uploaded-files/proveedores/certificaciones/",
            "title" => "certificaciones",
            "nombreArchivo" => "Certificaciones"
        ],
        [
            "tipo" => 18,
            "path" => "vistas/uploaded-files/proveedores/constancia-fiscal/",
            "title" => "constancia-fiscal",
            "nombreArchivo" => "ConstanciaFiscal"
        ],
        [
            "tipo" => 19,
            "path" => "vistas/uploaded-files/proveedores/opinion-cumplimiento/",
            "title" => "opinion-cumplimiento",
            "nombreArchivo" => "OpinionCumplimiento"
        ],
        [
            "tipo" => 20,
            "path" => "vistas/uploaded-files/proveedores/comprobante-domicilio/",
            "title" => "comprobante-domicilio",
            "nombreArchivo" => "ComprobanteDomicilio"
        ],
        [
            "tipo" => 21,
            "path" => "vistas/uploaded-files/proveedores/datos-bancarios/",
            "title" => "datos-bancarios",
            "nombreArchivo" => "DatosBancarios"
        ]
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "proveedor_archivos";    

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    CONSULTAR ARCHIVOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT A.* FROM $this->tableName A", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->proveedorId  = $respuesta["proveedorId"];
                $this->tipo  = $respuesta["tipo"];
                $this->titulo  = $respuesta["titulo"];
                $this->archivo  = $respuesta["archivo"];
                $this->formato  = $respuesta["formato"];
                $this->ruta  = $respuesta["ruta"];
                $this->categoriaId  = $respuesta["categoriaId"];
            }

            return $respuesta;

        }

    }

    // CONSULTAR ARCHIVO POR NOMBRE
    public function consultarArchivoNombre($nombre = null,$id = null) {
        
        foreach ($this->mapaDirectorios as $key => $value) {
            if($value["nombreArchivo"] == $nombre ){

                $nombreArchivo = $value["nombreArchivo"];
                if($id){
                    $resultado =  Conexion::queryAll($this->bdName, "SELECT 
                                                                    PA.*,
                                                                    PO.observacion
                                                                FROM $this->tableName PA
                                                                LEFT JOIN proveedor_observaciones PO ON PO.archivoProveedorId = PA.id
                                                                where 
                                                                    PA.tipo =  ". $value['tipo']."
                                                                and 
                                                                    PA.proveedorId = ".$id, $error);
                    $this->$nombreArchivo = $resultado;
                    return; 
                }

                $resultado =  Conexion::queryAll($this->bdName, "SELECT 
                                                                    PA.*,
                                                                    PO.observacion
                                                                FROM $this->tableName PA
                                                                LEFT JOIN proveedor_observaciones PO ON PO.archivoProveedorId = PA.id
                                                                where 
                                                                    PA.tipo =  ". $value['tipo']."
                                                                and 
                                                                    PA.proveedorId =  ".usuarioAutenticadoProveedor()["id"], $error);
                $this->$nombreArchivo = $resultado;
                return;
            }
        }




    }

    /*=============================================
    *FUNCIONES PARA INSERTAR DATOS
    =============================================*/

    /*=============================================
    FUNCION PARA INSERTAR Y MOVER LOS ARCHIVOS SUBIDOS
    POR EL PROVEEDOR
    
    @params $archivos Array de archivos 
    @return boolean Respuesta de la consulta
    =============================================*/
    function insertarArchivos($archivos) {

        $this->nombreOriginal = $archivos["name"];
        $this->tipoArchivo = $archivos["type"];
        $this->nombreTmp = $archivos["tmp_name"];

        if(!$this->guardarArchivoServidor()){
            throw new \Exception("PROBLEMAS AL INSERTAR EL ARCHIVO");
            return false;
        }
  
        $insertar = array();

        if ($this->proveedorId !== null && $this->proveedorId !== '') {
            $insertar["proveedorId"] =  $this->proveedorId;
        }else{
            $insertar["proveedorId"] = usuarioAutenticadoProveedor()["id"];
        }
        $insertar["tipo"] = $this->tipo;
        $insertar["titulo"] = $archivos["name"];
        $insertar["archivo"] = $this->nombreArchivoGenerado;
        $insertar["formato"] = $archivos["type"];   
        $insertar["categoriaId"] = "ESTADO PENDIENTE";
        $insertar["ruta"] = $this->rutaRelativa;
        if ($this->proveedorId !== null && $this->proveedorId !== '') {
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        }else{
            $insertar["usuarioIdCreacion"] = usuarioAutenticadoProveedor()["id"];
        }
        $arrayPDOParam = array();        
        $arrayPDOParam["proveedorId"] = self::$type[$this->keyName];
        $arrayPDOParam["tipo"] = "string";
        $arrayPDOParam["titulo"] = "string";
        $arrayPDOParam["archivo"] = "string";
        $arrayPDOParam["formato"] = "string";
        $arrayPDOParam["ruta"] = "string";
        $arrayPDOParam["categoriaId"] = "string";
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        // INSERTAR DATOS A LA BASE
        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $insertar, $arrayPDOParam, $error);
    }

    /*=============================================
    FUNCION INSERTAR LOS DATOS DE SOLICITUD ARCHIVOS 
    A PROVEEDOR ARCHIVOS
    
    @params $archivos array Array de datos del archivo de 
                        solicitud proveedor
    @params $directorio string Directorio donde se va a guardar el archivo
    @return $resultado boolean Estado de la consulta
    =============================================*/
    function insertarDatosProveedorArchivos( $datos , $directorio ) {


        $datos["proveedorId"] = $this->proveedorId;

        $arrayPDOParam = array();        
        $arrayPDOParam["proveedorId"] = self::$type[$this->keyName];
        $arrayPDOParam["tipo"] = "string";
        $arrayPDOParam["titulo"] = "string";
        $arrayPDOParam["archivo"] = "string";
        $arrayPDOParam["formato"] = "string";
        $arrayPDOParam["ruta"] = "string";
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["fechaCreacion"] = self::$type["fechaCreacion"];
        $arrayPDOParam["fechaActualizacion"] = self::$type["fechaActualizacion"];
        $arrayPDOParam["eliminado"] = self::$type["eliminado"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];

        // ASIGNAR NUEVO DIRECTORIO
        $datos["ruta"] = $directorio;

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error,$lastId);

        if($respuesta){
            $this->id = $lastId;
            // CREAR OBSERVACION
            $this->crearObservacionArchivo($datos);
        }

        return $respuesta;
    }

    /*=============================================
    * FUNCIONES PARA AUTORIZAR EL ARCHIVO
    =============================================*/
    public function autorizarArchivo(){

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];

        $datos = array();
        $datos[$this->keyName] = $this->id;        

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $datos["categoriaId"] = "ARCHIVO AUTORIZADO";

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        // CREAR OBSERVACION
        if($respuesta){
            $datos = [
                "detalleObservacion" => $this->observacion ?: "ARCHIVO AUTORIZADO",
                "categoriaIdObservacion" => "ARCHIVO AUTORIZADO",
                "tipoObservacion" => "ARCHIVO",
            ];

            $this->crearObservacionArchivo($datos);
        }

        return $respuesta;

    }

    public function rechazarArchivo(){

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];

        $datos = array();
        $datos[$this->keyName] = $this->id;        

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $datos["categoriaId"] = "ARCHIVO RECHAZADO";

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        // CREAR OBSERVACION
        if($respuesta){
            $datos = [
                "detalleObservacion" => $this->observacion ?: "ARCHIVO RECHAZADO",
                "categoriaIdObservacion" => "ARCHIVO RECHAZADO",
                "tipoObservacion" => "ARCHIVO",
            ];

            $this->crearObservacionArchivo($datos);
        }
        return $respuesta;
    }

    /*=============================================
    * FUNCIONES PARA ELIMINAR DATOS
    =============================================*/

    /*=============================================
    FUNCION ELIMINAR LOS ARCHIVOS POR ID DEL ARCHIVO
    
    @return $resultado boolean Estado de la consulta
    =============================================*/
    public function eliminarArchivo() { 

        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = false;

        if(!$this->eliminarArchivoProveedorEnServidor($this->ruta)){
            return false;
            die;
        }
        
        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    /*=============================================
    * HELPERS
    =============================================*/

    /*=============================================
    FUNCION PARA ELMINAR ARCHIVOS DEL PROVEEDOR EN EL
    SERVIDOR.
    RUTA QUE RECIBE: vistas/uploaded-files/proveedores
    
    @params $rutaRelativa Ruta relativa del archivo
    @return $respuesta boolean respuesta de la eliminación
    =============================================*/
    public function eliminarArchivoProveedorEnServidor($rutaRelativa){
        //CREA RUTA ABSOLUTA
        $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT']. CONST_APP_FOLDER . $rutaRelativa;

        $respuesta = true;
        if (file_exists($rutaAbsoluta)){
            if (!unlink($rutaAbsoluta)) { echo "No se pudo eliminar el archivo."; 
                return $respuesta = false;
            } 
        }else { echo "El archivo no existe."; 
            return $respuesta = false;
        }
        return $respuesta;
    }

    /*=============================================
    FUNCION OBTENER LAS DIRECCIONES SEGUN LOS TIPOS
    DE ARCHIVOS
    
    @params $tipoArchivo int Tipo de archivo al que se va a guardar como referencia
    @return $directorio string Direcctorio obtenido
    =============================================*/
    public function directorioArchivo($tipoArchivo) {

        foreach ($this->mapaDirectorios as $directorio) {
            if ($directorio['tipo'] == $tipoArchivo) {
                return $directorio['path'];
            }
        }
        // Si no lo encuentra, retorna el default
        return "vistas/uploaded-files/proveedores/";
    }

    /*=============================================
    FUNCION PARA CREAR OBSERVACIONES
    
    @params $archivos array Array de datos del archivo de 
                        solicitud proveedor
    @return $resultado boolean Estado de la consulta
    =============================================*/
    public function crearObservacionArchivo($datos){

        $arrayPDOParam = array();
        $arrayPDOParam["observacion"] = self::$type["observacion"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["tipoObservacion"] = self::$type["tipoObservacion"];
        $arrayPDOParam["archivoProveedorId"] = self::$type["archivoProveedorId"];

        $datos["observacion"] = $datos["detalleObservacion"];
        $datos["proveedorId"] = $this->proveedorId;
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["categoriaId"] = $datos["categoriaIdObservacion"];
        $datos["tipoObservacion"] = $datos["tipoObservacion"];
 
        if($datos["tipoObservacion"] === "ARCHIVO"){
            $datos["archivoProveedorId"] = $this->id;
        }else{
            $datos["archivoProveedorId"] = null;
        }

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO proveedor_observaciones " . $campos, $datos, $arrayPDOParam, $error);

    }

    /*=============================================
    FUNCION PARA CREAR OBSERVACIONES
    
    @params $datos array Array de datos del archivo 
    @return $resultado boolean Estado del movimiento
    =============================================*/
    public function guardarArchivoServidor(){

        $nombreArchivoGenerado = "";

        // BUSCA EN EL ARREGLO MAPA DIRECTORIOS EL NOMBRE 
        // DEL ARCHIVO
        foreach ($this->mapaDirectorios as $key => $value) {
                if($value["tipo"] == $this->tipo){
                    $nombreArchivoGenerado = "{$value["nombreArchivo"]}_" . time() . "_" . basename($this->nombreOriginal);
                    break;
                }else{
                    $nombreArchivoGenerado = "sin_carpeta_" . time() . "_" . basename($this->nombreOriginal);
                }
        }

        $directorio = $this->directorioArchivo($this->tipo);
        $directorioAbsoluto = $_SERVER['DOCUMENT_ROOT']. CONST_APP_FOLDER . $directorio;

        $rutaRelativa = $directorio . $nombreArchivoGenerado;
        $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT']. CONST_APP_FOLDER . $rutaRelativa;

        // Crear el directorio si no existe
        if (!is_dir($directorioAbsoluto)) {
            mkdir($directorioAbsoluto, 0777, true);
        }
        // MOVER EL ARCHIVO TEMPORAL A LA CARPETA DEL DE ARCHIVOS 
        if(!moverArchivos($this->nombreTmp, $rutaAbsoluta)){
            throw new \Exception("PROBLEMAS AL INSERTAR EL ARCHIVO");
            return false;
        }

        $this->nombreArchivoGenerado =$nombreArchivoGenerado;
        $this->rutaRelativa =$rutaRelativa;

        return true;
    }

}