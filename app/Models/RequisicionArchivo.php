<?php

namespace App\Models;

use App\Conexion;
use App\Route;
use PDO;

class RequisicionArchivo
{
    static protected $fillable = [
        'id', 'requisicionId', 'tipo', 'titulo', 'archivo', 'formato', 'ruta'
    ];

    static protected $type = [
        'id' => 'integer',
        'requisicionId' => 'integer',
        'tipo' => 'integer',
        'titulo' => 'string',
        'archivo' => 'string',
        'formato' => 'string',
        'ruta' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'eliminado' => 'integer',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "requisicion_archivos";

    protected $keyName = "id";

    public $id = null;
    public $requisicionId;
    public $tipo;
    public $titulo;
    public $archivo;
    public $formato;
    public $ruta;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR REQUISICION ARCHIVO
    =============================================*/
    public function consultar()
    {
        $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $this->id AND requisicionId = $this->requisicionId AND eliminado = 1", $error);

        if ( $respuesta ) {
            $this->id = $respuesta["id"];
            $this->requisicionId = $respuesta["requisicionId"];
            $this->tipo = $respuesta["tipo"];
            $this->titulo = $respuesta["titulo"];
            $this->archivo = $respuesta["archivo"];
            $this->formato = $respuesta["formato"];
            $this->ruta = $respuesta["ruta"];
        }

        return $respuesta;
    }

    public function crear($datos)
    {
        return;

        // Agregar al request para crear el registro
        $datos["maquinariaId"] = $this->maquinariaId;

        // Agregar al request el nombre, formato y ruta final del archivo
        $ruta = "";
        if ( $datos["archivo"]["tmp_name"] != "" ) {

            $archivo = $datos["archivo"]["name"];
            $tipo = $datos["archivo"]["type"];
            $tmp_name = $datos["archivo"]["tmp_name"];

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $directorio = "vistas/uploaded-files/maquinaria-horometros/";
            // $aleatorio = mt_rand(10000000,99999999);
            $extension = '';

            // if ( $datos["archivo"]["type"] == "application/msword" ) $extension = ".doc";
            // elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ) $extension = ".docx";
            // elseif ( $datos["archivo"]["type"] == "application/vnd.ms-excel" ) $extension = ".xls";
            // elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) $extension = ".xlsx";
            // else
            if ( $datos["archivo"]["type"] == "application/pdf" ) $extension = ".pdf";
            // elseif ( $datos["archivo"]["type"] == "image/jpeg" ) $extension = ".jpg";
            // elseif ( $datos["archivo"]["type"] == "image/png" ) $extension = ".png";

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

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fecha"] = fFechaSQL($datos["fecha"]);

        $arrayPDOParam = array();
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["horometroInicial"] = self::$type["horometroInicial"];
        $arrayPDOParam["kilometrajeInicial"] = self::$type["kilometrajeInicial"];
        $arrayPDOParam["horometroFinal"] = self::$type["horometroFinal"];
        $arrayPDOParam["kilometrajeFinal"] = self::$type["kilometrajeFinal"];
        $arrayPDOParam["archivo"] = self::$type["archivo"];
        $arrayPDOParam["formato"] = self::$type["formato"];
        $arrayPDOParam["ruta"] = self::$type["ruta"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

        if ( $respuesta && $ruta != "" ) {
            // move_uploaded_file($tmp_name, $ruta);
            move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$ruta); // Ruta absoluta al ser llamado desde JS
        }

        return $respuesta;
    }

    public function actualizar($datos)
    {
        return; // No se utiliza
    }

    public function eliminar()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos["id"] = $this->id;
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
        $datos["eliminado"] = 0;
        
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type[$this->keyName];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["eliminado"] = self::$type["eliminado"];
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam);

        if ( $respuesta && !is_null($this->ruta) ) {
            // Eliminar físicamente el archivo (si tiene)
            // unlink($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
            fDeleteFile($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
        }

        return $respuesta;
    }
}
