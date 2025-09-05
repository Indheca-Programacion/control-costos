<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PuestoPolicy.php" ) ) {
    require_once "app/Policies/PuestoPolicy.php";
} else {
    require_once "../Policies/PuestoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PuestoPolicy;

class Puesto extends PuestoPolicy
{
    static protected $fillable = [
        'id', 'nombreCorto','descripcion'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion ' => 'string',
        'nombreCorto ' => 'string',
        'id_puesto' => 'integer',

    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "puestos";

    protected $keyName = "id";

    public $id = null;    
    public $nombre;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PUESTOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT P.* FROM $this->tableName P", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion  = $respuesta["descripcion"];
                $this->nombreCorto  = $respuesta["nombreCorto"];
                $this->fechaCreacion  = $respuesta["fechaCreacion"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["nombreCorto"] = "string";
        $arrayPDOParam["descripcion"] = "string";

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (nombreCorto,descripcion) VALUES ( :nombreCorto,:descripcion) ", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET  nombreCorto = :nombreCorto, descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

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
