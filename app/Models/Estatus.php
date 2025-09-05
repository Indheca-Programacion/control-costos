<?php

namespace App\Models;

if ( file_exists ( "app/Policies/EstatusPolicy.php" ) ) {
    require_once "app/Policies/EstatusPolicy.php";
} else {
    require_once "../Policies/EstatusPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\EstatusPolicy;

class Estatus extends EstatusPolicy
{
    static protected $fillable = [
        'descripcion', 'nombreCorto', 'colorTexto', 'colorFondo', 'obraAbierta', 'obraCerrada', 'requisicionAbierta', 'requisicionCerrada', 'requisicionOrden', 'requisicionAgregarPartidas', 'requisicionUsuarioCreacion'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'colorTexto' => 'string',
        'colorFondo' => 'string',
        'obraAbierta' => 'integer',
        'obraCerrada' => 'integer',
        'requisicionAbierta' => 'integer',
        'requisicionCerrada' => 'integer',
        'requisicionOrden' => 'integer',
        'requisicionAgregarPartidas' => 'integer',
        'requisicionUsuarioCreacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "estatus";

    protected $keyName = "id";

    public $id = null;
    public $descripcion;
    public $nombreCorto;
    public $colorTexto;
    public $colorFondo;
    public $obraAbierta;
    public $obraCerrada;
    public $requisicionAbierta;
    public $requisicionCerrada;
    public $requisicionOrden;
    public $requisicionAgregarPartidas;
    public $requisicionUsuarioCreacion;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR ESTATUS
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT E.* FROM $this->tableName E ORDER BY E.descripcion", $error);

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
                $this->colorTexto = $respuesta["colorTexto"];
                $this->colorFondo = $respuesta["colorFondo"];
                $this->obraAbierta = $respuesta["obraAbierta"];
                $this->obraCerrada = $respuesta["obraCerrada"];
                $this->requisicionAbierta = $respuesta["requisicionAbierta"];
                $this->requisicionCerrada = $respuesta["requisicionCerrada"];
                $this->requisicionOrden = $respuesta["requisicionOrden"];
                $this->requisicionAgregarPartidas = $respuesta["requisicionAgregarPartidas"];
                $this->requisicionUsuarioCreacion = $respuesta["requisicionUsuarioCreacion"];
            }

            return $respuesta;

        }
    }

    static public function cerrado($id): bool
    {
        $respuesta = Conexion::queryUnique(CONST_BD_APP, "SELECT E.obraCerrada FROM estatus E WHERE E.id = {$id}", $error);

        return ( $respuesta ) ? $respuesta['obraCerrada'] : false;
    }

    public function crear($datos)
    {
        // Modificar el contenido de los checkboxes
        $datos["obraAbierta"] = ( isset($datos["obraAbierta"]) && mb_strtolower($datos["obraAbierta"]) == "on" ) ? "1" : "0";
        $datos["obraCerrada"] = ( isset($datos["obraCerrada"]) && mb_strtolower($datos["obraCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAbierta"] = ( isset($datos["requisicionAbierta"]) && mb_strtolower($datos["requisicionAbierta"]) == "on" ) ? "1" : "0";
        $datos["requisicionCerrada"] = ( isset($datos["requisicionCerrada"]) && mb_strtolower($datos["requisicionCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAgregarPartidas"] = ( isset($datos["requisicionAgregarPartidas"]) && mb_strtolower($datos["requisicionAgregarPartidas"]) == "on" ) ? "1" : "0";
        $datos["requisicionUsuarioCreacion"] = ( isset($datos["requisicionUsuarioCreacion"]) && mb_strtolower($datos["requisicionUsuarioCreacion"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["colorTexto"] = self::$type["colorTexto"];
        $arrayPDOParam["colorFondo"] = self::$type["colorFondo"];
        $arrayPDOParam["obraAbierta"] = self::$type["obraAbierta"];
        $arrayPDOParam["obraCerrada"] = self::$type["obraCerrada"];
        $arrayPDOParam["requisicionAbierta"] = self::$type["requisicionAbierta"];
        $arrayPDOParam["requisicionCerrada"] = self::$type["requisicionCerrada"];
        $arrayPDOParam["requisicionOrden"] = self::$type["requisicionOrden"];
        $arrayPDOParam["requisicionAgregarPartidas"] = self::$type["requisicionAgregarPartidas"];
        $arrayPDOParam["requisicionUsuarioCreacion"] = self::$type["requisicionUsuarioCreacion"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto, colorTexto, colorFondo, obraAbierta, obraCerrada, requisicionAbierta, requisicionCerrada, requisicionOrden, requisicionAgregarPartidas, requisicionUsuarioCreacion) VALUES (:descripcion, :nombreCorto, :colorTexto, :colorFondo, :obraAbierta, :obraCerrada, :requisicionAbierta, :requisicionCerrada, :requisicionOrden, :requisicionAgregarPartidas, :requisicionUsuarioCreacion)", $datos, $arrayPDOParam, $error);
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Modificar el contenido de los checkboxes
        $datos["obraAbierta"] = ( isset($datos["obraAbierta"]) && mb_strtolower($datos["obraAbierta"]) == "on" ) ? "1" : "0";
        $datos["obraCerrada"] = ( isset($datos["obraCerrada"]) && mb_strtolower($datos["obraCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAbierta"] = ( isset($datos["requisicionAbierta"]) && mb_strtolower($datos["requisicionAbierta"]) == "on" ) ? "1" : "0";
        $datos["requisicionCerrada"] = ( isset($datos["requisicionCerrada"]) && mb_strtolower($datos["requisicionCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAgregarPartidas"] = ( isset($datos["requisicionAgregarPartidas"]) && mb_strtolower($datos["requisicionAgregarPartidas"]) == "on" ) ? "1" : "0";
        $datos["requisicionUsuarioCreacion"] = ( isset($datos["requisicionUsuarioCreacion"]) && mb_strtolower($datos["requisicionUsuarioCreacion"]) == "on" ) ? "1" : "0";
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["colorTexto"] = self::$type["colorTexto"];
        $arrayPDOParam["colorFondo"] = self::$type["colorFondo"];
        $arrayPDOParam["obraAbierta"] = self::$type["obraAbierta"];
        $arrayPDOParam["obraCerrada"] = self::$type["obraCerrada"];
        $arrayPDOParam["requisicionAbierta"] = self::$type["requisicionAbierta"];
        $arrayPDOParam["requisicionCerrada"] = self::$type["requisicionCerrada"];
        $arrayPDOParam["requisicionOrden"] = self::$type["requisicionOrden"];
        $arrayPDOParam["requisicionAgregarPartidas"] = self::$type["requisicionAgregarPartidas"];
        $arrayPDOParam["requisicionUsuarioCreacion"] = self::$type["requisicionUsuarioCreacion"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, colorTexto = :colorTexto, colorFondo = :colorFondo, obraAbierta = :obraAbierta, obraCerrada = :obraCerrada, requisicionAbierta = :requisicionAbierta, requisicionCerrada = :requisicionCerrada, requisicionOrden = :requisicionOrden, requisicionAgregarPartidas = :requisicionAgregarPartidas, requisicionUsuarioCreacion = :requisicionUsuarioCreacion WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function eliminar()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}
