<?php

namespace App\Models;

use App\Conexion;
use App\Route;
use PDO;

class GastoArchivo
{
	static protected $fillable = [
        'id', 'gastoDetalleId', 'titulo', 'archivo', 'formato', 'ruta'
    ];

    static protected $type = [
        'id' => 'integer',
        'gastoDetalleId' => 'integer',
        'titulo' => 'string',
        'archivo' => 'string',
        'formato' => 'string',
        'ruta' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "gasto_archivos";

    protected $keyName = "id";

    public $id = null;
    public $gastoDetalleId;
    public $titulo;
    public $archivo;
    public $formato;
    public $ruta;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR SERVICIO ARCHIVO
    =============================================*/
    public function consultar()
    {
        $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $this->id AND gastoDetalleId = $this->gastoDetalleId", $error);

        if ( $respuesta ) {
            $this->id = $respuesta["id"];
            $this->gastoDetalleId = $respuesta["gastoDetalleId"];
            $this->titulo = $respuesta["titulo"];
            $this->archivo = $respuesta["archivo"];
            $this->formato = $respuesta["formato"];
            $this->ruta = $respuesta["ruta"];
        }

        return $respuesta;
    }

    public function crear($datos)
    {
        return; // No se utiliza
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
        $datos["gastoDetalleId"] = $this->gastoDetalleId;
        
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type[$this->keyName];
        $arrayPDOParam["gastoDetalleId"] = self::$type["gastoDetalleId"];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id AND gastoDetalleId = :gastoDetalleId", $datos, $arrayPDOParam, $error);

        if ( $respuesta && !is_null($this->ruta) ) {
            // Eliminar físicamente el archivo (si tiene)
            // unlink($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
            fDeleteFile($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
        }

        return $respuesta;
    }
}
