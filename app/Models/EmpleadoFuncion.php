<?php

namespace App\Models;

// if ( file_exists ( "app/Policies/EmpleadoFuncionPolicy.php" ) ) {
//     require_once "app/Policies/EmpleadoFuncionPolicy.php";
// } else {
//     require_once "../Policies/EmpleadoFuncionPolicy.php";
// }

use App\Conexion;
use PDO;
// use App\Policies\EmpleadoFuncionPolicy;

// class EmpleadoFuncion extends EmpleadoFuncionPolicy
class EmpleadoFuncion
{
    static protected $fillable = [
        'descripcion', 'nombreCorto', 'orden'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'orden' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "empleado_funciones";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;
    public $orden;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR FUNCIONES DE EMPLEADO
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT EF.* FROM $this->tableName EF ORDER BY EF.orden", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->orden = $respuesta["orden"];
            }

            return $respuesta;

        }
    }
}
