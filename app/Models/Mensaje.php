<?php

namespace App\Models;

// if ( file_exists ( "app/Policies/MensajePolicy.php" ) ) {
//     require_once "app/Policies/MensajePolicy.php";
// } else {
//     require_once "../Policies/MensajePolicy.php";
// }

use App\Conexion;
use PDO;
// use App\Policies\MensajePolicy;

// class Mensaje extends MensajePolicy
class Mensaje
{
    static protected $fillable = [
        'mensajeTipoId', 'mensajeEstatusId', 'asunto', 'correo', 'mensaje', 'liga', 'error'
    ];

    static protected $type = [
        'id' => 'integer',
        'mensajeTipoId' => 'integer',
        'mensajeEstatusId' => 'integer',
        'asunto' => 'string',
        'correo' => 'string',
        'mensaje' => 'string',
        'liga' => 'string',
        'error' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "mensajes";

    protected $keyName = "id";

    public $id = null;
    public $mensajeTipoId;
    public $mensajeEstatusId;
    public $asunto;
    public $correo;
    public $mensaje;
    public $liga;
    public $error;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MENSAJES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            // $query = "SELECT    M.*,
            //                     MT.nombreCorto AS 'mensaje_tipos.nombreCorto',
            //                     ME.nombreCorto AS 'mensaje_estatus.nombreCorto', ME.envio AS 'mensaje_estatus.envio',
            //                     CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'usuarios.nombreCompleto'
            //         FROM        {$this->tableName} M
            //         INNER JOIN  mensaje_tipos MT ON M.mensajeTipoId = MT.id
            //         INNER JOIN  mensaje_estatus ME ON M.mensajeEstatusId = ME.id
            //         INNER JOIN  usuarios U ON M.usuarioIdCreacion = U.id
            //         ORDER BY    M.id DESC";

            // GROUP_CONCAT(MD.correo ORDER BY MD.correo SEPARATOR '\n') AS 'correos_destinos'
            $query = "SELECT    M.*,
                                MT.nombreCorto AS 'mensaje_tipos.nombreCorto', MT.descripcion AS 'mensaje_tipos.descripcion',
                                ME.nombreCorto AS 'mensaje_estatus.nombreCorto', ME.descripcion AS 'mensaje_estatus.descripcion', ME.envio AS 'mensaje_estatus.envio',
                                CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'usuarios.nombreCompleto',
                                GROUP_CONCAT(MD.correo ORDER BY MD.correo SEPARATOR ', ') AS 'correos_destinos'
                    FROM        {$this->tableName} M
                    INNER JOIN  mensaje_tipos MT ON M.mensajeTipoId = MT.id
                    INNER JOIN  mensaje_estatus ME ON M.mensajeEstatusId = ME.id
                    LEFT JOIN  usuarios U ON M.usuarioIdCreacion = U.id
                    INNER JOIN  mensaje_destinatarios MD ON M.id = MD.mensajeId
                    GROUP BY    M.id
                    ORDER BY    M.id DESC";

            return Conexion::queryAll($this->bdName, $query, $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT M.*, CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'usuarios.nombreCompleto' FROM {$this->tableName} M LEFT JOIN   usuarios U ON M.usuarioIdCreacion = U.id WHERE M.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT M.*, CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'usuarios.nombreCompleto' FROM {$this->tableName} M LEFT JOIN   usuarios U ON M.usuarioIdCreacion = U.id WHERE M.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->mensajeTipoId = $respuesta["mensajeTipoId"];
                $this->mensajeEstatusId = $respuesta["mensajeEstatusId"];
                $this->asunto = $respuesta["asunto"];
                $this->correo = $respuesta["correo"];
                $this->mensaje = $respuesta["mensaje"];
                $this->liga = $respuesta["liga"];
                $this->error = $respuesta["error"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->nombreCompleto = $respuesta["usuarios.nombreCompleto"];

                $this->consultarDestinatarios();
            }

            return $respuesta;
            die;
        }

    }

