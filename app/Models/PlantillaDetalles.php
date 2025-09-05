<?php

namespace App\Models;

use App\Conexion;
use PDO;

class PlantillaDetalles
{
    static protected $fillable = [
        'presupuesto', 'cantidad', 'indirectoId', 'directoId', 'fk_plantilla'
    ];

    static protected $type = [
        'id' => 'integer',
        'presupuesto' => 'float',
        'cantidad' => 'float',
        'indirectoId' => 'integer',
        'directoId' => 'integer',
        'fk_plantilla' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "plantilla_detalles";

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

            return Conexion::queryAll($this->bdName, "SELECT PD.id, PD.cantidad, PD.presupuesto, COALESCE(D.descripcion,I.descripcion) AS descripcion,
                                                case 
                                                    when D.id is NULL then 'Indirecto'
                                                    ELSE 'Directo'
                                                END AS tipo
                                                FROM $this->tableName PD
                                                LEFT JOIN insumos D ON D.id = PD.directoId
                                                LEFT JOIN indirectos I ON I.id = PD.indirectoId
                                                WHERE PD.fk_plantilla = $id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, 
                                        "SELECT PD.id, PD.cantidad, PD.presupuesto, PD.fk_plantilla, COALESCE(D.descripcion,I.descripcion) AS descripcion,
                                        case 
                                            when D.id is NULL then 'Indirecto'
                                            ELSE 'Directo'
                                        END AS tipo
                                        FROM $this->tableName PD
                                        LEFT JOIN insumos D ON D.id = PD.directoId
                                        LEFT JOIN indirectos I ON I.id = PD.indirectoId
                                        WHERE PD.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->tipo = $respuesta["tipo"];
                $this->plantilla = $respuesta["fk_plantilla"];
                $this->descripcion = $respuesta["descripcion"];
                $this->presupuesto = $respuesta["presupuesto"];
                $this->cantidad = $respuesta["cantidad"];
            }

            return $respuesta;

        }

    }

    public function crear($datos)
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
        $datos["presupuesto"] = str_replace(",", "", $datos["presupuesto"]);;
        $datos["cantidad"] = str_replace(",", "", $datos["cantidad"]);;
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];

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
