<?php

namespace App\Models;

use App\Conexion;
use PDO;

class SolicitudTipo
{
    static protected $fillable = [
        'descripcion', 'nombreCorto'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string'
    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "solicitud_tipos";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE SOLICITUD
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT ST.* FROM $this->tableName ST ORDER BY ST.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto) VALUES (:descripcion, :nombreCorto)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto WHERE id = :id", $datos, $arrayPDOParam, $error);

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
