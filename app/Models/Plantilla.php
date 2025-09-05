<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PlantillaPolicy.php" ) ) {
    require_once "app/Policies/PlantillaPolicy.php";
} else {
    require_once "../Policies/PlantillaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PlantillaPolicy;

class Plantilla extends PlantillaPolicy
{
    static protected $fillable = [
        'nombreCorto', 'descripcion', 'presupuesto', 'cantidad', 'indirectoId', 'directoId', 'fk_plantilla'
    ];

    static protected $type = [
        'id' => 'integer',
        'nombreCorto' => 'string',
        'descripcion' => 'string',
        'usuarioIdCreacion' => 'integer',
        'presupuesto' => 'float',
        'cantidad' => 'float',
        'indirectoId' => 'integer',
        'fk_plantilla' => 'integer',
        'directoId' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "plantillas";

    protected $keyName = "id";

    public $id = null;
    public $codigo;
    public $aplicaciones = array();

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PERMISOS
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
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->descripcion = $respuesta["descripcion"];
                $this->usuarioCreacion = $respuesta["usuarioIdCreacion"];
            }

            return $respuesta;

        }

    }

    public function consultarPorUsuario($id)
    {
        return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName WHERE usuarioIdCreacion = $id", $error);
    }

    /*=============================================
    BUSCA LOS DETALLES QUE NO HAN SIDO AGREGADOS
    =============================================*/
    public function consultarDisponiblesObra($id){
        return Conexion::queryAll($this->bdName, "SELECT PD.*
                                                FROM plantilla_detalles PD
                                                WHERE PD.fk_plantilla = $this->id
                                                AND NOT EXISTS (
                                                    SELECT 1
                                                    FROM obra_detalles OD
                                                    WHERE obraId = $id
                                                    AND (
                                                        (PD.directoId = OD.insumoId )
                                                        OR (PD.indirectoId = OD.indirectoId)
                                                    )
                                                )", $error);
    }

    public function consultarDetalles($id){
        return Conexion::queryAll($this->bdName, "SELECT PD.id, PD.directoId, PD.indirectoId, PD.cantidad, PD.presupuesto, COALESCE(D.descripcion,I.descripcion) AS descripcion,
                                                case 
                                                    when D.id is NULL then 'Indirecto'
                                                    ELSE 'Directo'
                                                END AS tipo
                                                FROM plantilla_detalles PD
                                                LEFT JOIN insumos D ON D.id = PD.directoId
                                                LEFT JOIN indirectos I ON I.id = PD.indirectoId
                                                WHERE PD.fk_plantilla = $id 
                                                ORDER BY tipo", $error);
    }

    public function consultarDetalle($id)
    {
        return Conexion::queryAll($this->bdName,"SELECT * FROM plantilla_detalles WHERE PD.fk_plantilla = $id", $error);
    }

    public function consultarDetallesParaObra(){
        return Conexion::queryAll($this->bdName, "SELECT PD.id, PD.cantidad, PD.presupuesto, COALESCE(D.descripcion,I.descripcion) AS descripcion,
                                                case 
                                                    when D.id is NULL then 'Indirecto'
                                                    ELSE 'Directo'
                                                END AS tipo
                                                FROM plantilla_detalles PD
                                                LEFT JOIN insumos D ON D.id = PD.directoId
                                                LEFT JOIN indirectos I ON I.id = PD.indirectoId
                                                WHERE PD.fk_plantilla = $id 
                                                ORDER BY tipo", $error);
    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error);

        return $respuesta;

    }

    public function crearDetalle($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["fk_plantilla"] = self::$type["fk_plantilla"];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        if(isset($datos["indirectoId"])) $arrayPDOParam["indirectoId"] = self::$type["indirectoId"];
        if(isset($datos["directoId"])) $arrayPDOParam["directoId"] = self::$type["directoId"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO plantilla_detalles ".$campos, $datos, $arrayPDOParam, $error, $lastId);

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
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

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
}
