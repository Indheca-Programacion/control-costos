<?php

namespace App\Models;

use App\Conexion;
use PDO;

class TareaArchivo
{
    static protected $fillable = [
        'fk_usuario', 'descripcion', 'fecha_inicio', 'fecha_limite', 'estatus'
    ];

    static protected $type = [
        'id' => 'integer',
        'fk_tarea' => 'integer',
        'titulo' => 'string',
        'archivo' => 'date',
        'formato' => 'date',
        'ruta' => 'date',
        'usuarioIdActualizacion' => 'integer',
        'fechaCreacion' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "tarea_archivos";    

    protected $keyName = "id";

    public $id = null;
    public $fk_tarea;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $this->id AND fk_tarea = $this->fk_tarea", $error);

        if ( $respuesta ) {
            $this->id = $respuesta["id"];
            $this->fk_tarea = $respuesta["fk_tarea"];
            $this->titulo = $respuesta["titulo"];
            $this->archivo = $respuesta["archivo"];
            $this->formato = $respuesta["formato"];
            $this->ruta = $respuesta["ruta"];
        }

        return $respuesta;

    }

    public function consultarPorUsuario($id){
        return Conexion::queryAll($this->bdName, 
                "SELECT T.id, T.descripcion, T.fecha_inicio, T.fecha_limite, T.fecha_creacion,
                CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS responsable,
                CONCAT(UC.nombre, ' ', UC.apellidoPaterno, ' ', IFNULL(UC.apellidoMaterno, '')) AS creo,
                CASE
                    WHEN T.estatus = 1 THEN 'SIN EMPEZAR'
                    WHEN T.estatus = 2 THEN 'EN CURSO'
                    WHEN T.estatus = 3 THEN 'COMPLETADO'	
                END AS estatus
                FROM $this->tableName  T
                INNER JOIN usuarios U ON U.id = T.fk_usuario
                INNER JOIN usuarios UC ON UC.id = T.usuarioIdCreacion 
                WHERE U.id = $id", $error);
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

    public function actualizar($datos) {
        
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        
        
        
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

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos["id"] = $this->id;
        $datos["fk_tarea"] = $this->fk_tarea;
        
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type[$this->keyName];
        $arrayPDOParam["fk_tarea"] = self::$type["fk_tarea"];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id AND fk_tarea = :fk_tarea", $datos, $arrayPDOParam, $error);

        if ( $respuesta && !is_null($this->ruta) ) {
            // Eliminar físicamente el archivo (si tiene)
            // unlink($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
            fDeleteFile($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
        }

        return $respuesta;

    }

}
