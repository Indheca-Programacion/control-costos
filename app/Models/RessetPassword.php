<?php

namespace App\Models;

use App\Conexion;
use PDO;

class RessetPassword 
{
    static protected $fillable = [
        'id','correo', 'token', 'expiracion', 
    ];

    static protected $type = [
        'id' => 'integer',
        'correo' => 'string',
        'token' => 'string',
        'expiracion' => 'datetime',
        'codigo'=> 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "resset_password";    

    protected $keyName = "id";

    static public function fillable() {
        return self::$fillable;
    }

    public function crear($datos) {

        $datos["expiracion"] = date("Y-m-d H:i:s", time() + 600); // 10 minutos

        $arrayPDOParam = array();        
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["token"] = self::$type["token"];
        $arrayPDOParam["expiracion"] = self::$type["expiracion"];
        $arrayPDOParam["codigo"] = self::$type["codigo"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (correo, token, expiracion,codigo) VALUES (:correo, :token, :expiracion,:codigo)", $datos, $arrayPDOParam, $error);
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT RP.* FROM $this->tableName RP", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->correo = $respuesta["correo"];
                $this->token = $respuesta["token"];
                $this->expiracion = $respuesta["expiracion"];
                $this->codigo = $respuesta["codigo"];

            }
            return $respuesta;
        }

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function ressetPassword($datos) {

        $arrayPDOParam = array();


        if ( $datos["contrasena"] != "") {

            $datos["contrasena"] = hash('sha256', $datos["contrasena"]);

            $arrayPDOParam["contrasena"] = "string";
            $arrayPDOParam["correo"] = "string";

           return Conexion::queryExecute($this->bdName, "UPDATE usuarios SET contrasena = :contrasena WHERE correo = :correo", $datos, $arrayPDOParam, $error);
        }

    }
}
