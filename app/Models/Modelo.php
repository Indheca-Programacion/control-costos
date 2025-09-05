<?php

namespace App\Models;


use App\Conexion;
use PDO;

class Modelo
{
    static protected $fillable = [
        // 'empresaId', 
        'marcaId', 'descripcion'
    ];

    static protected $type = [
        'id' => 'integer',
        // 'empresaId' => 'integer',
        'marcaId' => 'integer',
        'descripcion' => 'string'
    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "modelos";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $marcaId;
    public $descripcion;


    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MODELOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        $this->empresaId = empresaId();

        if ( is_null($valor) ) {

            // return Conexion::queryAll($this->bdName, "SELECT MD.*, M.descripcion AS 'marcas.descripcion', E.nombreCorto AS 'empresas.nombreCorto' FROM $this->tableName MD INNER JOIN marcas M ON MD.marcaId = M.id INNER JOIN ".CONST_BD_SECURITY.".empresas E ON M.empresaId = E.id WHERE MD.empresaId = $this->empresaId ORDER BY E.nombreCorto, M.descripcion, MD.descripcion", $error);
            return Conexion::queryAll($this->bdName, "SELECT MD.*, M.descripcion AS 'marcas.descripcion' FROM $this->tableName MD INNER JOIN marcas M ON MD.marcaId = M.id ORDER BY M.descripcion, MD.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                // $this->empresaId = $respuesta["empresaId"];
                $this->marcaId = $respuesta["marcaId"];
                $this->descripcion = $respuesta["descripcion"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["marcaId"] = self::$type["marcaId"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        // return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (empresaId, marcaId, descripcion) VALUES (:empresaId, :marcaId, :descripcion)", $datos, $arrayPDOParam, $error);
        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (marcaId, descripcion) VALUES (:marcaId, :descripcion)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["marcaId"] = self::$type["marcaId"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        // return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET marcaId = :marcaId, descripcion = :descripcion WHERE id = :id AND empresaId = :empresaId", $datos, $arrayPDOParam, $error);
        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET marcaId = :marcaId, descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        // $datos["empresaId"] = empresaId();
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];

        // return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id AND empresaId = :empresaId", $datos, $arrayPDOParam, $error);
        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
