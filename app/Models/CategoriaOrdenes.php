<?php

namespace App\Models;

if ( file_exists ( "app/Policies/CategoriaOrdenesPolicy.php" ) ) {
    require_once "app/Policies/CategoriaOrdenesPolicy.php";
} else {
    require_once "../Policies/CategoriaOrdenesPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\CategoriaOrdenesPolicy;

class CategoriaOrdenes extends CategoriaOrdenesPolicy
{
    static protected $fillable = [
        'nombre', 'descripcion',
    ];

    static protected $type = [
        'id' => 'integer',
        'nombre' => 'string',
        'descripcion' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "categoria_ordenes";

    protected $keyName = "id";

    public $id = null;
    public $nombre;
    public $descripcion;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CATEGORIA PROVEEDORES
    =============================================*/
    public function consultar($item = null, $valor = null, $aplicacionId = null) {

        if ( is_null($valor) ) {
            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName ", $error);

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
            }

            return $respuesta;

        }

    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (nombre, descripcion) VALUES ( :nombre, :descripcion)", $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {
            // Asignamos el ID creado al momento de crear el permiso
            $this->id = $lastId;
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;

    }
}
