<?php

namespace App\Controllers;

require_once "app/Models/Usuario.php";
require_once "app/Models/Sucursal.php";
// require_once "app/Requests/Request.php";
require_once "app/Requests/LoginRequest.php";

use App\Models\Usuario;
use App\Models\Sucursal;
// use App\Requests\Request;
use App\Requests\LoginRequest;
use App\Route;

class LoginController
{
    public function index()
    {
        if ( usuarioAutenticado() ) {
            header("Location:" . Route::routes('inicio'));
            die();
        }

        include "vistas/modulos/login.php";
    }

    public function login()
    {
        // $request = Request::value();
        $request = LoginRequest::validated();

        $encriptar = hash('sha256', $request["contrasena"]);
        
        // $valor = trim($request["usuario"]);
        $valor = $request["usuario"];

        $usuario = New Usuario;
        $respuesta = $usuario->consultar("usuario", $valor);

        if ( mb_strtoupper($respuesta["usuario"]) == mb_strtoupper($valor) && $respuesta["contrasena"] == $encriptar && $respuesta["activo"] == 1 ) {

            // Verifica que el Usuario tenga una Empresa asignada
            if ( is_null($respuesta["empresaId"]) ) {

                // $_SESSION[CONST_SESSION_APP]["flash"] = "El usuario no tiene empresa asignada";
                // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'alert-danger',
                                                           'titulo' => 'Ingresar',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'El usuario no tiene empresa asignada' );

                header("Location:" . Route::routes('ingreso'));
                die();

            }

            // Verifica que la Empresa tenga una Sucursal
            $sucursal = New Sucursal;
            $respuestaSucursal = $sucursal->consultar("empresaId", $respuesta["empresaId"]);

            if ( !$respuestaSucursal ) {

                // $_SESSION[CONST_SESSION_APP]["flash"] = "La empresa no tiene una sucursal";
                // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'alert-danger',
                                                           'titulo' => 'Ingresar',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'La empresa no tiene una sucursal' );

                header("Location:" . Route::routes('ingreso'));
                die();

            }

            if ($respuesta["activo"] == 1) {

                $arraySesionGestionEmpresarial = [
                    "validarSesion" => "ok",
                    "id" => $respuesta["id"],
                    "usuario" => $respuesta["usuario"],
                    "empresaId" => $respuesta["empresaId"],
                    "sucursalId" => $respuestaSucursal["id"]
                ];

                $_SESSION[CONST_SESSION_APP]["ingreso"] = $arraySesionGestionEmpresarial;

                $ultimoLogin = $usuario->actualizarIngreso();

                if ( $ultimoLogin ) {

                    header("Location:" . Route::names('politicas.index'));
                    die();

                }               
                
            } else {

                // $_SESSION[CONST_SESSION_APP]["flash"] = "El usuario aún no está activado";
                // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'alert-danger',
                                                           'titulo' => 'Ingresar',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'El usuario aún no está activado' );

                header("Location:" . Route::routes('ingreso'));
                die();

            }       

        } else {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Error al ingresar, vuelve a intentarlo";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'alert-danger',
                                                           'titulo' => 'Ingresar',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Error al ingresar, vuelve a intentarlo' );

            header("Location:" . Route::routes('ingreso'));
            die();

        }

    }
}
