<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ProgramacionPagosPolicy.php" ) ) {
    require_once "app/Policies/ProgramacionPagosPolicy.php";
} else {
    require_once "../Policies/ProgramacionPagosPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ProgramacionPagosPolicy;

class ProgramacionPagos extends ProgramacionPagosPolicy
{
    static protected $fillable = [
        'fecha_programada', 'prioridad', 'tipo'
    ];

    static protected $type = [
        'id' => 'integer',
        'fecha_programada' => 'datetime',
        'prioridad' => 'integer',
        'tipo' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'pagado' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "programacion_pagos";

    protected $keyName = "id";

    public $id = null;
    public $codigo;
    public $aplicaciones = array();

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR Programacion pagos
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->fecha_programada = $respuesta["fecha_programada"];
                $this->prioridad = $respuesta["prioridad"];
                $this->tipo = $respuesta["tipo"];
            }

            return $respuesta;

        }

    }

    public function consultarBloques() {

        return Conexion::queryAll($this->bdName, "SELECT *, CASE WHEN tipo = 0 THEN 'Contado' WHEN tipo = 1 THEN 'Credito' ELSE 'OTRO' END AS tipoPago FROM $this->tableName ORDER BY prioridad ASC ", $error);

    }

    public function consultarBloquesOrdenesCompra() {

        return Conexion::queryAll($this->bdName, "SELECT * from programacion_pagos_ordenes ", $error);

    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["fecha_programada"] = self::$type["fecha_programada"];
        $arrayPDOParam["prioridad"] = self::$type["prioridad"];
        $arrayPDOParam["tipo"] = self::$type["tipo"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["fecha_programada"] = fFechaSQL($datos["fecha_programada"]);


        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error);

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["fecha_programada"] = self::$type["fecha_programada"];
        $arrayPDOParam["prioridad"] = self::$type["prioridad"];
        $arrayPDOParam["tipo"] = self::$type["tipo"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $datos["fecha_programada"] = fFechaSQL($datos["fecha_programada"]);
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);


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

    public function asignarOrdenesCompra($bloqueId, $ordenes) {

        $arrayPDOParam = array();
        $arrayPDOParam["programacion_pago"] = self::$type["id"];
        $arrayPDOParam["ordenCompraId"] = self::$type["id"];

        $datos = array();
        $datos["programacion_pago"] = $bloqueId;

        foreach ($ordenes as $orden) {
            $datos["ordenCompraId"] = $orden;
            Conexion::queryExecute($this->bdName, "INSERT INTO programacion_pagos_ordenes (programacion_pago, ordenCompraId) VALUES (:programacion_pago, :ordenCompraId)", $datos, $arrayPDOParam, $error);
        }

        return true;

    }

    public function actualizarPrioridades($prioridades) {

        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];
        $arrayPDOParam["prioridad"] = self::$type["prioridad"];

        foreach ($prioridades as $prioridad) {
            $datos = array();
            $datos["id"] = $prioridad["id"];
            $datos["prioridad"] = $prioridad["prioridad"];
            Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET prioridad = :prioridad WHERE id = :id", $datos, $arrayPDOParam, $error);
        }

        return true;

    }

    public function eliminarOrdenDeBloque($bloqueId, $ordenId) {

        $arrayPDOParam = array();
        $arrayPDOParam["programacion_pago"] = self::$type["id"];
        $arrayPDOParam["ordenCompraId"] = self::$type["id"];

        $datos = array();
        $datos["programacion_pago"] = $bloqueId;
        $datos["ordenCompraId"] = $ordenId;

        return Conexion::queryExecute($this->bdName, "DELETE FROM programacion_pagos_ordenes WHERE programacion_pago = :programacion_pago AND ordenCompraId = :ordenCompraId", $datos, $arrayPDOParam, $error);

    }

    public function marcarPagado($bloqueId) {

        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];
        $arrayPDOParam["pagado"] = self::$type["pagado"];

        $datos = array();
        $datos["id"] = $bloqueId;
        $datos["pagado"] = 1;

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET pagado = :pagado WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
