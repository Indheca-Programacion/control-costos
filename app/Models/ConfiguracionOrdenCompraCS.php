<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ConfiguracionOrdenCompraPolicy.php" ) ) {
    require_once "app/Policies/ConfiguracionOrdenCompraPolicy.php";
} else {
    require_once "../Policies/ConfiguracionOrdenCompraPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ConfiguracionOrdenCompraPolicy;


class ConfiguracionOrdenCompra  extends ConfiguracionOrdenCompraPolicy
{ 
    static protected $fillable = [
        'inicialEstatusId', 'usuarioCreacionEliminarPartidas', 'perfiles', 'flujo'
    ];

    static protected $type = [
        'id' => 'integer',
        'inicialEstatusId' => 'integer',
        'usuarioCreacionEliminarPartidas' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "configuracion_ordenes";

    protected $keyName = "id";

    public $id = null;    
    public $inicialEstatusId;
    public $usuarioCreacionEliminarPartidas;
    public $perfiles = array();
    public $flujo = array();

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CONFIGURACION REQUISICION
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT CR.* FROM $this->tableName CR ORDER BY CR.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->inicialEstatusId = $respuesta["inicialEstatusId"];
                $this->usuarioCreacionEliminarPartidas = $respuesta["usuarioCreacionEliminarPartidas"];
            }

            return $respuesta;

        }
    }

    public function consultarPerfiles()
    {
        $query = "SELECT    P.nombre AS 'perfiles.nombre', SE.descripcion AS 'servicio_estatus.descripcion' , CPSE.*
                FROM        configuracion_perfil_estatus_ordenes CPSE
                INNER JOIN  perfiles P ON CPSE.perfilId = P.id
                INNER JOIN  estatus_orden_compra SE ON CPSE.EstatusId = SE.id
                WHERE       CPSE.configuracionOrdenesId = {$this->id}
                AND         CPSE.documentoTipo = 2";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $arrayPerfiles = [];
        foreach ($resultado as $key => $value) {
            $perfil = $value['perfiles.nombre'];
            if ( !isset($arrayPerfiles[$perfil]) ) $arrayPerfiles[$perfil] = [];

            array_push($arrayPerfiles[$perfil], $value);
        }

        // $this->perfiles = $resultado;
        $this->perfiles = $arrayPerfiles;
    }

    public function checkPerfil(string $perfil, string $estatus, string $opcion = 'modificar')
    {
        if ( !$this->perfiles || !isset($this->perfiles[$perfil]) ) {
            return false;
        }
        
        $key = array_search($estatus, array_column($this->perfiles[$perfil], 'servicio_estatus.descripcion'));
        
        
        if ( $key === false ) {
            return false;
        }

        return $this->perfiles[$perfil][$key][$opcion];
    }

    public function consultarFlujo()
    {
        $query = "SELECT    SE.descripcion AS 'estatus.descripcion',
                            SSE.descripcion AS 'siguiente_estatus.descripcion', CF.*
                FROM        configuracion_flujo_ordenes CF
                INNER JOIN  estatus_orden_compra SE ON CF.estatusId = SE.id
                INNER JOIN  estatus_orden_compra SSE ON CF.siguienteEstatusId = SSE.id
                WHERE       CF.configuracionOrdenesId = {$this->id}
                AND         CF.documentoTipo = 2";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $arrayFlujo = [];
        foreach ($resultado as $key => $value) {
            $estatus = $value['estatus.descripcion'];
            if ( !isset($arrayFlujo[$estatus]) ) $arrayFlujo[$estatus] = [];

            array_push($arrayFlujo[$estatus], $value);
        }

        $this->flujo = $arrayFlujo;
    }

    public function checkFlujo(string $estatus, string $siguienteEstatus)
    {
        if ( !$this->flujo || !isset($this->flujo[$estatus]) ) {
            return false;
        }

        $key = array_search($siguienteEstatus, array_column($this->flujo[$estatus], 'siguiente_estatus.descripcion'));

        if ( $key === false ) {
            return false;
        }

        return true;
    }

    public function crear($datos)
    {
        return false;
    }

   public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Configuración
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Modificar el contenido de los checkboxes
        $datos["usuarioCreacionEliminarPartidas"] = ( isset($datos["usuarioCreacionEliminarPartidas"]) && mb_strtolower($datos["usuarioCreacionEliminarPartidas"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();
        $arrayPDOParam["inicialEstatusId"] = self::$type["inicialEstatusId"];
        $arrayPDOParam["usuarioCreacionEliminarPartidas"] = self::$type["usuarioCreacionEliminarPartidas"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
        
        
        if ( $respuesta ) {

            $arrayPerfiles = isset($datos["perfiles"]) ? $datos["perfiles"] : null;

            if ( $this->eliminarPerfiles() ) {
                if ( $arrayPerfiles ) $respuesta = $this->actualizarPerfiles($arrayPerfiles);
            }

            $arrayFlujo = isset($datos["flujo"]) ? $datos["flujo"] : null;
            if ( $this->eliminarFlujo() ) {
                if ( $arrayFlujo ) $respuesta = $this->actualizarFlujo($arrayFlujo);
            }

        }

        return $respuesta;
    }

    function eliminarPerfiles()
    {
        $eliminar = array();
        $eliminar["configuracionOrdenesId"] = $this->id;
        $eliminar["documentoTipo"] = 2; // Requisición
        
        $eliminarPDOParam = array();
        $eliminarPDOParam["configuracionOrdenesId"] = self::$type[$this->keyName];
        $eliminarPDOParam["documentoTipo"] = "integer";

        return Conexion::queryExecute($this->bdName, "DELETE FROM configuracion_perfil_estatus_ordenes WHERE configuracionOrdenesId = :configuracionOrdenesId AND documentoTipo = :documentoTipo", $eliminar, $eliminarPDOParam, $error);
    }

    function actualizarPerfiles(array $arrayPerfiles = null)
    {
        $respuesta = false;
    
        if ( $arrayPerfiles ) {

            $insertarPDOParam = array();
            $insertarPDOParam["configuracionOrdenesId"] = self::$type[$this->keyName];
            $insertarPDOParam["documentoTipo"] = "integer";
            $insertarPDOParam["perfil"] = "string";
            $insertarPDOParam["EstatusId"] = "string";
            $insertarPDOParam["modificar"] = "integer";
            $insertarPDOParam["automatico"] = "integer";
            $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
            

            foreach ($arrayPerfiles as $perfil => $perfilEstatus) {

                $insertar = array();
                $insertar["configuracionOrdenesId"] = $this->id;
                $insertar["documentoTipo"] = 2; // Requisición
                $insertar["perfil"] = $perfil;
                // $insertar["servicioEstatus"] = $perfil;
                // $insertar["modificar"] = 1;
                // $insertar["automatico"] = 1;
                // Agregar al request para especificar el usuario que actualizó la Configuración
                $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

                foreach ($perfilEstatus as $Estatus => $value) {

                    $insertar["EstatusId"] = $Estatus;
                    $insertar["modificar"] = "0";
                    $insertar["automatico"] = "0";

                    foreach ($value as $opcion) {
                        $insertar[$opcion] = "1";
                    }
                    
                    $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO configuracion_perfil_estatus_ordenes (configuracionOrdenesId, documentoTipo, perfilId, EstatusId, modificar, automatico, usuarioIdCreacion) VALUES (:configuracionOrdenesId, :documentoTipo, (SELECT id FROM perfiles WHERE nombre = :perfil), (SELECT id FROM estatus_orden_compra WHERE descripcion = :EstatusId), :modificar, :automatico, :usuarioIdCreacion)", $insertar, $insertarPDOParam, $error);

                }

            }
            
        }

        return $respuesta;
    }

    function eliminarFlujo()
    {
        $eliminar = array();
        $eliminar["configuracionOrdenesId"] = $this->id;
        $eliminar["documentoTipo"] = 2; // Requisición
        
        $eliminarPDOParam = array();
        $eliminarPDOParam["configuracionOrdenesId"] = self::$type[$this->keyName];
        $eliminarPDOParam["documentoTipo"] = "integer";

        return Conexion::queryExecute($this->bdName, "DELETE FROM configuracion_flujo_ordenes WHERE configuracionOrdenesId = :configuracionOrdenesId AND documentoTipo = :documentoTipo", $eliminar, $eliminarPDOParam, $error);
    }

    function actualizarFlujo(array $arrayFlujo = null)
    {
        $respuesta = false;
    
        if ( $arrayFlujo ) {

            $insertarPDOParam = array();
            $insertarPDOParam["configuracionOrdenesId"] = self::$type[$this->keyName];
            $insertarPDOParam["documentoTipo"] = "integer";
            $insertarPDOParam["estatus"] = "string";
            $insertarPDOParam["siguienteEstatus"] = "string";
            $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            foreach ($arrayFlujo as $estatus => $siguienteEstatus) {

                $insertar = array();
                $insertar["configuracionOrdenesId"] = $this->id;
                $insertar["documentoTipo"] = 2; // Requisición
                $insertar["estatus"] = $estatus;
                // Agregar al request para especificar el usuario que actualizó la Configuración
                $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

                foreach ($siguienteEstatus as $Estatus) {
                    $insertar["siguienteEstatus"] = $Estatus;

                    $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO configuracion_flujo_ordenes (configuracionOrdenesId, documentoTipo, estatusId, siguienteEstatusId, usuarioIdCreacion) VALUES (:configuracionOrdenesId, :documentoTipo, (SELECT id FROM estatus_orden_compra WHERE descripcion = :estatus), (SELECT id FROM estatus_orden_compra WHERE descripcion = :siguienteEstatus), :usuarioIdCreacion)", $insertar, $insertarPDOParam, $error);
                }

            }

        }

        return $respuesta;
    }

    public function eliminar()
    {
        return false;
    }
}
