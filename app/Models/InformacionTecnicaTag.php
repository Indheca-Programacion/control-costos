<?php

namespace App\Models;

if ( file_exists ( "app/Policies/InformacionTecnicaTagPolicy.php" ) ) {
    require_once "app/Policies/InformacionTecnicaTagPolicy.php";
} else {
    require_once "../Policies/InformacionTecnicaTagPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\InformacionTecnicaTagPolicy;

class InformacionTecnicaTag extends InformacionTecnicaTagPolicy 
{
    static protected $fillable = [
        'descripcion', 'nombreCorto', 'orden'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'orden' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "informacion_tecnica_tags";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;
    public $orden;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TAGS DE INFORMACION TECNICA
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            // return Conexion::queryAll($this->bdName, "SELECT ITT.* FROM $this->tableName ITT ORDER BY ITT.descripcion", $error);
            return Conexion::queryAll($this->bdName, "SELECT ITT.* FROM $this->tableName ITT ORDER BY ITT.orden", $error);

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
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["orden"] = self::$type["orden"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto, orden) VALUES (:descripcion, :nombreCorto, :orden)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["orden"] = self::$type["orden"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, orden = :orden WHERE id = :id", $datos, $arrayPDOParam, $error);

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
