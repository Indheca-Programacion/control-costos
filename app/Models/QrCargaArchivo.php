<?php

namespace App\Models;

use App\Conexion;
use App\Route;
use PDO;

class QrCargaArchivo
{
    static protected $fillable = [
        'id', 'idQrCarga', 'tipo', 'titulo', 'archivo', 'formato', 'ruta'
    ];

    static protected $type = [
        'id' => 'integer',
        'idQrCarga' => 'integer',
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
    protected $tableName = "qr_cargas_archivos";

    protected $keyName = "id";

    public $id = null;
    public $idMaquinaria;
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
        $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $this->id AND 	nIdMaquinaria02MaquinariaTraslado = $this->idMaquinaria AND eliminado = 1", $error);
        
        if ( $respuesta ) {
            $this->id = $respuesta["id"];
            $this->idMaquinaria = $respuesta["nIdMaquinaria02MaquinariaTraslado"];
            $this->tipo = $respuesta["tipo"];
            $this->titulo = $respuesta["titulo"];
            $this->archivo = $respuesta["archivo"];
            $this->formato = $respuesta["formato"];
            $this->ruta = $respuesta["ruta"];
        }

        return $respuesta;
    }


    /*=============================================
    FUNCION ELIMINAR ARCHIVO
    =============================================*/

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
