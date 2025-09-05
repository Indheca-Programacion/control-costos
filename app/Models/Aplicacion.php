<?php

namespace App\Models;

// require_once "app/conexion.php";
// require_once "app/Policies/AplicacionPolicy.php";

if ( file_exists ( "app/conexion.php" ) ) {
    require_once "app/conexion.php";
} else {
    require_once "../conexion.php";
}

use App\Conexion;
use PDO;
// use App\Policies\AplicacionPolicy;

// class Aplicacion extends AplicacionPolicy
class Aplicacion
{
    static protected $fillable = [
        'nombre', 'descripcion', 'nombreBD'
    ];

    static protected $type = [
        'id' => 'integer',
        'nombre' => 'string',
        'descripcion' => 'string',
        'nombreBD' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "aplicaciones";

    protected $keyName = "id";

    public $id = null;    
    public $nombre;
    public $descripcion;
    public $nombreBD;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR APLICACIONES
    =============================================*/

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName ORDER BY descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];                
                $this->nombre = $respuesta["nombre"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreBD = $respuesta["nombreBD"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreBD"] = self::$type["nombreBD"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (nombre, descripcion, nombreBD) VALUES (:nombre, :descripcion, :nombreBD)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreBD"] = self::$type["nombreBD"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreBD = :nombreBD WHERE id = :id", $datos, $arrayPDOParam, $error);

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
