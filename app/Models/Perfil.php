<?php

namespace App\Models;

// require_once "app/conexion.php";
// require_once "app/Policies/PerfilPolicy.php";

if ( file_exists ( "app/Policies/PerfilPolicy.php" ) ) {
    require_once "app/Policies/PerfilPolicy.php";
} else {
    require_once "../Policies/PerfilPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PerfilPolicy;

class Perfil extends PerfilPolicy
{
    static protected $fillable = [
        'nombre', 'descripcion', 'permisos'
    ];

    static protected $type = [
        'id' => 'integer',
        'nombre' => 'string',
        'descripcion' => 'string'
    ];

    // protected $hidden = [
    //     'password', 'remember_token',
    // ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "perfiles";

    protected $keyName = "id";

    public $id = null;
    public $nombre;
    public $descripcion;
    public $permisos = array();

    // function __construct() {
    //     $this->bdName = "security";
    //     $this->tableName = "perfiles";
    // }

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PERFILES
    =============================================*/

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->nombre = $respuesta["nombre"];
                $this->descripcion = $respuesta["descripcion"];
            }

            return $respuesta;

        }

    }

    public function consultarPermisos() {

        $query = "SELECT P.*, PP.* FROM perfil_permisos PP INNER JOIN permisos P ON PP.permisoId = P.id WHERE PP.perfilId = $this->id";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);
        
        // $this->permisos = array_column($resultado, "nombre");
        $this->permisos = $resultado;

    }

    public function consultarUsuarios(array $arrayPerfiles = null, array $arrayUsuarios = null)
    {
        $resultado = false;

        if ( $arrayPerfiles ) {

            $perfilesId = "";
            foreach ($arrayPerfiles as $key => $value) {
                if ( $key > 0 ) $perfilesId .= ", ";
                $perfilesId .= $value;
            }
            $usuarios = '';
            if ( $arrayUsuarios !== null ){
                $usuariosId = "";
                foreach ($arrayUsuarios as $key => $value) {
                    if ( $key > 0 ) $usuariosId .= ", ";
                    $usuariosId .= $value;
                }
                
                $usuarios = " AND U.id IN ( {$usuariosId}) ";
            }
            
            $query = "SELECT    UP.*, P.nombre, P.descripcion, U.correo
                    FROM        usuario_perfiles UP
                    INNER JOIN  perfiles P ON P.id = UP.perfilId
                    INNER JOIN  usuarios U ON U.id = UP.usuarioId
                    WHERE       UP.perfilId IN ( {$perfilesId} ) {$usuarios}";

            $resultado = Conexion::queryAll($this->bdName, $query, $error);

        }

        $this->usuarios = $resultado;
    }

    public function checkPermiso(string $permiso, string $opcion) {

        if ( !$this->permisos ) {
            return false;
        }

        $key = array_search($permiso, array_column($this->permisos, 'nombre'));

        if ( $key === false ) {
            return false;
        }

        return $this->permisos[$key][$opcion];

        // if ( $this->permisos[$key][$opcion] == 1 ) {
        //     return true;
        // }

        // return false;

    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $lastId = 0;
        
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (nombre, descripcion) VALUES (:nombre, :descripcion)", $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el perfil
            $this->id = $lastId;

            $arrayPermisos = isset($datos["permisos"]) ? $datos["permisos"] : null;

            if ( $arrayPermisos ) {

                $respuesta = $this->actualizarPermisos($arrayPermisos);

            }

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            $arrayPermisos = isset($datos["permisos"]) ? $datos["permisos"] : null;

            if ( $this->eliminarPermisos() ) {

                if ( $arrayPermisos ) {

                    $respuesta = $this->actualizarPermisos($arrayPermisos);

                }
            
            }

        }

        return $respuesta;

    }

    function eliminarPermisos() {

        $eliminar = array();
        $eliminar["perfilId"] = $this->id;
        
        $eliminarPDOParam = array();
        $eliminarPDOParam["perfilId"] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM perfil_permisos WHERE perfilId = :perfilId", $eliminar, $eliminarPDOParam, $error);

    }

    function actualizarPermisos(array $arrayPermisos = null) {

        $respuesta = false;
    
        if ( $arrayPermisos ) {

            $insertarPDOParam = array();
            $insertarPDOParam["perfilId"] = self::$type[$this->keyName];
            $insertarPDOParam["permiso"] = "string";
            $insertarPDOParam["ver"] = "integer";
            $insertarPDOParam["crear"] = "integer";
            $insertarPDOParam["actualizar"] = "integer";
            $insertarPDOParam["eliminar"] = "integer";

            foreach ($arrayPermisos as $permiso => $value) {

                $insertar = array();
                $insertar["perfilId"] = $this->id;
                $insertar["permiso"] = $permiso;

                $insertar["ver"] = "0";
                $insertar["crear"] = "0";
                $insertar["actualizar"] = "0";
                $insertar["eliminar"] = "0";

                foreach ($value as $opcion) {
                    $insertar[$opcion] = "1";
                }

                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO perfil_permisos (perfilId, permisoId, ver, crear, actualizar, eliminar) VALUES (:perfilId, (SELECT id FROM permisos WHERE nombre = :permiso), :ver, :crear, :actualizar, :eliminar)", $insertar, $insertarPDOParam, $error);                

            }
            
        }

        return $respuesta;

    }

    public function eliminar() {

        if ( $this->eliminarPermisos() ) {

            // Agregar al request para eliminar el registro
            $datos = array();
            $datos[$this->keyName] = $this->id;
            
            $arrayPDOParam = array();
            $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

            return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

        } else {

            return false;

        }

    }
}
