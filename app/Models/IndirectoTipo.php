<?php

namespace App\Models;

if ( file_exists ( "app/Policies/IndirectoTipoPolicy.php" ) ) {
    require_once "app/Policies/IndirectoTipoPolicy.php";
} else {
    require_once "../Policies/IndirectoTipoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\IndirectoTipoPolicy;

class IndirectoTipo extends IndirectoTipoPolicy
{
    static protected $fillable = [
        'numero', 'descripcion', 'nombreCorto', 'perfilesCrearRequis'
    ];

    static protected $type = [
        'id' => 'integer',
        'numero' => 'string',
        'segmento1' => 'integer',
        'segmento2' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'perfilesCrearRequis' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "indirecto_tipos";

    protected $keyName = "id";

    public $id = null;    
    public $numero;
    public $segmento1;
    public $segmento2;
    public $descripcion;
    public $nombreCorto;
    public $perfilesCrearRequis;
    
    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE INDIRECTOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT IT.* FROM $this->tableName IT ORDER BY IT.segmento1, IT.segmento2", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->numero = $respuesta["numero"];
                $this->segmento1 = $respuesta["segmento1"];
                $this->segmento2 = $respuesta["segmento2"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->perfilesCrearRequis = json_decode($respuesta["perfilesCrearRequis"]);
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arraySegmentos = explode(".", str_replace('_', '', $datos["numero"]));
        $segmento1 = intval($arraySegmentos[0]);
        $segmento2 = intval($arraySegmentos[1]);

        // Agregar al request para especificar los segmentos        
        $datos["numero"] = "{$segmento1}.{$segmento2}";
        $datos["segmento1"] = $segmento1;
        $datos["segmento2"] = $segmento2;

        $arrayPDOParam = array();        
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["segmento1"] = self::$type["segmento1"];
        $arrayPDOParam["segmento2"] = self::$type["segmento2"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["perfilesCrearRequis"] = self::$type["perfilesCrearRequis"];

        if (!isset($datos["perfilesCrearRequis"])) {
            $datos["perfilesCrearRequis"] = "[]";
        }else{
            $datos["perfilesCrearRequis"] = json_encode($datos["perfilesCrearRequis"]);
        }

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (numero, segmento1, segmento2, descripcion, nombreCorto, perfilesCrearRequis) VALUES (:numero, :segmento1, :segmento2, :descripcion, :nombreCorto, :perfilesCrearRequis)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arraySegmentos = explode(".", str_replace('_', '', $datos["numero"]));
        $segmento1 = intval($arraySegmentos[0]);
        $segmento2 = intval($arraySegmentos[1]);

        // Agregar al request para especificar los segmentos        
        $datos["numero"] = "{$segmento1}.{$segmento2}";
        $datos["segmento1"] = $segmento1;
        $datos["segmento2"] = $segmento2;
        if (!isset($datos["perfilesCrearRequis"])) {
            $datos["perfilesCrearRequis"] = "[]";
        }else{
            $datos["perfilesCrearRequis"] = json_encode($datos["perfilesCrearRequis"]);
        }

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["segmento1"] = self::$type["segmento1"];
        $arrayPDOParam["segmento2"] = self::$type["segmento2"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["perfilesCrearRequis"] = self::$type["perfilesCrearRequis"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET numero = :numero, segmento1 = :segmento1, segmento2 = :segmento2, descripcion = :descripcion, nombreCorto = :nombreCorto, perfilesCrearRequis = :perfilesCrearRequis  WHERE id = :id", $datos, $arrayPDOParam, $error);

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
