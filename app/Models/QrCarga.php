<?php

namespace App\Models;

if ( file_exists ( "app/Policies/QrCargaPolicy.php" ) ) {
    require_once "app/Policies/QrCargaPolicy.php";
} else {
    require_once "../Policies/QrCargaPolicy.php";
}

use App\Conexion;
use PDO;

class QrCarga extends QrCargaPolicy
{
    static protected $fillable = [
        'nId01Qr',
        'operadorId',
        'sMarca',
        'sModelo',
        'sPlaca',
        'sYear',
        'sCapacidad',
        'dFechaCreacion',
        'nIdUsuarioCreacion ',
        'sNumeroEconomico',
        'nIdOperador03Operador',
        'idMaquinaria',

        'evidenciaArchivos',
        'verificacionArchivos',
        'tarjetaCirculacionArchivos',
        'acuerdoArchivos',

        'condiciones',
        'polizaSeguro',
        'arrendadorEquipo',
        'cumpleNoCumple',
    ];

    static protected $type = [
        'nId01Qr' => 'integer',
        'operadorId' => 'integer',
        'sMarca' => 'string',
        'sModelo' => 'string',
        'sPlaca' => 'string',
        'sYear' => 'string',
        'sCapacidad' => 'string',
        'dFechaCreacion' => 'string',
        'nIdUsuarioCreacion' => 'integer',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "COSTOS_01_QR";

    protected $keyName = "nId01Qr";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR QR
    =============================================*/

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT 
                QR.nId01Qr,
                QR.sEstatus,
                MT.sPlaca,
                O.sNombre AS nombreOperador,
                MT.sCapacidad
            FROM 
                $this->tableName QR 
            LEFT JOIN 
                COSTOS_02_MAQUINARIA_TRASLADO MT
            ON 
                QR.nIdMaquinaria02MaquinariaTraslado 
            = 	
                MT.nId02MaquinariaTraslado
            LEFT JOIN 
                COSTOS_03_OPERADOR O
            ON 
                MT.nIdOperador03Operador	
            = 	
                O.nId03Operador
            ORDER BY QR.sEstatus DESC, QR.nId01Qr ASC", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                    QR.nId01Qr,
                    MT.nId02MaquinariaTraslado AS idMaquinaria,
                    MT.sPlaca,
                    MT.sModelo,
                    MT.sYear,
                    MT.sMarca,
                    MT.sCapacidad,
                    MT.sNumeroEconomico,
                    MT.condiciones,
                    MT.polizaSeguro,
                    MT.arrendadorEquipo,
                    MT.cumpleNoCumple,
                    O.nId03Operador 
                FROM 
                    $this->tableName QR
                LEFT JOIN 
                    COSTOS_02_MAQUINARIA_TRASLADO MT
                ON 
                    QR.nIdMaquinaria02MaquinariaTraslado 
                = 	
                    MT.nId02MaquinariaTraslado
                LEFT JOIN 
                    COSTOS_03_OPERADOR O
                ON 
                    MT.nIdOperador03Operador	
                = 	
                O.nId03Operador
                WHERE 
                    $this->keyName = $valor", $error);

             
            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["nId01Qr"];      
                $this->idMaquinaria = $respuesta["idMaquinaria"] ;
                $this->placa = $respuesta["sPlaca"];
                $this->marca = $respuesta["sMarca"];
                $this->modelo = $respuesta["sModelo"];
                $this->año = $respuesta["sYear"];
                $this->capacidad = $respuesta["sCapacidad"];
                $this->numeroEconomico = $respuesta["sNumeroEconomico"];
                $this->polizaSeguro = $respuesta["polizaSeguro"];
                $this->condiciones = $respuesta["condiciones"];
                $this->arrendadorEquipo = $respuesta["arrendadorEquipo"];
                $this->cumpleNoCumple = $respuesta["cumpleNoCumple"];
    
                $this->operadorId = $respuesta["nId03Operador"];
            }

