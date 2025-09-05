<?php

namespace App\Models;

if ( file_exists ( "app/Policies/SgiPolicy.php" ) ) {
    require_once "app/Policies/SgiPolicy.php";
} else {
    require_once "../Policies/SgiPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\SgiPolicy;

class Sgi extends SgiPolicy
{
    static protected $fillable = [
        'obra','inventario', 'cantidad', 'fechaAsignacion','usuarioRecibio', 'fk_usuarioEntrego', 'archivos', 'observaciones', 'empresa', 'estatus'
    ];

    static protected $type = [
        'id' => 'integer',
        'usuarioRecibio' => 'integer',
        'empresa' => 'integer',
        'usuarioEntrego' => 'integer',
        'obra' => 'integer',
        'inventario' => 'integer',
        'estatus' => 'integer',
        'cantidad' => 'decimal',
        'observaciones' => 'string',
        'fechaAsignacion' => 'date',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "resguardos";

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

            return Conexion::queryAll($this->bdName, 
                "SELECT R.id, R.cantidad, R.fechaAsignacion, O.descripcion AS 'obra.descripcion', IV.descripcion AS 'inventario.descripcion', 
                CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombre.recibio',
                CONCAT(USEN.nombre, ' ', USEN.apellidoPaterno, ' ', IFNULL(USEN.apellidoMaterno, '')) AS 'nombre.entrego'
                FROM $this->tableName R
                INNER JOIN obras O ON O.id = R.obra
                INNER JOIN inventarios IV ON IV.id = R.inventario
                INNER JOIN usuarios US ON US.id = R.usuarioRecibio
                INNER JOIN usuarios USEN ON USEN.id = R.usuarioEntrego", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT R.*, IV.unidad, IV.descripcion as 'inventario.descripcion' FROM $this->tableName R INNER JOIN inventarios IV ON IV.id = R.inventario WHERE R.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT R.*, IV.unidad, IV.descripcion FROM $this->tableName R INNER JOIN inventarios IV ON IV.id = R.inventario WHERE R.$item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->usuarioEntrego = $respuesta["usuarioEntrego"];
                $this->usuarioRecibio = $respuesta["usuarioRecibio"];
                $this->obra = $respuesta["obra"];
                $this->cantidad = $respuesta["cantidad"];
                $this->fechaAsignacion = fFechaLarga($respuesta["fechaAsignacion"]);
                $this->inventario = $respuesta["inventario"];
                $this->descripcion = $respuesta["inventario.descripcion"];
                $this->observaciones = $respuesta["observaciones"];
                $this->unidad = $respuesta["unidad"];
                $this->empresa = $respuesta["empresa"];
                $this->estatus = $respuesta["estatus"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["inventario"] = self::$type["inventario"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["fechaAsignacion"] = self::$type["fechaAsignacion"];
        $arrayPDOParam["usuarioRecibio"] = self::$type["usuarioRecibio"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["empresa"] = self::$type["empresa"];
        $arrayPDOParam["usuarioEntrego"] = self::$type["usuarioEntrego"];
        $arrayPDOParam["estatus"] = self::$type["estatus"];

        $datos["fechaAsignacion"] = fFechaSQL($datos["fechaAsignacion"]);
        $datos["usuarioEntrego"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;

            if (isset($datos["archivos"]) && $datos['archivos']['name'][0] != '') {
                
                $respuesta = $this->insertarArchivos($datos['archivos']);
            }

        }

        return $respuesta;
    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["estatus"] = self::$type["estatus"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario

            if (isset($datos["archivos"]) && $datos['archivos']['name'][0] != '') {
                
                $respuesta = $this->insertarArchivos($datos['archivos']);
            }

        }

        return $respuesta;


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
