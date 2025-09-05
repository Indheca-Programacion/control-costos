<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ObraDetallesPolicy.php" ) ) {
    require_once "app/Policies/ObraDetallesPolicy.php";
} else {
    require_once "../Policies/ObraDetallesPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ObraDetallesPolicy;

class ObraDetalles extends ObraDetallesPolicy{

    static protected $fillable = [
        'obraId', 'insumoId', 'indirectoId', 'cantidad', 'presupuesto', 'presupuesto_dolares'
    ];

    static protected $type = [
        'id' => 'integer',
        'obraId' => 'integer',
        'insumoId' => 'integer',
        'indirectoId' => 'string',
        'cantidad' => 'float',
        'presupuesto' => 'float',
        'presupuesto_dolares' => 'float',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "obra_detalles";

    protected $keyName = "id";

    public $id = null;
    public $obraId;
    public $insumoId;
    public $indireindirectoIdctoId;
    public $cantidad;
    public $presupuesto;
    public $Tipo;

    static public function fillable() {
        return self::$fillable;
    }

    public function crear($datos)
    {
        $arrayPDOParam = array();     
        $arrayPDOParam["obraId"] = self::$type["obraId"];   
        // if(array_key_exists('insumoId', $datos)) $arrayPDOParam["insumoId"] = self::$type["insumoId"] ?? 
        if(isset($datos["indirectoId"])) $arrayPDOParam["indirectoId"] = self::$type["indirectoId"];
        if(isset($datos["insumoId"])) $arrayPDOParam["insumoId"] = self::$type["insumoId"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];
        $arrayPDOParam["presupuesto_dolares"] = self::$type["presupuesto_dolares"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $obradetalleid=0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $obradetalleid);

        if ( $respuesta ) $this->id = $obradetalleid;

        return $respuesta;
    }

    public function agregar($datos)
    {
        $arrayPDOParam = array();     
        $arrayPDOParam["obraId"] = self::$type["obraId"];   
        $arrayPDOParam["indirectoId"] = self::$type["indirectoId"];
        $arrayPDOParam["insumoId"] = self::$type["insumoId"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function consultarDisponiblesPlantilla($obraId,$plantillaId)
    {
        return Conexion::queryAll($this->bdName, 
            "SELECT OD.insumoId, OD.indirectoId, OD.cantidad, OD.presupuesto
            FROM $this->tableName OD
            WHERE OD.obraId = $obraId
            AND NOT EXISTS (
                SELECT 1
                FROM plantilla_detalles PD
                WHERE PD.fk_plantilla = $plantillaId
                AND (
                    (PD.directoId = OD.insumoId)
                    OR (PD.indirectoId = OD.indirectoId)
                )
            )", $error);
    }

    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT od.*, o.nombreCorto 
            FROM $this->tableName 
            INNER JOIN obras o ON o.id = od.obraId", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->obraId = $respuesta["obraId"];
                $this->insumoId = $respuesta["insumoId"];
                $this->indirectoId = $respuesta["indirectoId"];
                $this->cantidad = $respuesta["cantidad"];
                $this->presupuesto = $respuesta["presupuesto"];
                $this->presupuesto_dolares = $respuesta["presupuesto_dolares"];
                if($respuesta["insumoId"] !== null) $this->Tipo = "Directo" ?? $this->Tipo = "Indirecto"; 
            }

            return $respuesta;

        }
    }

    public function consultarPorObra($obra)
    {
        return Conexion::queryAll($this->bdName, 
            "SELECT OD.id, 
                COALESCE(D.descripcion, I.descripcion) AS descripcion,
                COALESCE(UD.descripcion, UI.descripcion) AS unidad
            FROM $this->tableName OD
            LEFT JOIN insumos D ON D.id = OD.insumoId
            LEFT JOIN indirectos I ON I.id = OD.indirectoId
            LEFT JOIN insumo_tipos IT ON IT.id = D.insumoTipoId
            LEFT JOIN unidades UD ON UD.id = D.unidadId
            LEFT JOIN unidades UI ON UI.id = I.unidadId
            WHERE OD.obraId = $obra", $error);
    }

    public function consultarIndirectos()
    {
        return Conexion::queryAll($this->bdName, 
            "SELECT  
                OD.id,
                I.descripcion AS descripcion, 
                I.numero AS codigo,
                IT.descripcion AS tipo, 
                UI.descripcion AS unidad, 
                UD.id AS unidadId,
                OD.cantidad, OD.presupuesto,
                OD.presupuesto - COALESCE((SELECT SUM(p.costo) FROM partidas p WHERE p.obraDetalleId = OD.id),0) AS remanente,
                OD.cantidad - COALESCE((SELECT SUM(p.cantidad) FROM partidas p WHERE p.obraDetalleId = OD.id),0) AS remanente_cantidad

                FROM $this->tableName OD

                INNER JOIN indirectos I ON I.id = OD.indirectoId
                INNER JOIN indirecto_tipos IT ON IT.id = I.indirectoTipoId

                INNER JOIN unidades UI ON UI.id = I.unidadId

                WHERE OD.obraId = $this->obraId
                ORDER BY IT.segmento1, IT.segmento2, I.segmento1, I.segmento2, I.segmento3", $error);
    }

    public function consultarDirectos()
    {
        return Conexion::queryAll($this->bdName, 
            "SELECT 
                OD.id,
                D.descripcion AS descripcion, 
                D.codigo AS codigo,
                CAST(SUBSTRING_INDEX(codigo, '.', 1) AS UNSIGNED) AS nivel1,
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(codigo, '.', 2), '.', -1) AS UNSIGNED) AS nivel2,
                CAST(SUBSTRING_INDEX(codigo, '.', -1) AS UNSIGNED) AS nivel3,
                DT.descripcion AS tipo, 
                UD.descripcion AS unidad, 
                UD.id AS unidadId,
                OD.cantidad, OD.presupuesto,
                OD.presupuesto - COALESCE((SELECT SUM(p.costo) FROM partidas p WHERE p.obraDetalleId = OD.id),0) AS remanente,
                OD.cantidad - COALESCE((SELECT SUM(p.cantidad) FROM partidas p WHERE p.obraDetalleId = OD.id),0) AS remanente_cantidad

                FROM $this->tableName OD

                INNER JOIN insumos D ON D.id = OD.insumoId
                INNER JOIN insumo_tipos DT ON DT.id = D.insumoTipoId

                INNER JOIN unidades UD ON UD.id = D.unidadId

                WHERE OD.obraId = $this->obraId
                ORDER BY D.insumoTipoId, nivel1, nivel2, nivel3;", $error);
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];
        $arrayPDOParam["presupuesto_dolares"] = self::$type["presupuesto_dolares"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function eliminar(){
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}