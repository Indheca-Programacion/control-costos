<?php

namespace App\Models;

if ( file_exists ( "app/Policies/NominaPolicy.php" ) ) {
    require_once "app/Policies/NominaPolicy.php";
} else {
    require_once "../Policies/NominaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\NominaPolicy;

class Nominas extends NominaPolicy
{
    static protected $fillable = [
        'datos', 'semana', 'obraId','filtroObraId'
    ];

    static protected $type = [
        'id' => 'integer',
        'fk_obraId' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'primas' => 'integer',
        'comida' => 'integer',
        'prestamos' => 'integer',
        'semana' => 'integer',
        'descuentos' => 'integer',
        'pension' => 'integer',
        'neto' => 'integer',
        'fk_empleadoId' => 'integer',
        'fk_nominaId' => 'integer',
        'fk_obraDetalleId' => 'integer',
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

    public function getPuesto($idObra){
        return Conexion::queryAll($this->bdName, 
            "SELECT OD.id as id, O.periodos , I.descripcion AS descripcionI, I.indirectoTipoId , D.descripcion AS descripcionD, D.insumoTipoId, O.fechaInicio
            FROM obra_detalles OD 
            INNER JOIN obras O on O.id = OD.obraId
            LEFT JOIN insumos D ON D.id = OD.insumoId
            LEFT JOIN indirectos I ON I.id = OD.indirectoId
            WHERE obraId =  $idObra AND (I.indirectoTipoId = 6 OR D.insumoTipoId = 10)");
    }

    public function consultarPorObra($idobra){
        return Conexion::queryAll($this->bdName,"SELECT ND.neto as costo, ND.fk_obraDetalleId as obraDetalleId , OD.insumoId, OD.indirectoId, N.semana as periodo, OD.presupuesto, N.id
                                                FROM nominas_detalles ND
                                                INNER JOIN nominas N ON N.id = ND.fk_nominaId
                                                INNER JOIN obra_detalles OD ON OD.id = ND.fk_obraDetalleId
                                                INNER JOIN obras O ON O.id = OD.obraId
                                                WHERE O.id = $idobra");
    }

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

    public function crearDetalle($datos){

        $arrayPDOParam = array();
        $arrayPDOParam["fk_nominaId"] = self::$type["fk_nominaId"];
        $arrayPDOParam["primas"] = self::$type["primas"];
        $arrayPDOParam["comida"] = self::$type["comida"];
        $arrayPDOParam["prestamos"] = self::$type["prestamos"];
        $arrayPDOParam["descuentos"] = self::$type["descuentos"];
        $arrayPDOParam["pension"] = self::$type["pension"];
        $arrayPDOParam["neto"] = self::$type["neto"];
        $arrayPDOParam["salario"] = self::$type["salario"];
        $arrayPDOParam["hrsExtras"] = self::$type["hrsExtras"];
        $arrayPDOParam["fk_obraDetalleId"] = self::$type["fk_obraDetalleId"];
        $arrayPDOParam["fk_empleadoId"] = self::$type["fk_empleadoId"];
        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO nominas_detalles ". $campos, $datos, $arrayPDOParam, $error);
    }
}
