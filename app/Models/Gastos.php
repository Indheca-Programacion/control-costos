<?php

namespace App\Models;

if ( file_exists ( "app/Policies/GastosPolicy.php" ) ) {
    require_once "app/Policies/GastosPolicy.php";
} else {
    require_once "../Policies/GastosPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\GastosPolicy;

class Gastos extends GastosPolicy
{
    static protected $fillable = [
        'obra', 'tipoGasto', 'encargado', 'fecha_inicio', 'fecha_fin', 'banco', 'cuenta', 'clave'
    ];

    static protected $type = [
        'id' => 'integer',
        'obra' => 'string',
        'tipoGasto' => 'integer',
        'encargado' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_envio' => 'date',
        'banco' => 'string',
        'cuenta' => 'string',
        'clave' => 'string',
        'cerrada' => 'integer',
        'requisicionId' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdAutorizacion' => 'integer',
        'procesado' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "gastos";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null)
    {
        
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                    "SELECT G.*, O.descripcion,  CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto, G.fecha_inicio, 
                    CASE
                        WHEN G.tipoGasto = 1 THEN 'deducible'
                        WHEN G.tipoGasto = 2 THEN 'no deducible'
                    END AS tipoGasto,
                    CASE
                        WHEN G.procesado = 1 THEN 'EN PROCESO'
                        WHEN G.procesado = 2 THEN 'PROCESADO'
                        WHEN G.procesado = 3 THEN 'PAGADO'
                        WHEN G.requisicionId is not null THEN 'CON REQ.'
                        WHEN G.cerrada = 0 THEN 'ABIERTO'
                        WHEN G.cerrada = 1 THEN 'CERRADO'
                    END AS estatus
                    FROM  $this->tableName G
                    INNER JOIN usuarios E ON E.id = G.encargado
                    INNER JOIN obras O ON O.id = G.obra

                    ORDER BY G.fecha_envio desc", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                        G.* 
                                                                    FROM $this->tableName G
                                                                    INNER JOIN obras O ON O.id = G.obra
                                                                    WHERE G.$this->keyName = $valor
                                                                    ", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                        G.* 
                                                                    FROM $this->tableName G
                                                                    INNER JOIN obras O ON O.id = G.obra
                                                                    WHERE G.$item = '$valor'
                                                                    ", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->requisicionId = $respuesta["requisicionId"];
                $this->obra = $respuesta["obra"];
                $this->tipoGasto = $respuesta["tipoGasto"];
                $this->banco = $respuesta["banco"];
                $this->cuenta = $respuesta["cuenta"];
                $this->cerrada = $respuesta["cerrada"];
                $this->clave = $respuesta["clave"];
                $this->encargado = $respuesta["encargado"];
                $this->usuarioIdAutorizacion = $respuesta["usuarioIdAutorizacion"];
                $this->fecha_inicio = fFechaLarga($respuesta["fecha_inicio"]);
                $this->fecha_fin = $respuesta["fecha_fin"] ? fFechaLarga($respuesta["fecha_fin"]) : null;
                $this->fecha_envio = fFechaLarga($respuesta["fecha_envio"]);
                $this->procesado = $respuesta["procesado"] ?? null;
            }
            return $respuesta;
        }
    }

    public function consultarPorUsuario($id)
    {
        return Conexion::queryAll($this->bdName, 
                    "SELECT G.*,  CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto, G.fecha_inicio, O.descripcion,
                    CASE
                        WHEN G.tipoGasto = 1 THEN 'deducible'
                        WHEN G.tipoGasto = 2 THEN 'no deducible'
                    END AS tipoGasto,
                    CASE
                        WHEN G.procesado = 1 THEN 'EN PROCESO'
                        WHEN G.procesado = 2 THEN 'PROCESADO'
                        WHEN G.procesado = 3 THEN 'PAGADO'
                        WHEN G.requisicionId is not null THEN 'CON REQ.'
                        WHEN G.cerrada = 0 THEN 'ABIERTO'
                        WHEN G.cerrada = 1 THEN 'CERRADO'
                    END AS estatus
                    FROM  $this->tableName G
                    INNER JOIN usuarios E ON E.id = G.encargado
                    INNER JOIN obras O ON O.id = G.obra
                    WHERE G.usuarioIdCreacion = $id OR G.encargado = $id
                    ORDER BY G.fecha_inicio desc
                    ", $error);
    }

    public function crear($datos) {

        // Agregar al request para especificar los segmentos
        $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        $datos["fecha_fin"] = fFechaSQL($datos["fecha_fin"]);
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();        
        $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["tipoGasto"] = self::$type["tipoGasto"];
        $arrayPDOParam["encargado"] = self::$type["encargado"];
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        $arrayPDOParam["banco"] = self::$type["banco"];
        $arrayPDOParam["cuenta"] = self::$type["cuenta"];
        $arrayPDOParam["clave"] = self::$type["clave"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);
    }

    public function actualizar($datos)
    {
        $datos[$this->keyName] = $this->id;
        $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        $datos["fecha_fin"] = fFechaSQL($datos["fecha_fin"]);
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        $arrayPDOParam["encargado"] = self::$type["encargado"];
        $arrayPDOParam["banco"] = self::$type["banco"];
        $arrayPDOParam["cuenta"] = self::$type["cuenta"];
        $arrayPDOParam["clave"] = self::$type["clave"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
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

    public function actualizarRequisicionId($datos)
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function cerrarGasto()
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["cerrada"] = self::$type["cerrada"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        $datos["cerrada"] = 1;
        $datos["fecha_fin"] = date("Y-m-d H:i:s");

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function consultarArchivos($id)
    {
        $query = "SELECT    SA.*, SA.ruta as 'ruta'
                    FROM        gasto_archivos SA
                    INNER JOIN gasto_detalles GD ON GD.id = SA.gastoDetalleId
                    INNER JOIN gastos GA ON GA.id = GD.gastoId
                    WHERE GA.id = $id
                    ORDER BY    SA.id";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    public function consultarArchivosDescargar($id)
    {
        $query = "SELECT    SA.*, SA.ruta as 'ruta'
                    FROM        gasto_archivos SA
                    INNER JOIN gasto_detalles GD ON GD.id = SA.gastoDetalleId
                    INNER JOIN gastos GA ON GA.id = GD.gastoId
                    WHERE GA.id = $id
                    ORDER BY    SA.id";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    public function autorizarGasto()
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["usuarioIdAutorizacion"] = self::$type["usuarioIdAutorizacion"];

        $datos["usuarioIdAutorizacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function actualizarEstatus($datos)
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["procesado"] = self::$type["procesado"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function procesarGasto()
    {
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        $arrayPDOParam["procesado"] = self::$type["procesado"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function enlazarRequisicion()
    {
        $datos[$this->keyName] = $this->id;
        $datos["requisicionId"] = $this->requisicionId;
        $datos["cerrada"] = 1;

        $arrayPDOParam = array();
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["cerrada"] = self::$type["cerrada"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}