<?php

namespace App\Models;

if ( file_exists ( "app/Policies/MovimientosPolicy.php" ) ) {
    require_once "app/Policies/MovimientosPolicy.php";
} else {
    require_once "../Policies/MovimientosPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\MovimientosPolicy;

class Movimientos extends MovimientosPolicy
{
    static protected $fillable = [
        'id', 'nIdObra', 'nTipo', 'nEstatus', 'nIdMaquinariaTraslado02MaquinariaTraslado','idCarga'
    ];

    static protected $type = [
        'nId06MaquinariaMovimiento' => 'integer',
        'nIdObra' => 'integer',
        'nTipo' => 'integer',
        'nEstatus' => 'integer',
        'nIdMaquinariaTraslado02MaquinariaTraslado' => 'integer',
        'nIdUsuarioCreacion' => 'integer',
        'idCarga'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "COSTOS_06_MAQUINARIA_MOVIMIENTO";

    protected $keyName = "nId06MaquinariaMovimiento";

    public $id = null;    
    public $nIdObra;
    public $nTipo;
    public $nEstatus;
    public $nIdMaquinariaTraslado02MaquinariaTraslado;

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
                                                    left JOIN COSTOS_01_QR QR ON QR.nIdMaquinaria02MaquinariaTraslado = MT.nId02MaquinariaTraslado
                                                    INNER JOIN COSTOS_03_OPERADOR OP ON OP.nId03Operador = MT.nIdOperador03Operador
                                                    ORDER BY MOV.dFechaCreacion DESC", $error);
        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryAll($this->bdName, 
                "SELECT O.descripcion AS 'obra.descripcion',  
                    MOV.dfechaCreacion AS 'fechaCreacion', 
                    MT.sPlaca AS 'placa',
                                                    MOV.dfechaCreacion, 
                                                    MT.nIdQr01Qr AS 'codigo', 
                                                    MOV.nId06MaquinariaMovimiento AS 'id', OP.sNombre AS 'operador',
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
                                                    left JOIN COSTOS_01_QR QR ON QR.nIdMaquinaria02MaquinariaTraslado = MT.nId02MaquinariaTraslado
                                                    INNER JOIN COSTOS_03_OPERADOR OP ON OP.nId03Operador = MT.nIdOperador03Operador
                                                    WHERE $item = '$valor'
                                                    ORDER BY MOV.dFechaCreacion DESC", $error);
            }

            if ( $respuesta ) {
                // $this->id = $respuesta["id"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["nIdObra"] = self::$type["nIdObra"];
        $arrayPDOParam["nTipo"] = self::$type["nTipo"];
        $arrayPDOParam["nEstatus"] = self::$type["nEstatus"];
        $arrayPDOParam["nIdMaquinariaTraslado02MaquinariaTraslado"] = self::$type["nIdMaquinariaTraslado02MaquinariaTraslado"];
        $arrayPDOParam["nIdUsuarioCreacion"] = self::$type["nIdUsuarioCreacion"];
        $arrayPDOParam["nId04Carga"] = 'integer';


        $idCarga = $datos["idCarga"];

        $datos["nIdUsuarioCreacion"] = usuarioAutenticado()["id"];
        $datos["nId04Carga"] = $idCarga;

        if( isset($datos["idCarga"]) && $datos["idCarga"] > 0){

            if($datos["nTipo"] == 0 ){


                Conexion::queryExecute($this->bdName, "UPDATE COSTOS_04_CARGA
                                                                SET estatus = 'ACTIVADO'
                                                                WHERE nId04Carga = $idCarga", [], [], $error);
            }else{
                Conexion::queryExecute($this->bdName, "UPDATE COSTOS_04_CARGA
                                                                SET estatus = 'DESACTIVADO'
                                                                WHERE nId04Carga = $idCarga", [], [], $error);
            }
            
        }

        
        $campos = fCreaCamposInsert($arrayPDOParam);

        $movimientoId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ". $campos, $datos, $arrayPDOParam, $error, $movimientoId);
        if ( $respuesta ){
            $this->id = $movimientoId;
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

