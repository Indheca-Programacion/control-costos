<?php

namespace App\Requests;
use Exception;

// require_once "app/Controllers/Validacion.php";
if ( file_exists ( "app/Controllers/Validacion.php" ) ) {
    require_once "app/Controllers/Validacion.php";
} else {
    require_once "../Controllers/Validacion.php";
}

use App\Controllers\Validacion;

class Request
{
    static public function value() {

		if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

            if ( $_FILES )  {

                // Si hay campos de tipo file ($_FILES) en el formulario agregarlos al resto de variables $_POST
                $respuesta = $_POST;

                foreach ($_FILES as $key => $value) {
                    $respuesta[$key] = $value;
                }
                
                return $respuesta;

            }
            
        	return $_POST;

        } else {

            header("Location:" . $_SERVER['HTTP_REFERER']);
            die();
            
        }

    }

    static public function method() {
    	
    	return isset($_REQUEST['_method']) ? $_REQUEST['_method'] : $_SERVER['REQUEST_METHOD'];

    }

    static public function validating(array $fillables, array $reglas, array $mensajes)
    {
        // return self::value();
        
        ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) ? $peticionAjax = true : $peticionAjax = false;

        //Validar primero que el Token sea valido

        if ( !self::validatingToken($error) ) {

            $arrayErrores = array();
            array_push($arrayErrores, $error);
        
            $_SESSION[CONST_SESSION_APP]["errors"] = $arrayErrores;
        
            if ( $peticionAjax ) {
                throw new Exception("NO TIENES SESION");
            }
        }
    
        $arrayErrores = array();
        $valores = self::value();
        $respuesta = array();

        foreach ($valores as $key => $value) {
            
            if ( !$peticionAjax ) $_SESSION[CONST_SESSION_APP]["old"][$key] = $value;

            if ( array_search($key, $fillables) !== false) {

                // Solo validar si existe la regla para el campo especifico
                if ( isset($reglas[$key]) ) {
                    $reglasCampo = explode("|", $reglas[$key]);

                    // Validar el campo
                    $campoValido = true;
                    foreach ($reglasCampo as $reglaCampo) {

                        $reglaCampoArray = explode(":", $reglaCampo);
                        if ( !Validacion::validar($key, $value, $reglaCampoArray) ) {

                            $campoValido = false;

                            // Buscar el error en el Arreglo de Mensajes
                            $keyMessage = $key . "." . $reglaCampoArray[0];
                            if ( isset($mensajes[$keyMessage]) ) {

                                // array_push($arrayErrores, $mensajes[$keyMessage]);
                                $arrayErrores[$key] = $mensajes[$keyMessage];

                            } else {

                                array_push($arrayErrores, "El campo no cumplió con la regla de validación '" . $keyMessage . "'");
                                
                            }

                        }

                    }

                } else {

                    $campoValido = true;

                }

                if ( $campoValido) {

                    $respuesta[$key] = $value;

                } 

            } 

        }
        
        if ( $arrayErrores ) {

            $_SESSION[CONST_SESSION_APP]["errors"] = $arrayErrores;
            if ( $peticionAjax ) return;
            header("Location:" . $_SERVER['HTTP_REFERER']);
            die();
        }

        return $respuesta;

    }

    static public function validatingToken(&$error) {

        $valores = self::value();
        $arrayErrores = array();

        $respuesta = true;

        if ( !isset($valores["_token"]) || !Validacion::validar("_token", $valores["_token"], ['required']) ) {

            $error = "No fue proporcionado un Token";
            $respuesta = false;
        
        } elseif ( !Validacion::validar("_token", $valores["_token"], ['token']) ) {

            $error = "El Token proporcionado no es válido";
            $respuesta = false;

        }

        // deleteToken();

        return $respuesta;

    }
}
