<?php

namespace App\Models;

use App\Conexion;
use PDO;

class DatosFiscalArchivos
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
        'usuarioIdCreacion' => 'integer',
        'usuarioIdModificacion' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "datos_fiscales_archivos";    

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultarCV($id = null) {

        if($id){
            $resultado = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 1 and proveedorId = ".$id, $error);
            $this->cv = $resultado;
            return; 
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 1 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->cv = $resultado;
    }

    public function consultarContratoFacturaOC1($id = null) {

        if($id){
            $resultado = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 2 and proveedorId = ".$id, $error);
            $this->contrato_factura_oc1 = $resultado;
            return; 
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 2 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->contrato_factura_oc1 = $resultado;
    }

    public function consultarContratoFacturaOC2($id = null) {

        if($id){
            $resultado = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 3 and proveedorId = ".$id, $error);
            $this->contrato_factura_oc2 = $resultado;
            return; 
        }
        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 3 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->contrato_factura_oc2 = $resultado;
    }

    public function consultarContratoFacturaOC3($id = null) {

        if($id){
            $resultado = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 4 and proveedorId = ".$id, $error);
            $this->contrato_factura_oc3 = $resultado;   
            return; 
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 4 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->contrato_factura_oc3 = $resultado;
    }

    public function consultarActaConstitutiva($id = null) {

        if($id){
           $resultado = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 5 and proveedorId = ".$id, $error);
            $this->acta_constitutiva = $resultado;
            return; 
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 5 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->acta_constitutiva = $resultado;
    }

    public function consultarConstanciaSituacionFiscal($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 6 and proveedorId = ".$id, $error);
            $this->constancia_situacion_fiscal = $resultado;
            return; 
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 6 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->constancia_situacion_fiscal = $resultado;
    }

    public function consultarCumplimientoSAT($id = null) {

        if($id){
            $resultado = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 7 and proveedorId = ".$id, $error);
            $this->cumplimientoSAT = $resultado;
            return; 
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 7 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->cumplimientoSAT = $resultado;
    }

    public function consultarCumplimientoIMSS($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 8 and proveedorId = ".$id, $error);
            $this->cumplimientoIMSS = $resultado;
            return; 
        }


        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 8 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->cumplimientoIMSS = $resultado;
    }

    public function consultarCumplimientoInfonavit($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 9 and proveedorId = ".$id, $error);
            $this->cumplimientoInfonavit = $resultado;
            return; 
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 9 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->cumplimientoInfonavit = $resultado;
    }

    public function consultarAltaRepse($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 10 and proveedorId = ".$id, $error);
            $this->alta_repse = $resultado;
            return; 
        }
        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 10 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->alta_repse = $resultado;
    }

    public function consultarUltimaInformativa($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 11 and proveedorId = ".$id, $error);
            $this->ultima_informativa = $resultado;
            return;
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 11 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->ultima_informativa = $resultado;
    }

    public function consultarEstadoCuenta($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 12 and proveedorId = ".$id, $error);
            $this->estadoCuenta = $resultado;
            return;
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 12 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->estadoCuenta = $resultado;
    }

    public function consultarEstadoFinancieros($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 13 and proveedorId = ".$id, $error);
            $this->estadoFinancieros = $resultado;
            return;
        }
        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 13 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->estadoFinancieros = $resultado;
    }

    public function consultarUltimaDeclaracionAnual($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 14 and proveedorId = ".$id, $error);
            $this->ultimaDeclaracionAnual = $resultado;
            return;
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 14 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->ultimaDeclaracionAnual = $resultado;
    }

    public function consultarSoporte($id = null) {

        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 15 and proveedorId = ".$id, $error);
            $this->soporte = $resultado;
            return;
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 15 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->soporte = $resultado;
    }

    public function consultarListado($id = null){
        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 16 and proveedorId = ".$id, $error);
            $this->listado = $resultado;
            return;
        }
        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 16 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->listado = $resultado;
    }

    public function consultarCertificaciones($id = null) {
        if($id){
            $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 17 and proveedorId = ".$id, $error);
            $this->certificaciones = $resultado;
            return;
        }

        $resultado =  Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where tipo = 17 and proveedorId = ".usuarioAutenticadoProveedor()["id"], $error);
        $this->certificaciones = $resultado;
    }

    function insertarArchivos($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                $directorio = "../../vistas/uploaded-files/datos-fiscales/";//Esta sobrando los ../../ ya que como se usa esta funcion en ajax, hay problemas con las rutas
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';
                if (!is_dir($directorio)) {
                    // Crear el directorio si no existe
                    mkdir($directorio, 0777, true);
                }
                
                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";
                elseif ( $archivos["type"][$i] == "text/xml" ) $extension = ".xml";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["proveedorId"] = usuarioAutenticadoProveedor()["id"];
            $insertar["tipo"] = $this->tipo;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["estatus"] = "DOCUMENTO EN REVISION";

            $insertar["ruta"] = substr($ruta,6);
            $insertar["usuarioIdCreacion"] = usuarioAutenticadoProveedor()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["proveedorId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "string";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["estatus"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);//Estoy haciendo un substring por que como se usa esta funcion en ajax, hay problemas con las rutas
            }

        }

        return $respuesta;

    }

    public function eliminarArchivo() {

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", array("id" => $this->id), array("id" => self::$type[$this->keyName]), $error);

        return $respuesta;

    }

    public function autorizarArchivo(){

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatus"] = self::$type["estatus"];

        $datos = array();
        $datos[$this->keyName] = $this->id;        

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $datos["estatus"] = "AUTORIZADO POR JURIDICO";

        return  Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function rechazarArchivo(){

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatus"] = self::$type["estatus"];

        $datos = array();
        $datos[$this->keyName] = $this->id;        

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $datos["estatus"] = "RECHAZADO POR JURIDICO";

        return  Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}