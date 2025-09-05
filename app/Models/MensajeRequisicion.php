<?php

namespace App\Models;

use App\Conexion;
use PDO;

class MensajeRequisicion 
{
    static protected $fillable = [
        'id',
        'mensaje',
        'idRequisicion',
        'idUsuario',
        'fechaEnviado'
    ];

    static protected $type = [
        'id' => 'integer',
        'mensaje' => 'string',
        'idRequisicion' => 'integer',
        'idUsuario' => 'integer',
        'fechaEnviado' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "mensaje_requisiciones";

    protected $keyName = "id";

    public $id = null;
    public $mensaje;
    public $idRequisicion;
    public $idUsuario;
    public $fechaEnviado;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MENSAJES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        return  Conexion::queryAll($this->bdName, "SELECT 
                                                        MR.*, 
                                                        CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS nombreCompleto
                                                    FROM 
                                                        $this->tableName  MR
                                                    LEFT JOIN usuarios U ON MR.idUsuario = U.id
                                                    WHERE
                                                        MR.idRequisicion = '$valor'", $error,);
    }
    
    public function crear($datos) {
        
        // Agregar al request para especificar el usuario que creó el mensaje
        $datos["idUsuario"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();        
        $arrayPDOParam["mensaje"] = self::$type["mensaje"];
        $arrayPDOParam["idRequisicion"] = self::$type["idRequisicion"];
        $arrayPDOParam["idUsuario"] = self::$type["idUsuario"];

         $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (idRequisicion, mensaje, idUsuario) VALUES (:idRequisicion ,:mensaje, :idUsuario)", $datos, $arrayPDOParam, $error);

    }

}
