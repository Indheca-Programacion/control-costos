<?php

namespace App\Models;

// require_once "app/conexion.php";
// require_once "app/Policies/SucursalPolicy.php";

if ( file_exists ( "app/Policies/SucursalPolicy.php" ) ) {
    require_once "app/Policies/SucursalPolicy.php";
} else {
    require_once "../Policies/SucursalPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\SucursalPolicy;

class Sucursal extends SucursalPolicy
{
    static protected $fillable = [
        'empresaId', 'descripcion', 'nombreCorto', 'domicilioFiscal', 'calle', 'noExterior', 'noInterior', 'colonia', 'localidad', 'referencia', 'municipio', 'estado', 'pais', 'codigoPostal'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresaId' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'domicilioFiscal' => 'integer',
        'calle' => 'string',
        'noExterior' => 'string',
        'noInterior' => 'string',
        'colonia' => 'string',
        'localidad' => 'string',
        'referencia' => 'string',
        'municipio' => 'string',
        'estado' => 'string',
        'pais' => 'string',
        'codigoPostal' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "sucursales";

    protected $tableNameParent = "empresas";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $descripcion;
    public $nombreCorto;
    public $domicilioFiscal;
    public $calle;
    public $noExterior;
    public $noInterior;
    public $colonia;
    public $localidad;
    public $referencia;
    public $municipio;
    public $estado;
    public $pais;
    public $codigoPostal;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR SUCURSALES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT E.razonsocial AS '$this->tableNameParent.razonSocial', S.* FROM $this->tableName S INNER JOIN $this->tableNameParent E ON S.empresaId = E.id ORDER BY E.razonSocial, S.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = $valor", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresaId = $respuesta["empresaId"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->domicilioFiscal = $respuesta["domicilioFiscal"];
                $this->calle = $respuesta["calle"];
                $this->noExterior = $respuesta["noExterior"];
                $this->noInterior = $respuesta["noInterior"];
                $this->colonia = $respuesta["colonia"];
                $this->localidad = $respuesta["localidad"];
                $this->referencia = $respuesta["referencia"];
                $this->municipio = $respuesta["municipio"];
                $this->estado = $respuesta["estado"];
                $this->pais = $respuesta["pais"];
                $this->codigoPostal = $respuesta["codigoPostal"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        // Modificar el contenido de los checkboxes
        $datos["domicilioFiscal"] = ( isset($datos["domicilioFiscal"]) && mb_strtolower($datos["domicilioFiscal"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["domicilioFiscal"] = self::$type["domicilioFiscal"];
        $arrayPDOParam["calle"] = self::$type["calle"];
        $arrayPDOParam["noExterior"] = self::$type["noExterior"];
        $arrayPDOParam["noInterior"] = self::$type["noInterior"];
        $arrayPDOParam["colonia"] = self::$type["colonia"];
        $arrayPDOParam["localidad"] = self::$type["localidad"];
        $arrayPDOParam["referencia"] = self::$type["referencia"];
        $arrayPDOParam["municipio"] = self::$type["municipio"];
        $arrayPDOParam["estado"] = self::$type["estado"];
        $arrayPDOParam["pais"] = self::$type["pais"];
        $arrayPDOParam["codigoPostal"] = self::$type["codigoPostal"];        

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (empresaId, descripcion, nombreCorto, domicilioFiscal, calle, noExterior, noInterior, colonia, localidad, referencia, municipio, estado, pais, codigoPostal) VALUES (:empresaId, :descripcion, :nombreCorto, :domicilioFiscal, :calle, :noExterior, :noInterior, :colonia, :localidad, :referencia, :municipio, :estado, :pais, :codigoPostal)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Modificar el contenido de los checkboxes
        $datos["domicilioFiscal"] = ( isset($datos["domicilioFiscal"]) && mb_strtolower($datos["domicilioFiscal"]) == "on" ) ? "1" : "0";
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["domicilioFiscal"] = self::$type["domicilioFiscal"];
        $arrayPDOParam["calle"] = self::$type["calle"];
        $arrayPDOParam["noExterior"] = self::$type["noExterior"];
        $arrayPDOParam["noInterior"] = self::$type["noInterior"];
        $arrayPDOParam["colonia"] = self::$type["colonia"];
        $arrayPDOParam["localidad"] = self::$type["localidad"];
        $arrayPDOParam["referencia"] = self::$type["referencia"];
        $arrayPDOParam["municipio"] = self::$type["municipio"];
        $arrayPDOParam["estado"] = self::$type["estado"];
        $arrayPDOParam["pais"] = self::$type["pais"];
        $arrayPDOParam["codigoPostal"] = self::$type["codigoPostal"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, domicilioFiscal = :domicilioFiscal, calle = :calle, noExterior = :noExterior, noInterior = :noInterior, colonia = :colonia, localidad = :localidad, referencia = :referencia, municipio = :municipio, estado = :estado, pais = :pais, codigoPostal = :codigoPostal WHERE id = :id", $datos, $arrayPDOParam, $error);

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
