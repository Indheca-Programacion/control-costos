<?php

namespace App\Models;

// require_once "app/conexion.php";
// require_once "app/Policies/PermisoPolicy.php";

if ( file_exists ( "app/Policies/PermisoPolicy.php" ) ) {
    require_once "app/Policies/PermisoPolicy.php";
} else {
    require_once "../Policies/PermisoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PermisoPolicy;

class Permiso extends PermisoPolicy
{
    static protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'aplicaciones'
    ];

    static protected $type = [
        'id' => 'integer',
        'codigo' => 'string',
        'nombre' => 'string',
        'descripcion' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "permisos";

    protected $keyName = "id";

    public $id = null;
    public $codigo;
    public $nombre;
    public $descripcion;
    public $aplicaciones = array();

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    public function consultar($item = null, $valor = null, $aplicacionId = null) {

        if ( is_null($valor) ) {

            if ( is_null($aplicacionId) ) {

                return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName ORDER BY codigo, nombre", $error);

            } else {

                return Conexion::queryAll($this->bdName, "SELECT P.* FROM $this->tableName P INNER JOIN permiso_aplicaciones PA ON P.id = PA.permisoId AND PA.aplicacionId = $aplicacionId ORDER BY P.codigo, P.nombre", $error);
                
            }

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->codigo = $respuesta["codigo"];
                $this->nombre = $respuesta["nombre"];
                $this->descripcion = $respuesta["descripcion"];
            }

            return $respuesta;

        }

    }

    public function consultarAplicaciones($item = null, $valor = null) {

        $resultado = Conexion::queryAll($this->bdName, "SELECT A.* FROM permiso_aplicaciones PA INNER JOIN aplicaciones A ON A.id = PA.aplicacionId WHERE PA.permisoId = $this->id", $error);
        
        $this->aplicaciones = array_column($resultado, "nombre");

    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["codigo"] = self::$type["codigo"];
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (codigo, nombre, descripcion) VALUES (:codigo, :nombre, :descripcion)", $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el permiso
            $this->id = $lastId;

            $arrayAplicaciones = isset($datos["aplicaciones"]) ? $datos["aplicaciones"] : null;

            if ( $arrayAplicaciones ) {

                $respuesta = $this->actualizarAplicaciones($arrayAplicaciones);

            }

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["codigo"] = self::$type["codigo"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET codigo = :codigo, descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            $arrayAplicaciones = isset($datos["aplicaciones"]) ? $datos["aplicaciones"] : null;

            if ( $this->eliminarAplicaciones() ) {

                if ( $arrayAplicaciones ) {

                    $respuesta = $this->actualizarAplicaciones($arrayAplicaciones);

                }
            
            }

        }

        return $respuesta;

    }

    function eliminarAplicaciones() {

        $eliminar = array();
        $eliminar["permisoId"] = $this->id;
        
        $eliminarPDOParam = array();
        $eliminarPDOParam["permisoId"] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM permiso_aplicaciones WHERE permisoId = :permisoId", $eliminar, $eliminarPDOParam, $error);

    }

    function actualizarAplicaciones(array $aplicaciones = null) {

        $respuesta = false;
    
        if ( $aplicaciones ) {

            foreach ($aplicaciones as $aplicacion) {

                $insertar = array();
                $insertar["permisoId"] = $this->id;
                $insertar["aplicacion"] = $aplicacion;

                $insertarPDOParam = array();
                $insertarPDOParam["permisoId"] = self::$type[$this->keyName];
                $insertarPDOParam["aplicacion"] = "string";

                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO permiso_aplicaciones (permisoId, aplicacionId) VALUES (:permisoId, (SELECT id FROM aplicaciones WHERE nombre = :aplicacion))", $insertar, $insertarPDOParam, $error);

            }
            
        }

        return $respuesta;

    }

    public function eliminar() {

        if ( $this->eliminarAplicaciones() ) {

            // Agregar al request para eliminar el registro
            $datos = array();
            $datos[$this->keyName] = $this->id;
            
            $arrayPDOParam = array();
            $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

            return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

        } else {

            return false;

        }

    }
}
