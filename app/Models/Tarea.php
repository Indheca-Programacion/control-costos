<?php

namespace App\Models;

if ( file_exists ( "app/Policies/TareaPolicy.php" ) ) {
    require_once "app/Policies/TareaPolicy.php";
} else {
    require_once "../Policies/TareaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\TareaPolicy;

class Tarea extends TareaPolicy
{
    static protected $fillable = [
        'fk_usuario', 'descripcion', 'fecha_inicio', 'fecha_limite', 'estatus'
    ];

    static protected $type = [
        'id' => 'integer',
        'fk_usuario' => 'integer',
        'descripcion' => 'string',
        'fecha_inicio' => 'date',
        'fecha_limite' => 'date',
        'fecha_finalizacion' => 'date',
        'usuarioIdCreacion' => 'integer',
        'estatus' => 'integer',
        'idRequisicion' => 'integer'

    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "tareas";    

    protected $keyName = "id";

    public $id = null;
    public $observaciones;
    public $archivos;
    public $requisicionId;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                "SELECT T.id, T.descripcion, T.fecha_inicio, T.fecha_limite, T.fecha_creacion,
                CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS responsable,
                CONCAT(UC.nombre, ' ', UC.apellidoPaterno, ' ', IFNULL(UC.apellidoMaterno, '')) AS creo,
                T.estatus as estatus,
                CASE
                    WHEN T.estatus = 0 THEN 'SIN EMPEZAR'
                    WHEN T.estatus = 10 THEN 'COMPLETADO'	
                    ELSE 'EN CURSO'
                END AS estatusLabel
                FROM $this->tableName  T
                INNER JOIN usuarios U ON U.id = T.fk_usuario
                INNER JOIN usuarios UC ON UC.id = T.usuarioIdCreacion ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT* FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->responsable = $respuesta["fk_usuario"];
                $this->descripcion = $respuesta["descripcion"];
                $this->fecha_inicio = fFechaLarga($respuesta["fecha_inicio"]);
                $this->fecha_limite = fFechaLarga($respuesta["fecha_limite"]);
                $this->estatus = $respuesta["estatus"];
                $this->creo = $respuesta["usuarioIdCreacion"];
                $this->requisicionId = $respuesta["idRequisicion"];
            }

            return $respuesta;

        }

    }

    public function consultarPorUsuario($id){
        return Conexion::queryAll($this->bdName, 
                "SELECT T.id, T.descripcion, T.fecha_inicio, T.fecha_limite, T.fecha_creacion,
                CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS responsable,
                CONCAT(UC.nombre, ' ', UC.apellidoPaterno, ' ', IFNULL(UC.apellidoMaterno, '')) AS creo,
                T.estatus as estatus,
                CASE
                    WHEN T.estatus = 0 THEN 'SIN EMPEZAR'
                    WHEN T.estatus = 10 THEN 'COMPLETADO'	
                    ELSE 'EN CURSO'
                END AS estatusLabel
                FROM $this->tableName  T
                INNER JOIN usuarios U ON U.id = T.fk_usuario
                INNER JOIN usuarios UC ON UC.id = T.usuarioIdCreacion 
                WHERE U.id = $id or T.usuarioIdCreacion = $id", $error);
    }

    public function consultarPendientes($id){
        return Conexion::queryAll($this->bdName,"SELECT * FROM $this->tableName where fk_usuario = $id and estatus <> 10");
    }

    public function consultarObservaciones(){
        $respuesta = Conexion::queryAll($this->bdName,"SELECT * FROM tarea_observaciones where fk_tarea = $this->id");
        $this->observaciones = $respuesta;
    }

    public function consultarArchivos(){
        $respuesta = Conexion::queryAll($this->bdName,"SELECT * FROM tarea_archivos where fk_tarea = $this->id");
        $this->archivos = $respuesta;
    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["fk_usuario"] = self::$type["fk_usuario"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_limite"] = self::$type["fecha_limite"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $lastId = 0;
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        $datos["fecha_limite"] = fFechaSQL($datos["fecha_limite"]);
        
        $columna=fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$columna, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;

        }

        return $respuesta;

    }

    public function crearTareaSuperIntendente($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["fk_usuario"] = self::$type["fk_usuario"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_limite"] = self::$type["fecha_limite"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["idRequisicion"] = self::$type["idRequisicion"];

        $hoy = date('Y-m-d');
        $fechaLimite = date('Y-m-d', strtotime('+2 days'));

        $lastId = 0;
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["fecha_inicio"] = $hoy;
        $datos["fecha_limite"] = $fechaLimite;
        $datos["descripcion"] = "Autorizar requisición 1".$datos["idRequisicion"];
        
        $columna=fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$columna, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;

        }

        return $respuesta;

    }

    public function actualizar($datos) {
        
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        
        if(isset($datos["descripcion"])) $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        if(isset($datos["fk_usuario"])) $arrayPDOParam["fk_usuario"] = self::$type["fk_usuario"];
        if(isset($datos["fecha_inicio"])) {
            $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
            $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        }
        if(isset($datos["fecha_limite"])) {
            $arrayPDOParam["fecha_limite"] = self::$type["fecha_limite"];
            $datos["fecha_limite"] = fFechaSQL($datos["fecha_limite"]);

        } 
        
        if(isset($datos["estatus"])){
            $arrayPDOParam["estatus"] = self::$type["estatus"];
            if($datos["estatus"]==10){
                $fecha_actual = date("Y-m-d H:i:s");
                $datos["fecha_finalizacion"] = $fecha_actual;
                $arrayPDOParam["fecha_finalizacion"] = self::$type["fecha_finalizacion"];
            }
        }
        
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);


        if ( $respuesta ) {
            $arrayPDOParam["estatus"] = self::$type["estatus"];
        }

        return $respuesta;

    }

    public function actualizarEstatus($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["estatus"] = self::$type["estatus"];

        if($datos["estatus"]==10){
            $fecha_actual = date("Y-m-d H:i:s");
            $datos["fecha_finalizacion"] = $fecha_actual;
            $arrayPDOParam["fecha_finalizacion"] = self::$type["fecha_finalizacion"];
        }

        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);
        
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
