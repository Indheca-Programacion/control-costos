<?php

namespace App\Models;

if ( file_exists ( "app/Policies/Nom35Policy.php" ) ) {
    require_once "app/Policies/Nom35Policy.php";
} else {
    require_once "../Policies/Nom35Policy.php";
}

use App\Conexion;
use PDO;
use App\Policies\Nom35Policy;

class Nom35 extends Nom35Policy
{
    static protected $fillable = [
        'id'
    ];

    static protected $type = [
        'id' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "nominas";

    protected $keyName = "id";

    public $id = null;    
    public $nombre;
    public $costo;
    public $obraDetalleId;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR APLICACIONES
    =============================================*/

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT  N.id, N.fechaCreacion, N.semana, U.nombre, U.apellidoPaterno, U.apellidoMaterno, O.descripcion, N.fk_obraId
                                                    FROM $this->tableName N
                                                    INNER JOIN usuarios U ON U.id = N.usuarioIdCreacion
                                                    INNER JOIN obras O ON O.id = N.fk_obraId
                                                    ORDER BY N.fk_obraId, N.semana", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            // if ( $respuesta ) {
            //     $this->id = $respuesta["id"];
            // }

            return $respuesta;

        }

    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["fk_obraId"] = self::$type["fk_obraId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["semana"] = self::$type["semana"];
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);
        
        $NominaId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ". $campos, $datos, $arrayPDOParam, $error, $NominaId);
        if ( $respuesta ){
            $this->id = $NominaId;
        } 
        return $respuesta;
    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["fk_obraDetalle"] = self::$type["fk_obraDetalle"];
        
        $campos = fCreaCamposUpdate($arrayPDOParam);
        
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

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