            return $respuesta;

        }

    }

    // DAR DE BAJA EQUIPO

    public function darBaja(){

        $idCarga = $this->idCarga;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["sEstatus"] = 'string';
        $arrayPDOParam["nIdMaquinaria02MaquinariaTraslado"] = 'integer';

        $datos = array();
        $datos[$this->keyName] = $this->id;        
        $datos["nIdMaquinaria02MaquinariaTraslado"] = null;        

        $campos = fCreaCamposUpdate($arrayPDOParam);
        
        $datos["sEstatus"] = "DISPONIBLE";

        $respuesta =true;
        $respuesta =   Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE nId01Qr = :nId01Qr", $datos, $arrayPDOParam, $error);

        if($respuesta){

            if (isset($idCarga) && is_numeric($idCarga)) {
                Conexion::queryExecute($this->bdName, "UPDATE COSTOS_04_CARGA
                                                        SET estatus = 'COMPLETADO'
                                                        WHERE nId04Carga = $idCarga;", [], [], $error);
            }


            $this->completarArchivosQr();
        }

        return $respuesta;
    }


    public function darBajaOperadorMaquinaria(){

        $arrayPDOParam = array();
        $arrayPDOParam["nId02MaquinariaTraslado"] = 'integer';
        $arrayPDOParam["nIdOperador03Operador"] = 'integer';

        $datos = array();
        $datos["nId02MaquinariaTraslado"] = $this->idMaquinaria;        
        $datos["nIdOperador03Operador"] = null;        

        $campos = fCreaCamposUpdate($arrayPDOParam);
        
        $respuesta =  Conexion::queryExecute($this->bdName, "UPDATE COSTOS_02_MAQUINARIA_TRASLADO  SET " . $campos . " WHERE nId02MaquinariaTraslado = :nId02MaquinariaTraslado", $datos, $arrayPDOParam, $error);

        return $respuesta;
    
    }

    public function completarArchivosQr()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos["nIdMaquinaria02MaquinariaTraslado"] = $this->idMaquinaria;
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
        $datos["eliminado"] = 2;
        
        $arrayPDOParam = array();
        $arrayPDOParam["nIdMaquinaria02MaquinariaTraslado"] = self::$type[$this->keyName];
        $arrayPDOParam["usuarioIdActualizacion"] = "integer";
        $arrayPDOParam["eliminado"] = "integer";
        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE qr_cargas_archivos SET " . $campos . " WHERE 	nIdMaquinaria02MaquinariaTraslado = :nIdMaquinaria02MaquinariaTraslado", $datos, $arrayPDOParam);


        return $respuesta;
    }


    public function actualizar($datos) {

        $respuesta = true;
                              
        if ( $respuesta ) {

            if ( isset($datos['evidenciaArchivos']) && $datos['evidenciaArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['evidenciaArchivos'], 1);
            
            if ( isset($datos['verificacionArchivos']) && $datos['verificacionArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['verificacionArchivos'], 2);

            if ( isset($datos['tarjetaCirculacionArchivos']) && $datos['tarjetaCirculacionArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['tarjetaCirculacionArchivos'], 3);

            if ( isset($datos['acuerdoArchivos']) && $datos['acuerdoArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['acuerdoArchivos'], 4);

        }
        return $respuesta;
    }

    function insertarArchivos($archivos, $tipoArchivo, $dir="") {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                if ( $tipoArchivo == 1 ) $directorio =  "vistas/uploaded-files/qr-cargas/evidencias/";
                elseif ( $tipoArchivo == 2 ) $directorio = "vistas/uploaded-files/qr-cargas/verificacion/";
                elseif ( $tipoArchivo == 3 ) $directorio = "vistas/uploaded-files/qr-cargas/tarjeta-circulacion/";
                elseif ( $tipoArchivo == 4 ) $directorio = "vistas/uploaded-files/qr-cargas/acuerdo/";

                else $directorio = "vistas/uploaded-files/requisiciones/qr-cargas/";
                // $aleatorio = mt_rand(10000000,99999999);

                $extension = '';
                if (!is_dir($dir.$directorio)) {
                    // Crear el directorio si no existe
                    mkdir($dir.$directorio, 0777, true);
                }
                
                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";
                elseif ( $archivos["type"][$i] == "text/xml" ) $extension = ".xml";
                elseif ( $archivos["type"][$i] == "image/jpg" ) $extension = ".jpg";
                elseif ( $archivos["type"][$i] == "image/png" ) $extension = ".jpg";
                elseif ( $archivos["type"][$i] == "image/jpeg" ) $extension = ".jpg";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["nIdMaquinaria02MaquinariaTraslado"] = $this->id;
            $insertar["tipo"] = $tipoArchivo; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["nIdMaquinaria02MaquinariaTraslado"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO qr_cargas_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $dir.$ruta);
            }

        }

        return $respuesta;

    }

    public function consultarEvidencias() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT QCA.* FROM qr_cargas_archivos QCA WHERE QCA.nIdMaquinaria02MaquinariaTraslado = $this->idMaquinaria AND QCA.tipo = 1 AND QCA.eliminado = 1 ORDER BY QCA.id", $error);
        
        $this->evidencias = $resultado;

    }

    public function consultarVerificaciones() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT QCA.* FROM qr_cargas_archivos QCA WHERE QCA.nIdMaquinaria02MaquinariaTraslado = $this->idMaquinaria AND QCA.tipo = 2 AND QCA.eliminado = 1 ORDER BY QCA.id", $error);
        
        $this->verificaciones = $resultado;
    }

    public function consultarTarjetasCirculacion() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT QCA.* FROM qr_cargas_archivos QCA WHERE QCA.nIdMaquinaria02MaquinariaTraslado = $this->idMaquinaria AND QCA.tipo = 3 AND QCA.eliminado = 1 ORDER BY QCA.id", $error);
        
        $this->tarjetasCirculacion = $resultado;
    }

    public function consultarAcuedos() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT QCA.* FROM qr_cargas_archivos QCA WHERE QCA.nIdMaquinaria02MaquinariaTraslado  = $this->idMaquinaria AND QCA.tipo = 4 AND QCA.eliminado = 1 ORDER BY QCA.id", $error);
        
        $this->acuerdos = $resultado;
    }
}