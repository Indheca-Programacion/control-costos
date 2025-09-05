<?php

namespace App\Models;

use App\Conexion;
use PDO;

class PuestoSuperiorInferior 
{
    static protected $fillable = [
        'id', 'idPuesto', 'puestoSuperior', 'puestoInferior'
    ];

    static protected $type = [
        'id' => 'integer',
        'idPuesto' => 'integer',
        'puestoSuperior' => 'string',
        'puestoInferior' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "puesto_superior_inferior";

    protected $keyName = "id";

    public $id = null;    

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    OBTENER LOS PUESTOS SUPERIORES E INFERIORES

    FUNCION PARA OBTENER LOS PUESTOS SUPERIORES E
    INFERIORES POR EL ID DE PUESTO.

    RETORNANDO LA RESPUESTA Y ASIGNANDO LOS DATOS
    A LAS VARIBLES
    =============================================*/
    public function consultar($item = null, $valor = null) {


        $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE idPuesto = '$valor'", $error);

        if ( $respuesta ) {
            $this->id = $respuesta["id"];
            $this->idPuesto = $respuesta["idPuesto"];

            // Decodifica el JSON
            $this->puestoSuperior = !is_null($respuesta["puestoSuperior"]) ? json_decode($respuesta["puestoSuperior"], true) : [];
            $this->puestoInferior = !is_null($respuesta["puestoInferior"]) ? json_decode($respuesta["puestoInferior"], true) : [];
            

        }

        return $respuesta;

    }

    public function crear($datos) {

        $datos["puestoSuperior"] = ( isset($datos["puestoSuperior"]) ) ? json_encode( $datos["puestoSuperior"] = explode(",", $datos["puestoSuperior"])) : null;
        $datos["puestoInferior"] = ( isset($datos["puestoInferior"]) ) ? json_encode( $datos["puestoInferior"] = explode(",", $datos["puestoInferior"])) : null;

        $arrayPDOParam = array();        
        $arrayPDOParam["idPuesto"] = self::$type["idPuesto"];
        $arrayPDOParam["puestoSuperior"] = self::$type["puestoSuperior"];
        $arrayPDOParam["puestoInferior"] = self::$type["puestoInferior"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $datos["idPuesto"];

        $datos["puestoSuperior"] = ( isset($datos["puestoSuperior"]) ) ? json_encode( $datos["puestoSuperior"] = explode(",", $datos["puestoSuperior"])) : null;
        $datos["puestoInferior"] = ( isset($datos["puestoInferior"]) ) ? json_encode( $datos["puestoInferior"] = explode(",", $datos["puestoInferior"])) : null;

        $arrayPDOParam = array();        
        $arrayPDOParam["puestoSuperior"] = self::$type["puestoSuperior"];
        $arrayPDOParam["puestoInferior"] = self::$type["puestoInferior"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE idPuesto = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;

    }

}
