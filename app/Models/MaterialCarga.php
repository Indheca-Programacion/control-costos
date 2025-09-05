<?php

namespace App\Models;

if ( file_exists ( "app/Policies/MaterialCargaPolicy.php" ) ) {
    require_once "app/Policies/MaterialCargaPolicy.php";
} else {
    require_once "../Policies/MaterialCargaPolicy.php";
}

use App\Conexion;
use PDO;


class MaterialCarga extends MaterialCargaPolicy
{
    static protected $fillable = [
        'nId05Material',
        'sDescripcion'
    ];

    static protected $type = [
        'nId05Material' => 'integer',
        'sDescripcion' => 'varchar',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "COSTOS_05_MATERIAL";

    protected $keyName = "nId05Material";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MATERIALES
    =============================================*/

    public function consultar($item = null, $valor = null) {


        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT 
                M.nId05Material as id,
                M.sDescripcion AS descripcion
            FROM 
                $this->tableName M", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                    M.nId05Material AS id,
                    M.sDescripcion AS descripcion
                FROM 
                    $this->tableName M
                WHERE 
                    M.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                    M.nId05Material AS id,
                    M.sDescripcion AS descripcion
                FROM 
                    $this->tableName M
                WHERE 
                    M.$item = '$valor'", $error);
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];                
                $this->descripcion = $respuesta["descripcion"];
            }
            return $respuesta;
        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["sDescripcion"] = self::$type["sDescripcion"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (sDescripcion) VALUES (:sDescripcion)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["sDescripcion"] = self::$type["sDescripcion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET {$campos} WHERE nId05Material  = :nId05Material ", $datos, $arrayPDOParam, $error);

    }

}