<?php

namespace App\Models;

// if ( file_exists ( "app/Policies/IndicadorPolicy.php" ) ) {
//     require_once "app/Policies/IndicadorPolicy.php";
// } else {
//     require_once "../Policies/IndicadorPolicy.php";
// }

use App\Conexion;
use PDO;
// use App\Policies\IndicadorPolicy;

// class Indicador extends IndicadorPolicy
class Indicador
{
    static protected $fillable = [
        'diasSinCargaTitulo', 'diasSinCargaNumero', 'diasSinCargaMaquinariaEstatus', 'diasSinCargaMaquinariaTipos'
    ];

    static protected $type = [
        'id' => 'integer',
        'diasSinCargaTitulo' => 'string',
        'diasSinCargaNumero' => 'integer',
        'diasSinCargaMaquinariaEstatus' => 'string',
        'diasSinCargaMaquinariaTipos' => 'string',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "indicadores";

    protected $keyName = "id";

    public $id = null;
    public $diasSinCargaTitulo;
    public $diasSinCargaNumero;
    public $diasSinCargaMaquinariaEstatus;
    public $diasSinCargaMaquinariaTipos;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR INDICADORES
    =============================================*/
    public function consultar($item = null, $valor = null)
    {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT I.* FROM {$this->tableName} I ORDER BY I.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT I.* FROM {$this->tableName} I WHERE I.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT I.* FROM {$this->tableName} I WHERE I.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->diasSinCargaTitulo = $respuesta["diasSinCargaTitulo"];
                $this->diasSinCargaNumero = $respuesta["diasSinCargaNumero"];
                $this->diasSinCargaMaquinariaEstatus = json_decode($respuesta["diasSinCargaMaquinariaEstatus"]);
                $this->diasSinCargaMaquinariaTipos = json_decode($respuesta["diasSinCargaMaquinariaTipos"]);
            }

            return $respuesta;

        }

    }

    public function crear($datos)
    {
    }

    public function actualizar($datos)
    {
    }

    public function eliminar()
    {
    }
}
