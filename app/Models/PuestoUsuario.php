<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PuestoPolicy.php" ) ) {
    require_once "app/Policies/PuestoPolicy.php";
} else {
    require_once "../Policies/PuestoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PuestoPolicy;

class PuestoUsuario extends PuestoPolicy
{
    static protected $fillable = [
        'idPuesto', 'idUsuario','idUbicacion','idObra'
    ];

    static protected $type = [
        'id' => 'integer',
        'idPuesto' => 'integer',
        'idUsuario' => 'integer',
        'idObra' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "puesto_usuario";

    protected $keyName = "id";

    public $id = null;    
    public $idPuesto = null;    
    public $idUsuario = null;    
    public $idObra;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PUESTOS USUARIOS
    =============================================*/

    public function consultar($item1 = null, $valor1 = null, $item2 =  null, $valor2 = null,$item3 =  null, $valor3 = null) {
        
        if ( is_null($valor1) ) {

            return Conexion::queryAll($this->bdName, "SELECT PU.* FROM $this->tableName PU", $error);

        } elseif ($item2 && $item3) {

            return Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName 
            WHERE $item1 = '$valor1' 
            AND $item2 = $valor2
            AND $item3 = $valor3
            ", $error);
        }
        else {

            if ( is_null($item1) ) {
                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor1", $error);

            } else {
                $respuesta = Conexion::queryAll($this->bdName, 
                    "SELECT 
	                    PU.*, 
                        P.nombreCorto AS nombrePuesto,
                        O.descripcion AS nombreObra
                    FROM 
	                    `puesto_usuario` PU
                    INNER JOIN	 puestos P ON PU.idPuesto = P.id
                    INNER JOIN obras O ON PU.idObra = O.id
                    WHERE 
                        $item1 = '$valor1'", $error);

                return $respuesta;
            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->idPuesto  = $respuesta["idPuesto"];
                $this->idUsuario  = $respuesta["idUsuario"];
                $this->idObra  = $respuesta["idObra"];
                $this->nombreObra  = $respuesta["nombreObra"];
                $this->nombrePuesto  = $respuesta["nombrePuesto"];

            }

            return $respuesta;
        }
    }

    public function obtenerSuperIntendente($idObra = null){

        return Conexion::queryAll($this->bdName, "SELECT 
                                                    * FROM 
                                                $this->tableName
                                                WHERE 
                                                    idObra = $idObra
                                                AND
                                                idPuesto = 10", $error);
    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["idUsuario"] = "integer";
        $arrayPDOParam["idPuesto"] = "integer";
        $arrayPDOParam["idObra"] = "integer";

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (idUsuario,idPuesto,idObra) VALUES (:idUsuario,:idPuesto,:idObra) ", $datos, $arrayPDOParam, $error);

    }

    //FALTA CREAR FUNCION PARA ELIMINAR 
    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
