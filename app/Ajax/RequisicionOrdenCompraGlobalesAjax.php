<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/RequisicionOrdenCompraGlobales.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\RequisicionOrdenCompraGlobales;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class RequisicionOrdenCompraGlobalesAjax
{
    public function mostrarTabla()
    {
        $modelo = new RequisicionOrdenCompraGlobales;
        $registrosBD = $modelo->consultar();

        $columnas = [];
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "campo1" ]);
        array_push($columnas, [ "data" => "campo2" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        $registros = [];

        foreach ($registrosBD as $key => $value) {
            $rutaEdit = Route::names('requisicionordencompraglobales.edit', $value['id']);
            $rutaDestroy = Route::names('requisicionordencompraglobales.destroy', $value['id']);
            $folio = fString($value['campo1']);

            array_push($registros, [
                "consecutivo" => ($key + 1),
                "campo1" => fString($value["campo1"]),
                "campo2" => fString($value["campo2"]),
                "acciones" => "
                    <a href='$rutaEdit' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                    <form method='POST' action='$rutaDestroy' style='display: inline'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='$token'>
                        <button type='button' class='btn btn-xs btn-danger eliminar' folio='$folio'>
                            <i class='far fa-times-circle'></i>
                        </button>
                    </form>"
            ]);
        }

        $respuesta = [
            'codigo' => 200,
            'error' => false,
            'datos' => [
                'columnas' => $columnas,
                'registros' => $registros
            ]
        ];

        echo json_encode($respuesta);
    }

    public $token;
    public $campo;

    public function agregar()
    {
        $respuesta = [ "error" => false ];

        $usuario = new Usuario;
        if (usuarioAutenticado()) {
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

            if (!Autorizacion::perfil($usuario, CONST_ADMIN) &&
                !Autorizacion::permiso($usuario, "requisicionordencompraglobales", "crear")) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No autorizado.";
            }

        } else {
            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no autenticado.";
        }

        if (!isset($this->token) || !Validacion::validar("_token", $this->token, ['required'])) {
            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Token requerido.";
        } elseif (!Validacion::validar("_token", $this->token, ['token'])) {
            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Token inválido.";
        }

        if (!Validacion::validar("campo", $this->campo, ['max', '60'])) {
            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Campo muy largo.";
        }

        if ($respuesta["error"]) {
            echo json_encode($respuesta);
            return;
        }

        $modelo = new RequisicionOrdenCompraGlobales;
        $datos = [
            "campo" => $this->campo,
            "otroCampo" => ''
        ];

        $respuesta["respuesta"] = false;

        if ($modelo->consultar("campo", $this->campo)) {
            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Registro duplicado.";
        } else {
            if ($modelo->crear($datos)) {
                $respuesta["respuestaMessage"] = "Registro creado correctamente.";
                $respuesta["respuesta"] = $modelo->consultar("campo", $this->campo);

                if (!$respuesta["respuesta"]) {
                    $respuesta["error"] = true;
                    $respuesta["errorMessage"] = "Refresque para ver el nuevo registro.";
                }
            } else {
                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Error al guardar.";
            }
        }

        echo json_encode($respuesta);
    }
}

$ajax = new RequisicionOrdenCompraGlobalesAjax();

if (isset($_POST["campo"])) {
    $ajax->token = $_POST["_token"];
    $ajax->campo = $_POST["campo"];
    $ajax->agregar();
} else {
    $ajax->mostrarTabla();
}