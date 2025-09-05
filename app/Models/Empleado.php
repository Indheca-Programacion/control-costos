<?php

namespace App\Models;

if ( file_exists ( "app/Policies/EmpleadoPolicy.php" ) ) {
    require_once "app/Policies/EmpleadoPolicy.php";
} else {
    require_once "../Policies/EmpleadoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\EmpleadoPolicy;

class Empleado extends EmpleadoPolicy
{
    static protected $fillable = [
        'activo', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'correo', 'foto', 'fotoAnterior', 'funciones', 'rfc', 'seguro', 'cbanco', 'curp'
    ];

    static protected $type = [
        'id' => 'integer',
        'activo' => 'integer',
        'nombre' => 'string',
        'apellidoPaterno' => 'string',
        'apellidoMaterno' => 'string',
        'correo' => 'string',
        'foto' => 'string',
        'funciones' => 'string',
        'rfc' => 'string',
        'seguro' => 'integer',
        'cbanco' => 'integer',
        'curp' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "empleados";    

    protected $keyName = "id";

    public $id = null;
    public $activo;
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $correo;
    public $foto;
    public $funciones = array();
    public $curp;
    public $rfc;
    public $seguro;
    public $cbanco;
    public $nombreCompleto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR EMPLEADOS ACTIVOS
    =============================================*/
    public function consultarActivos()
    {
        return Conexion::queryAll($this->bdName, "SELECT E.*, CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS 'nombreCompleto' FROM {$this->tableName} E WHERE E.activo = 1 ORDER BY E.apellidoPaterno, E.apellidoMAterno, E.nombre", $error);
    }

    /*=============================================
    MOSTRAR EMPLEADOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT E.*, CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS 'nombreCompleto' FROM {$this->tableName} E ORDER BY E.apellidoPaterno, E.apellidoMAterno, E.nombre", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT E.* FROM {$this->tableName} E WHERE E.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT E.* FROM {$this->tableName} E WHERE E.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->activo = $respuesta["activo"];
                $this->nombre = $respuesta["nombre"];
                $this->apellidoPaterno = $respuesta["apellidoPaterno"];
                $this->apellidoMaterno = $respuesta["apellidoMaterno"];
                $this->correo = $respuesta["correo"];
                $this->foto = $respuesta["foto"];
                $this->funciones = json_decode($respuesta["funciones"]);
                $this->curp = $respuesta["curp"];
                $this->seguro = $respuesta["seguro"];
                $this->cbanco = $respuesta["cbanco"];
                $this->rfc = $respuesta["rfc"];
                
                $this->nombreCompleto = fNombreCompleto($respuesta["nombre"], $respuesta["apellidoPaterno"], $respuesta["apellidoMaterno"]);
            }

            return $respuesta;

        }

    }

    public function consultarEmpleados($array){
        return Conexion::queryAll($this->bdName, "SELECT E.*, CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto from $this->tableName E where $this->keyName in ($array)");
    }

    public function crear($datos) {

        // Modificar el contenido de los checkboxes
        $datos["activo"] = ( isset($datos["activo"]) && mb_strtolower($datos["activo"]) == "on" ) ? "1" : "0";

        // Agregar al request la ruta final de la foto
        $ruta = "";
        if ( $datos["foto"]["tmp_name"] != "" ) {

            $tmpName = $datos["foto"]["tmp_name"];
            $tipo = $datos["foto"]["type"];

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $directorio = "vistas/img/empleados/";

            do {
                $ruta = fRandomNameImageFile($directorio, $tipo);
            } while ( file_exists($ruta) );

        }
        // Request con el nombre del archivo
        $datos["foto"] = $ruta;

        // Convertir el array funciones a formato JSON
        $datos["funciones"] = ( isset($datos["funciones"]) ) ? json_encode($datos["funciones"]) : "[]";

        $arrayPDOParam = array();
        $arrayPDOParam["activo"] = self::$type["activo"];
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
        $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["foto"] = self::$type["foto"];
        $arrayPDOParam["funciones"] = self::$type["funciones"];
        $arrayPDOParam["curp"] = self::$type["curp"];
        $arrayPDOParam["rfc"] = self::$type["rfc"];
        $arrayPDOParam["cbanco"] = self::$type["cbanco"];
        $arrayPDOParam["seguro"] = self::$type["seguro"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO {$this->tableName} {$campos}", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            if ( $ruta != "" ) fSaveImageFile($tmpName, $tipo, $datos["foto"]);

        }

        return $respuesta;

    }

    public function actualizar($datos) {
        
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Modificar el contenido de los checkboxes
        $datos["activo"] = ( isset($datos["activo"]) && mb_strtolower($datos["activo"]) == "on" ) ? "1" : "0";
        
        // Agregar al request la ruta final de la foto
        $tmpName = $datos["foto"]["tmp_name"];
        $tipo = $datos["foto"]["type"];

        if ( $datos["foto"]["tmp_name"] == "" ) {

            // Si no viene una nueva imágen dejamos la anterior
            $datos["foto"] = $datos["fotoAnterior"];

        } else {

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $ruta = "";
            $directorio = "vistas/img/empleados/";

            do {
                $ruta = fRandomNameImageFile($directorio, $tipo);
            } while ( file_exists($ruta) );

            // Si viene una nueva imágen renombramos el Request con el nombre del archivo
            $datos["foto"] = $ruta;

        }

        // Convertir el array funciones a formato JSON
        $datos["funciones"] = ( isset($datos["funciones"]) ) ? json_encode($datos["funciones"]) : "[]";

        $arrayPDOParam = array();
        $arrayPDOParam["activo"] = self::$type["activo"];
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
        $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["foto"] = self::$type["foto"];
        $arrayPDOParam["funciones"] = self::$type["funciones"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE {$this->tableName} SET {$campos} WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            // Si viene una imagen en el POST foto actualizarla físicamente
            if ( $tmpName != "" ) fSaveImageFile($tmpName, $tipo, $datos["foto"], $datos["fotoAnterior"]);

        }

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM {$this->tableName} WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta && !is_null($this->foto) ) {

            // Eliminar físicamente la foto (si tiene)
            fDeleteFile($this->foto);

        }

        return $respuesta;

    }
}
