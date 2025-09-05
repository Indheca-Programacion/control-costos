<?php

namespace App\Models;

if ( file_exists ( "app/Policies/InsumoTipoPolicy.php" ) ) {
    require_once "app/Policies/InsumoTipoPolicy.php";
} else {
    require_once "../Policies/InsumoTipoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\InsumoTipoPolicy;

class InsumoTipo extends InsumoTipoPolicy
{
    static protected $fillable = [
        'descripcion', 'nombreCorto', 'orden', 'perfilesCrearRequis'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'orden' => 'integer',
        'perfilesCrearRequis' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "insumo_tipos";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;
    public $orden;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE INSUMOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT IT.* FROM $this->tableName IT ORDER BY IT.descripcion", $error);

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
                $this->orden = $respuesta["orden"];
                $this->perfilesCrearRequis = json_decode($respuesta["perfilesCrearRequis"]);

            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["perfilesCrearRequis"] = self::$type["perfilesCrearRequis"];
        $arrayPDOParam["orden"] = self::$type["orden"];

        if (!isset($datos["perfilesCrearRequis"])) {
            $datos["perfilesCrearRequis"] = "[]";
        }else{
            $datos["perfilesCrearRequis"] = json_encode($datos["perfilesCrearRequis"]);
        }

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto, orden, perfilesCrearRequis) VALUES (:descripcion, :nombreCorto, :orden, :perfilesCrearRequis)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["orden"] = self::$type["orden"];
        $arrayPDOParam["perfilesCrearRequis"] = self::$type["perfilesCrearRequis"];


        if (!isset($datos["perfilesCrearRequis"])) {
            $datos["perfilesCrearRequis"] = "[]";
        }else{
            $datos["perfilesCrearRequis"] = json_encode($datos["perfilesCrearRequis"]);
        }

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, orden = :orden, perfilesCrearRequis = :perfilesCrearRequis WHERE id = :id", $datos, $arrayPDOParam, $error);

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
