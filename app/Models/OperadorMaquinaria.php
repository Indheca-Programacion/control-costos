<?php

namespace App\Models;

if ( file_exists ( "app/Policies/OperadorMaquinariaPolicy.php" ) ) {
    require_once "app/Policies/OperadorMaquinariaPolicy.php";
} else {
    require_once "../Policies/OperadorMaquinariaPolicy.php";
}

use App\Conexion;
use PDO;

class OperadorMaquinaria extends OperadorMaquinariaPolicy
{
    static protected $fillable = [
        'nId03Operador',
        'sNombre'
    ];

    static protected $type = [
        'nId03Operador' => 'integer',
        'sNombre' => 'varchar',

    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "COSTOS_03_OPERADOR";

    protected $keyName = "nId03Operador";

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
                OM.nId03Operador as id,
                OM.sNombre AS nombreOperador
            FROM 
                $this->tableName OM", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                    OM.nId03Operador AS id,
                    OM.sNombre AS nombre
                FROM 
                    $this->tableName OM
                WHERE 
                    OM.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                    OM.nId03Operador AS id,
                    OM.sNombre AS nombre
                FROM 
                    $this->tableName OM
                WHERE 
                    OM.$item = '$valor'", $error);
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];                
                $this->nombre = $respuesta["nombre"];
            }
            return $respuesta;
        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["sNombre"] = self::$type["sNombre"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (sNombre) VALUES (:sNombre)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["sNombre"] = self::$type["sNombre"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET {$campos} WHERE nId03Operador  = :nId03Operador ", $datos, $arrayPDOParam, $error);

    }

}