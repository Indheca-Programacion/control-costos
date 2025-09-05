<?php

namespace App\Models;

if ( file_exists ( "app/Policies/MaquinariaTrasladoPolicy.php" ) ) {
    require_once "app/Policies/MaquinariaTrasladoPolicy.php";
} else {
    require_once "../Policies/MaquinariaTrasladoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\MaquinariaTrasladoPolicy;

class MaquinariaTraslado extends MaquinariaTrasladoPolicy
{
    static protected $fillable = [
        'sModelo', 'sPlaca', 'sMarca', 'sYear', 'sCapacidad', 'sNumeroEconomico', 'nIdOperador03Operador', 'nId01Qr',
        'condiciones',
        'polizaSeguro',
        'arrendadorEquipo',
        'cumpleNoCumple',
    ];

    static protected $type = [
        'nId02MaquinariaTraslado' => 'integer',
        'sPlaca' => 'string',
        'sMarca' => 'string',
        'sModelo' => 'string',
        'sYear' => 'string',
        'sCapacidad' => 'string',
        'sNumeroEconomico' => 'string',
        'nIdOperador03Operador' => 'integer',
        'dFechaCreacion' => 'date',
        'nIdUsuarioCreacion' => 'integer',
        'nIdQr01Qr' => 'integer',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "COSTOS_02_MAQUINARIA_TRASLADO";

    protected $keyName = "nId02MaquinariaTraslado";

    public $nId02MaquinariaTraslado = null;
    public $sPlaca = null;
    public $sMarca = null;
    public $sModelo = null;
    public $sYear = null;
    public $sCapacidad = null;
    public $sNumeroEconomico = null;
    public $nIdOperador03Operador = null;
    public $dFechaCreacion = null;
    public $nIdUsuarioCreacion = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR APLICACIONES
    =============================================*/

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT O.descripcion AS 'obra.descripcion',  MOV.dfechaCreacion AS 'fechaCreacion', MT.sPlaca AS 'placa',
                                                    MOV.dfechaCreacion, MT.nIdQr01Qr AS 'codigo', MOV.nId06MaquinariaMovimiento AS 'id', OP.sNombre AS 'operador',
                                                    CASE 
                                                        WHEN MOV.nTipo = 0 THEN 'salida' 
                                                        WHEN MOV.nTipo = 1 THEN 'entrada'
                                                        ELSE 'desconocido'                                        
                                                    END AS 'tipoMovimiento',
                                                    CASE
                                                        WHEN MOV.nEstatus = 0 THEN 'no cargado'
                                                        WHEN MOV.nEstatus = 1 THEN 'cargado'
                                                        ELSE 'desconocido'
                                                    END AS 'estatusMovimiento'
                                                    FROM $this->tableName MOV
                                                    INNER JOIN obras O ON O.id = MOV.nIdObra
                                                    INNER JOIN COSTOS_02_MAQUINARIA_TRASLADO MT ON MT.nId02MaquinariaTraslado = MOV.nIdMaquinariaTraslado02MaquinariaTraslado
                                                    INNER JOIN COSTOS_01_QR QR ON QR.nIdMaquinaria02MaquinariaTraslado = MT.nId02MaquinariaTraslado
                                                    INNER JOIN COSTOS_03_OPERADOR OP ON OP.nId03Operador = MT.nIdOperador03Operador", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->nId02MaquinariaTraslado = $respuesta["nId02MaquinariaTraslado"];
                $this->sPlaca = $respuesta["sPlaca"];
                $this->sMarca = $respuesta["sMarca"];
                $this->sModelo = $respuesta["sModelo"];
                $this->sYear = $respuesta["sYear"];
                $this->sCapacidad = $respuesta["sCapacidad"];
                $this->sNumeroEconomico = $respuesta["sNumeroEconomico"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["sModelo"] = self::$type["sModelo"];
        $arrayPDOParam["sPlaca"] = self::$type["sPlaca"];
        $arrayPDOParam["sMarca"] = self::$type["sMarca"];
        $arrayPDOParam["sYear"] = self::$type["sYear"];
        $arrayPDOParam["sCapacidad"] = self::$type["sCapacidad"];
        $arrayPDOParam["sNumeroEconomico"] = self::$type["sNumeroEconomico"];
        $arrayPDOParam["nIdOperador03Operador"] = self::$type["nIdOperador03Operador"];
        $arrayPDOParam["nIdUsuarioCreacion"] = self::$type["nIdUsuarioCreacion"];
        $arrayPDOParam["nIdQr01Qr"] = self::$type["nIdQr01Qr"];
        $arrayPDOParam["condiciones"] = 'string';
        $arrayPDOParam["polizaSeguro"] = 'string';
        $arrayPDOParam["arrendadorEquipo"] = 'string';
        $arrayPDOParam["cumpleNoCumple"] = 'string';

        $datos["nIdQr01Qr"] = $datos["nId01Qr"];

        $datos["nIdUsuarioCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);
        
        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ". $campos, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {
            $arrayPDOParamQR = array();
            $arrayPDOParamQR["nIdMaquinaria02MaquinariaTraslado"] = 'integer';
            $arrayPDOParamQR["sEstatus"] = 'string';

            $datosQR = array();
            $datosQR["nIdMaquinaria02MaquinariaTraslado"] = $lastId;
            $datosQR["nId01Qr"] = $datos["nId01Qr"];

            $datosQR["sEstatus"] = 'Activo';

            $camposQR = fCreaCamposUpdate($arrayPDOParamQR);

            $arrayPDOParamQR["nId01Qr"] = 'integer';

            $respuesta = Conexion::queryExecute($this->bdName, "UPDATE COSTOS_01_QR SET " . $camposQR . " WHERE nId01Qr = :nId01Qr", $datosQR, $arrayPDOParamQR, $error);

        }
        return $respuesta;
    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["fk_obraDetalle"] = self::$type["fk_obraDetalle"];
        
        $campos = fCreaCamposUpdate($arrayPDOParam);
        
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE nId06MaquinariaMovimiento = :nId06MaquinariaMovimiento", $datos, $arrayPDOParam, $error);

    }

}