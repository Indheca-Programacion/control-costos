<?php

namespace App\Models;

if ( file_exists ( "app/Policies/DatosBancariosPolicy.php" ) ) {
    require_once "app/Policies/DatosBancariosPolicy.php";
} else {
    require_once "../Policies/DatosBancariosPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\DatosBancariosPolicy;

class DatosBancarios extends DatosBancariosPolicy
{
    static protected $fillable = [
        'id', 'nombreTitular', 'nombreBanco', 'cuenta', 'cuentaClave','proveedorId','fechaCreacion','fechaActualizacion', 'divisaId','datoBancarioId'
    ];

    static protected $type = [
        'id' => 'integer',
        'nombreTitular' => 'varchar',
        'nombreBanco' => 'varchar',
        'cuenta' => 'varchar',
        'cuentaClave' => 'varchar',
        'proveedorId' => 'integer',
        'fechaCreacion' => 'date',
        'fechaActualizacion' => 'date',
        'divisaId' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "datos_bancarios_proveedores";    

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                "SELECT *
                FROM $this->tableName DBP", $error);
        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT* FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->nombreTitular = $respuesta["nombreTitular"];
                $this->nombreBanco = $respuesta["nombreBanco"];
                $this->numeroCuenta = $respuesta["cuenta"];
                $this->cuentaClave = $respuesta["cuentaClave"];
                $this->proveedorId = $respuesta["proveedorId"];
                $this->divisaId = $respuesta["divisaId"];
            }

            return $respuesta;

        }

    }

    // DATOS BANCARIOS POR ID DEL PROVEEDOR
    public function consultarDatosBancariosProveedor($proveedorId) {

        return Conexion::queryAll($this->bdName, 
                                        "SELECT DBP.*, D.descripcion as divisa, D.nombreCorto as divisaCorto
                                            FROM $this->tableName DBP
                                            inner join divisas D on DBP.divisaId = D.id
                                            WHERE proveedorId = '$proveedorId'", $error);

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["nombreTitular"] = self::$type["nombreTitular"];
        $arrayPDOParam["nombreBanco"] = self::$type["nombreBanco"];
        $arrayPDOParam["cuenta"] = self::$type["cuenta"];
        $arrayPDOParam["cuentaClave"] = self::$type["cuentaClave"];
        $arrayPDOParam["fechaCreacion"] = self::$type["fechaCreacion"];
        $arrayPDOParam["divisaId"] = self::$type["divisaId"];

        $lastId = 0;
		$datos["fechaCreacion"] = date("Y-m-d H:i:s");

        $columna=fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $columna, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {
            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        

        $arrayPDOParam = [];
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam['nombreTitular'] = self::$type['nombreTitular'];
        $arrayPDOParam['nombreBanco']   = self::$type['nombreBanco'];
        $arrayPDOParam['cuenta']        = self::$type['cuenta'];
        $arrayPDOParam['cuentaClave']   = self::$type['cuentaClave'];
        $arrayPDOParam['divisaId']      = self::$type['divisaId'];

        $sql = "
            UPDATE {$this->tableName}
            SET nombreTitular = :nombreTitular,
                nombreBanco   = :nombreBanco,
                cuenta        = :cuenta,
                cuentaClave   = :cuentaClave,
                divisaId      = :divisaId
            WHERE {$this->keyName} = :{$this->keyName}
        ";

        return Conexion::queryExecute(
            $this->bdName,
            $sql,
            $datos,          // si usas bind por orden, alinea $datos con el nuevo SQL
            $arrayPDOParam,
            $error
        );
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
