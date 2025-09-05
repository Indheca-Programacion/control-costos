<?php

namespace App\Models;

if ( file_exists ( "app/Policies/InformacionTecnicaPolicy.php" ) ) {
    require_once "app/Policies/InformacionTecnicaPolicy.php";
} else {
    require_once "../Policies/InformacionTecnicaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\InformacionTecnicaPolicy;

class InformacionTecnica extends InformacionTecnicaPolicy
{
    static protected $fillable = [
        'titulo', 'archivo', 'formato', 'ruta', 'tags', 'perfiles'
    ];

    static protected $type = [
        'id' => 'integer',
        'titulo' => 'string',
        'archivo' => 'string',
        'formato' => 'string',
        'ruta' => 'string',
        'tags' => 'string',
        'perfiles' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "informacion_tecnica";

    protected $keyName = "id";

    public $id = null;    
    public $titulo;
    public $archivo;
    public $formato;
    public $ruta;
    public $tags;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR INFORMACION TECNICA
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT IT.*, US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno' FROM $this->tableName IT INNER JOIN usuarios US ON IT.usuarioIdCreacion = US.id ORDER BY IT.titulo", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->titulo = $respuesta["titulo"];
                $this->archivo = $respuesta["archivo"];
                $this->formato = $respuesta["formato"];
                $this->ruta = $respuesta["ruta"];
                $this->perfiles = ( !is_null($respuesta["perfiles"]) ) ? json_decode($respuesta["perfiles"], true) : array();
                $this->tags = ( !is_null($respuesta["tags"]) ) ? json_decode($respuesta["tags"], true) : array();
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        // Agregar al request para especificar el usuario que creó la Información Técnica
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Agregar al request el nombre, formato y ruta final del archivo
        $ruta = "";
        if ( $datos["archivo"]["tmp_name"] != "" ) {

            $archivo = $datos["archivo"]["name"];
            $tipo = $datos["archivo"]["type"];
            $tmp_name = $datos["archivo"]["tmp_name"];

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $directorio = "vistas/uploaded-files/informacion-tecnica/";
            // $aleatorio = mt_rand(10000000,99999999);
            $extension = '';

            if ( $datos["archivo"]["type"] == "application/msword" ) $extension = ".doc";
            elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ) $extension = ".docx";
            elseif ( $datos["archivo"]["type"] == "application/vnd.ms-excel" ) $extension = ".xls";
            elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) $extension = ".xlsx";
            elseif ( $datos["archivo"]["type"] == "application/pdf" ) $extension = ".pdf";
            elseif ( $datos["archivo"]["type"] == "image/jpeg" ) $extension = ".jpg";
            elseif ( $datos["archivo"]["type"] == "image/png" ) $extension = ".png";

            if ( $extension != '') {
                // $ruta = $directorio.$aleatorio.$extension;
                do {
                    $ruta = fRandomNameFile($directorio, $extension);
                } while ( file_exists($ruta) );
                // move_uploaded_file($tmp_name, $ruta);
            }

        }
        // Request con el nombre del archivo
        $datos["archivo"] = $archivo;
        $datos["formato"] = $tipo;
        $datos["ruta"] = $ruta;

        // Modificar el contenido de los tags
        // $datos["tags"] = ( isset($datos["tags"]) ) ? json_encode($datos["tags"], JSON_FORCE_OBJECT) : null;
        $datos["tags"] = ( isset($datos["tags"]) ) ? json_encode($datos["tags"]) : null;
        $datos["perfiles"] = ( isset($datos["perfiles"]) ) ? json_encode($datos["perfiles"]) : "[]";

        $arrayPDOParam = array();        
        $arrayPDOParam["titulo"] = self::$type["titulo"];
        $arrayPDOParam["archivo"] = self::$type["archivo"];
        $arrayPDOParam["formato"] = self::$type["formato"];
        $arrayPDOParam["ruta"] = self::$type["ruta"];
        $arrayPDOParam["tags"] = self::$type["tags"];
        $arrayPDOParam["perfiles"] = self::$type["perfiles"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

        if ( $respuesta && $ruta != "" ) {
            move_uploaded_file($tmp_name, $ruta);
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["titulo"] = self::$type["titulo"];
        $arrayPDOParam["tags"] = self::$type["tags"];
        $arrayPDOParam["perfiles"] = self::$type["perfiles"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        // Agregar al request el nombre, formato y ruta final del archivo
        $ruta = "";
        if ( isset($datos["archivo"]) && $datos["archivo"]["tmp_name"] != "" ) {

            $archivo = $datos["archivo"]["name"];
            $tipo = $datos["archivo"]["type"];
            $tmp_name = $datos["archivo"]["tmp_name"];

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $directorio = "vistas/uploaded-files/informacion-tecnica/";
            // $aleatorio = mt_rand(10000000,99999999);
            $extension = '';

            if ( $datos["archivo"]["type"] == "application/msword" ) $extension = ".doc";
            elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ) $extension = ".docx";
            elseif ( $datos["archivo"]["type"] == "application/vnd.ms-excel" ) $extension = ".xls";
            elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) $extension = ".xlsx";
            elseif ( $datos["archivo"]["type"] == "application/pdf" ) $extension = ".pdf";
            elseif ( $datos["archivo"]["type"] == "image/jpeg" ) $extension = ".jpg";
            elseif ( $datos["archivo"]["type"] == "image/png" ) $extension = ".png";

            if ( $extension != '') {
                // $ruta = $directorio.$aleatorio.$extension;
                do {
                    $ruta = fRandomNameFile($directorio, $extension);
                } while ( file_exists($ruta) );
                // move_uploaded_file($tmp_name, $ruta);

                // Request con el nombre del archivo
                $datos["archivo"] = $archivo;
                $datos["formato"] = $tipo;
                $datos["ruta"] = $ruta;

                $arrayPDOParam["archivo"] = self::$type["archivo"];
                $arrayPDOParam["formato"] = self::$type["formato"];
                $arrayPDOParam["ruta"] = self::$type["ruta"];                
            }

        }

        // Modificar el contenido de los tags
        // $datos["tags"] = ( isset($datos["tags"]) ) ? json_encode($datos["tags"], JSON_FORCE_OBJECT) : null;
        $datos["tags"] = ( isset($datos["tags"]) ) ? json_encode($datos["tags"]) : null;
        $datos["perfiles"] = ( isset($datos["perfiles"]) ) ? json_encode($datos["perfiles"]) : "[]";

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta && $ruta != "" ) {
            move_uploaded_file($tmp_name, $ruta);

            // Eliminar  físicamente el archivo anterior
            if ( !is_null($this->ruta) ) unlink($this->ruta);
        }

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta && !is_null($this->ruta) ) {
            // Eliminar físicamente el archivo (si tiene)
            // unlink($this->ruta);
            fDeleteFile($this->ruta);
        }

        return $respuesta;

    }
}