    public function consultarDestinatarios() {

        $query = "SELECT    MD.*,
                            CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'usuarios.nombreCompleto'
                FROM        mensaje_destinatarios MD
                LEFT JOIN  usuarios U ON MD.usuarioId = U.id
                WHERE       MD.mensajeId = {$this->id}
                ORDER BY    'usuarios.nombreCompleto'";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $this->destinatarios = $resultado;
    }

    public function crear($datos)
    {
        // Agregar al request para especificar el usuario que creó la Requisición
        $usuario = usuarioAutenticado();
        $datos["usuarioIdCreacion"] = $usuario ? $usuario["id"] : 1;
        
        $arrayPDOParam = array();
        $arrayPDOParam["mensajeTipoId"] = self::$type["mensajeTipoId"];
        $arrayPDOParam["mensajeEstatusId"] = self::$type["mensajeEstatusId"];
        $arrayPDOParam["asunto"] = self::$type["asunto"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["mensaje"] = self::$type["mensaje"];
        $arrayPDOParam["liga"] = self::$type["liga"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $mensajeId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $mensajeId);

        if ( $respuesta  ) {
            $this->id = $mensajeId;

            if ( is_array($datos['destinatarios'])) $respuesta = $this->insertarDestinatarios($datos['destinatarios']);

        }

        return $respuesta;
    }

    public function crearRessetPassword($datos)
    {

        $datos["usuarioIdCreacion"] = $datos["destinatarios"][0]["usuarioId"];
        
        $arrayPDOParam = array();
        $arrayPDOParam["mensajeTipoId"] = self::$type["mensajeTipoId"];
        $arrayPDOParam["mensajeEstatusId"] = self::$type["mensajeEstatusId"];
        $arrayPDOParam["asunto"] = self::$type["asunto"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["mensaje"] = self::$type["mensaje"];
        $arrayPDOParam["liga"] = self::$type["liga"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $mensajeId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $mensajeId);

        if ( $respuesta  ) {
            $this->id = $mensajeId;

            if ( is_array($datos['destinatarios'])) $respuesta = $this->insertarDestinatarios($datos['destinatarios']);

        }

        return $respuesta;
    }

    public function enviado()
    {
        $datos = array();

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request el estatus Enviado
        $datos["mensajeEstatusId"] = 2;

        $arrayPDOParam = array();
        $arrayPDOParam["mensajeEstatusId"] = self::$type["mensajeEstatusId"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function noEnviado($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request el estatus No Enviado
        $datos["mensajeEstatusId"] = 3;

        $arrayPDOParam = array();
        $arrayPDOParam["mensajeEstatusId"] = self::$type["mensajeEstatusId"];
        $arrayPDOParam["error"] = self::$type["error"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function actualizar($datos)
    {
        return
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        // $datos["fechaInicial"] = fFechaSQL($datos["fechaInicial"]);
        // $datos["fechaFinal"] = fFechaSQL($datos["fechaFinal"]);

        $arrayPDOParam = array();
        // $arrayPDOParam["fechaInicial"] = self::$type["fechaInicial"];
        // $arrayPDOParam["fechaFinal"] = self::$type["fechaFinal"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            $respuesta = $this->insertarDetalles($datos['detalles']);

        }

        return $respuesta;
    }

    function insertarDestinatarios(array $arrayDestinatarios)
    {

        $respuesta = false;

        $insertarPDOParam = array();
        $insertarPDOParam["mensajeId"] = self::$type[$this->keyName];
        $insertarPDOParam["usuarioId"] = "integer";        
        $insertarPDOParam["correo"] = "string";

        foreach ($arrayDestinatarios as $key => $value) {

            $insertar = array();
            $insertar["mensajeId"] = $this->id;
            $insertar["usuarioId"] = $value["usuarioId"];
            $insertar["correo"] = $value["correo"];

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO mensaje_destinatarios (mensajeId, usuarioId, correo) VALUES (:mensajeId, :usuarioId, :correo)", $insertar, $insertarPDOParam, $error);

        }

        return $respuesta;
    }

    public function eliminar()
    {
        return
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}
