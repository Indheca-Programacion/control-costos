<?php

namespace App\Controllers;

require_once "app/Models/Proveedor.php";
require_once "app/Models/Sucursal.php";
require_once "app/Requests/LoginRequest.php";

use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Requests\LoginRequest;
use App\Route;

class LoginProveedorController
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

        $proveedor = New Proveedor;
        $respuesta = $proveedor->consultar("rfc", $valor);

        if ( mb_strtoupper($respuesta["rfc"]) == mb_strtoupper($valor) && $respuesta["contrasena"] == $encriptar ) {

            if ($respuesta["activo"] == 1) {

                $arraySesion = [
                    "validarSesion" => "ok",
                    "id" => $respuesta["id"],
                    "usuario" => $respuesta["razonSocial"]
                ];

                $_SESSION[CONST_SESSION_APP]["ingresoProveedor"] = $arraySesion;

                header("Location:" . Route::routes('inicio'));
                die();
                
            } else {

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'alert-danger',
                                                           'titulo' => 'Ingresar',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'El usuario aún no está activado' );

                header("Location:" . Route::routes('ingreso'));
                die();

            }       

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'alert-danger',
                                                           'titulo' => 'Ingresar',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'La contraseña o el usuario es incorrecto' );

            header("Location:" . Route::routes('ingreso'));
            die();

        }

    }
}
