<?php

namespace App\Controllers;

if ( file_exists ( "app/Models/Usuario.php" ) ) {
    require_once "app/Models/Usuario.php";
} else {
    require_once "../Models/Usuario.php";
}

use App\Conexion;
use PDO;
use App\Models\Usuario;

class Autorizacion
{
    // static public function authUser() {

    //     $usuarioAutenticado = New Usuario;
    //     if ( usuarioAutenticado() ) {

    //         if ( $usuarioAutenticado->consultar("usuario", usuarioAutenticado()["usuario"]) ) {

    //             $usuarioAutenticado->consultarPerfiles();
    //             $usuarioAutenticado->consultarPermisos();

    //         }
            
    //     }

    //     return $usuarioAutenticado;

    // }

    static public function authorize($metodo, $modelo) {        

        if ( is_null(usuarioAutenticado()) ) {
            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();
        }
        
        $usuario = new Usuario;
        $usuario -> id = usuarioAutenticado()["id"];
        $usuario -> usuario = usuarioAutenticado()["usuario"];

        if ( !$modelo->$metodo($usuario, $modelo) ) {
            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();
        }

    }

    static public function perfil(Usuario $usuario, string $perfil) {        
        try {

            $con = Conexion::conectarBD(CONST_BD_SECURITY);
        
            $stmt = $con->prepare("SELECT * FROM perfiles INNER JOIN usuario_perfiles ON perfiles.id = usuario_perfiles.perfilId WHERE perfiles.nombre = :perfil AND usuario_perfiles.usuarioId = $usuario->id");
            
            $stmt->bindParam(":perfil", $perfil, PDO::PARAM_STR);

            $stmt -> execute();

            $respuesta = $stmt -> fetch();

        }

        catch( PDOException $Exception ) {

            $respuesta = (int)$Exception->getCode() . ": ". $Exception->getMessage();

        }

        $stmt = null;
        $con = null;

        return $respuesta;

    }

    static public function permiso(Usuario $usuario, string $permiso, string $tipo = null) {

        if ( is_null($tipo) ) {

            $trace = debug_backtrace();

            if ( isset($trace[1]["function"]) ) {

                $policy = $trace[1]["function"];

                switch ( $policy ) { 
                    case "view":
                        $tipo = "ver";
                        break;
                    case "create":
                        $tipo = "crear";
                        break;
                    case "update":
                        $tipo = "actualizar";
                        break;
                    case "delete":
                        $tipo = "eliminar";
                        break;
                    default:
                        return false;
                }

            }

        }

        try {

            $con = Conexion::conectarBD(CONST_BD_SECURITY);

            $stmt = $con->prepare("SELECT P.*, UP.". $tipo ." FROM usuario_permisos UP INNER JOIN permisos P ON UP.permisoId = P.id WHERE P.nombre = :permiso AND UP.usuarioId = $usuario->id AND UP.". $tipo." = 1 UNION ALL SELECT P.*, PP.". $tipo ." FROM usuario_perfiles UP INNER JOIN perfil_permisos PP ON UP.perfilId = PP.perfilId INNER JOIN permisos P ON PP.permisoId = P.id WHERE P.nombre = :permiso AND UP.usuarioId = $usuario->id AND PP.". $tipo." = 1");

            $stmt->bindParam(":permiso", $permiso, PDO::PARAM_STR);

            $stmt -> execute();

            $respuesta = $stmt -> fetch();

        }

        catch( PDOException $Exception ) {

            $respuesta = (int)$Exception->getCode() . ": ". $Exception->getMessage();

        }

        $stmt = null;
        $con = null;

        return $respuesta;

    }

}
