<?php

namespace App\Models;

if ( file_exists ( "app/Policies/TareaObservacionesPolicy.php" ) ) {
    require_once "app/Policies/TareaObservacionesPolicy.php";
} else {
    require_once "../Policies/TareaObservacionesPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\TareaObservacionesPolicy;

class TareaObservaciones extends TareaObservacionesPolicy
{
    static protected $fillable = [
        'fk_tarea', 'observacion', 'archivos'
    ];

    static protected $type = [
        'id' => 'integer',
        'fk_tarea' => 'integer',
        'observacion' => 'string',
        'usuarioIdCreacion' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "tarea_observaciones";    

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                "SELECT T.id, T.descripcion, T.fecha_inicio, T.fecha_limite, T.fecha_creacion,
                FROM $this->tableName  T
                INNER JOIN usuarios U ON U.id = T.fk_usuario", $error);

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
            }

            return $respuesta;

        }

    }

    public function consultarPorTarea($id){
        return Conexion::queryAll($this->bdName, 
                "SELECT * FROM $this->tableName WHERE fk_tarea = $id",$error);
    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["fk_tarea"] = self::$type["fk_tarea"];
        $arrayPDOParam["observacion"] = self::$type["observacion"];

        $lastId = 0;

        $columna=fCreaCamposInsert($arrayPDOParam);
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$columna, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;
            $this->fk_tarea = $datos["fk_tarea"];

            if (isset($datos["archivos"]) && $datos['archivos']['name'][0] != '') {
                
                $respuesta = $this->insertarArchivos($datos['archivos']);
            }
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
            if($datos["estatus"]==3){
                $fecha_actual = date("Y-m-d H:i:s");
                $datos["fecha_finalizacion"] = $fecha_actual;
                $arrayPDOParam["fecha_finalizacion"] = self::$type["fecha_finalizacion"];

            }
        }
        
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);


        if ( $respuesta ) {

        }

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

    function insertarArchivos($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/tareas/";
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';

                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";
                elseif ( $archivos["type"][$i] == "text/xml" ) $extension = ".xml";
                elseif ( $archivos["type"][$i] == "image/png" ) $extension = ".png";
                elseif ( $archivos["type"][$i] == "image/jpeg" ) $extension = ".jpeg";
                elseif ( $archivos["type"][$i] == "image/jpg" ) $extension = ".jpg";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["fk_tarea"] = $this->fk_tarea;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;

            $arrayPDOParam = array();        
            $arrayPDOParam["fk_tarea"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);
            
            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO tarea_archivos " . $campos, $insertar, $arrayPDOParam, $error);
            
            if ( $respuesta && $ruta != "" ) {

                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }
}
