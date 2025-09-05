<?php

namespace App\Models;

if ( file_exists ( "app/Policies/CargaPolicy.php" ) ) {
    require_once "app/Policies/CargaPolicy.php";
} else {
    require_once "../Policies/CargaPolicy.php";
}

use App\Conexion;
use PDO;

class Carga extends CargaPolicy
{
    static protected $fillable = [
        'id',
        'idObra',
        'idMaterial',
        'idMaquinaria',
        'dFechaHora',
        'nPeso',
        'sFolio'
    ];

    static protected $type = [
        'id' => 'integer',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "COSTOS_04_CARGA";

    protected $keyName = "nId04Carga";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR QR
    =============================================*/

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT 
            MT.nIdQr01Qr AS 'codigo',
            C.nId04Carga idCarga,
            C.nPeso pesoCarga,
            C.dFechaHora AS fechaHoraCarga,
            C.sFolio AS folioCarga,
            O.descripcion AS nombreObra,
            M.sDescripcion AS nombreMaterial,
            MT.sPlaca AS placaMaquinaria,
            MT.sNumeroEconomico AS numeroEconomicoMaquinaria,
            CONCAT(MT.sMarca,' ',MT.sModelo) AS nombreMaquinaria
            FROM 
                COSTOS_04_CARGA C
            LEFT JOIN 
                COSTOS_05_MATERIAL M ON C.nIdMaterial05Material = M.nId05Material
            LEFT JOIN 
                obras O ON C.nIdObra = O.id
            LEFT JOIN 
                COSTOS_02_MAQUINARIA_TRASLADO MT ON C.nIdMaquinariaTraslado02MaquinariaTraslado = MT.nId02MaquinariaTraslado
            LEFT JOIN
                COSTOS_01_QR QR ON QR.nIdMaquinaria02MaquinariaTraslado = MT.nId02MaquinariaTraslado
            ORDER BY
                C.dFechaHora DESC", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                C.nId04Carga idCarga,
                C.nPeso pesoCarga,
                C.dFechaHora AS fechaHoraCarga,
                C.sFolio AS folioCarga,
                C.sUrlTicket AS urlTicket,
                C.nId01Qr AS idQr,
                O.id AS idObra,
                M.sDescripcion AS nombreMaterial,
                MT.nId02MaquinariaTraslado  as idMaquinaria,
                CONCAT(MT.sMarca,' ',MT.sModelo) AS nombreMaquinaria
                FROM 
                    COSTOS_04_CARGA C
                LEFT JOIN 
                    COSTOS_05_MATERIAL M ON C.nIdMaterial05Material = M.nId05Material
                LEFT JOIN 
                    obras O ON C.nIdObra = O.id
                LEFT JOIN 
                COSTOS_02_MAQUINARIA_TRASLADO MT ON C.nIdMaquinariaTraslado02MaquinariaTraslado = MT.nId02MaquinariaTraslado
                WHERE 
                    C.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["idCarga"];                
                $this->pesoCarga = $respuesta["pesoCarga"];
                $this->fechaHoraCarga = $respuesta["fechaHoraCarga"];
                $this->folioCarga = $respuesta["folioCarga"];
                $this->urlTicket = $respuesta["urlTicket"];
                $this->idObra = $respuesta["idObra"];
                $this->nombreMaterial = $respuesta["nombreMaterial"];
                $this->nombreMaquinaria = $respuesta["nombreMaquinaria"];
                $this->idMaquinaria = $respuesta["idMaquinaria"];
                $this->idQr = $respuesta["idQr"];
            }

            return $respuesta;

        }

    }


    public function crear($datos) {

        $idCarga = $datos["idCarga"];

        if($idCarga !== ""){
            Conexion::queryExecute($this->bdName, "UPDATE COSTOS_04_CARGA SET estatus = 'COMPLETADO' WHERE nId04Carga = $idCarga;", [], [], $error);
        }
        
        $arrayPDOParam = array();        
        $arrayPDOParam["nIdObra"] = "integer";
        $arrayPDOParam["nIdMaterial05Material"] = "integer";
        $arrayPDOParam["nIdMaquinariaTraslado02MaquinariaTraslado"] = "integer";
        $arrayPDOParam["nPeso"] = "float";
        $arrayPDOParam["dFechaHora"] = "date";
        $arrayPDOParam["sFolio"] = "string";
        $arrayPDOParam["sUrlTicket"] = "string";
        $arrayPDOParam["nId01Qr"] = "string";
        $arrayPDOParam["estatus"] = "string";

        $datos["nIdObra"] = $datos["idObra"];
        $datos["nIdMaterial05Material"] = $datos["idMaterial"];
        $datos["nIdMaquinariaTraslado02MaquinariaTraslado"] = $datos["idMaquinaria"];
        $datos["nId01Qr"] = $datos["idQrCarga"];
        $datos["estatus"] = "DESACTIVADO";

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {
            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;
        }
        return $respuesta;
    }

    public function verificarCargaActiva($id){

            $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                C.nId04Carga AS idCarga,
                C.nId01Qr AS idQr,
                C.estatus
                FROM 
                    COSTOS_04_CARGA C
                WHERE 
                    C.nId01Qr = $id
                AND
                    C.estatus <> 'COMPLETADO'
                    ", $error);

            $respuestaEnviar = [];

            if($respuesta){
                $respuestaEnviar = [
                    "estatus" => $respuesta["estatus"],
                    "idCarga" => $respuesta["idCarga"]
                ];
            }
        return $respuestaEnviar;
    }
}