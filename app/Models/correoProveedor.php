<?php

namespace App\Models;

use App\Conexion;
use PDO;

class correoProveedor
{
    //No se usa por que 
    static protected $fillable = [
        'descripcion', 'nombreCorto'
    ];

    static protected $type = [
        'id' => 'integer',
        'requisicionId' => 'integer',
        'empresaId' => 'integer',
        'ordenCompraId' => 'string',
        'correo' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "correoproveedores";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR UNIDADES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT U.* FROM $this->tableName U ORDER BY U.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->requisicionId = $respuesta["requisicionId"];
                $this->empresaId = $respuesta["empresaId"];
                $this->ordenCompra = $respuesta["ordenCompra"];
                $this->correo = $respuesta["correo"];
                $this->estatus = $respuesta["estatus"];
            }

            return $respuesta;

        }

    }

    public function crear() {

        $arrayPDOParam = array();        
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["ordenCompra"] = self::$type["ordenCompraId"];
        $arrayPDOParam["correo"] = self::$type["correo"];

        $datos = array();
        $datos["requisicionId"] = $this->requisicionId;
        $datos["empresaId"] = $this->empresaId;
        $datos["ordenCompra"] = $this->ordenCompra;
        $datos["correo"] = $this->correo;

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos , $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {
            $this->id = $lastId;
        }
        return $respuesta;

    }

    public function actualizar() {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET estatus = 1 WHERE id = :id", $datos, $arrayPDOParam, $error);

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
