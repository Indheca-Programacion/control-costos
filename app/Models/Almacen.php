<?php

namespace App\Models;

if ( file_exists ( "app/Policies/AlmacenPolicy.php" ) ) {
    require_once "app/Policies/AlmacenPolicy.php";
} else {
    require_once "../Policies/AlmacenPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\AlmacenPolicy;

class Almacen extends AlmacenPolicy
{
    static protected $fillable = [
        'nombre', 'nombreCorto'
    ];
    
    static protected $type = [
        'id' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'nombre' => 'string',
        'nombreCorto' => 'string',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "almacenes";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];                
                $this->nombre = $respuesta["nombre"];
                $this->nombreCorto = $respuesta["nombreCorto"];

            }

            return $respuesta;

        }

    }

    public function consultarPropios()
    {
        $id = usuarioAutenticado()["id"];
        return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName WHERE usuarioIdCreacion = $id", $error);
    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
    
        $campos = fCreaCamposInsert($arrayPDOParam);

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["nombre"] = self::$type["nombre"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET {$campos} WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

}
