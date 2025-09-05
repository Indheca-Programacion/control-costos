<?php

namespace App\Controllers;

class Validacion
{
    static public function validar($campo, &$valor, array $tipoValidacion) {
    $tipo = $tipoValidacion[0];

    if ($tipo != "image" && $tipo != "requiredFile" && $tipo != "type" && $tipo != "maxSize" && is_string($valor)) {
        $valor = trim($valor);
    }

        switch ($tipo) {
            case "token":
                return self::validaToken($valor);
                break;
            case "required":
                return self::validaRequired($valor);
                break;
            case "requiredFile":
                if ( $valor["tmp_name"] == "" ) {
                    return false;
                }
                return true;
                break;
            case "value":
                return self::validaValor($valor, $tipoValidacion[1]);
            case "string":

                return true;
                break;
            case "integer":
                if ( $valor == "" ) {
                    return true;
                }
                return ( self::validaInteger($valor) === false ) ? false : true;
                break;
            case "decimal":
                if ( $valor == "" ) {
                    return true;
                }
                return ( self::validaDecimal($valor) === false ) ? false : true;
                break;
            case "email":
                if ( $valor == "" ) {
                    return true;
                }
                return self::validaEmail($valor);
                break;
            case "min":
                if ( $valor == "" ) {
                    return true;
                }
                return self::validaLongitudMinima($valor, $tipoValidacion[1]);
                break;
            case "minValue":
                if ( $valor == "" ) {
                    return true;
                }
                return self::validaValorMinimo($valor, $tipoValidacion[1]);
                break;
            case "max":
                if ( $valor == "" ) {
                    return true;
                }
                return self::validaLongitudMaxima($valor, $tipoValidacion[1]);
                break;
            case "maxDecimal":
                // return self::validaLongitudMaxima($valor, $tipoValidacion[1]);
                return self::validaLongitudMaxima(str_replace(',', '', $valor), $tipoValidacion[1]);
                break;
            case "date":
                if ( $valor == "" ) {
                    return true;
                }
                $fechaArray = explode('/', $valor);
                return self::validaDate($fechaArray);
                break;
            case "datetime":
                if ( $valor == "" ) {
                    return true;
                }
                $fechaArray = explode(' ', $valor);
                $fechaArray = explode('/', $fechaArray[0]);
                return self::validaDate($fechaArray);
                break;
            case "image":
                if ( $valor["tmp_name"] == "" ) {
                    return true;
                }
                $respuesta = false;
                $i = 1;
                while ( $i < count($tipoValidacion) ) { 
                    if ( $valor["type"] == ( $tipoValidacion[0] . "/" . $tipoValidacion[$i] )  ) {
                        $respuesta = true;
                        break;
                    }
                    $i++;
                }
                return $respuesta;
                break;
            case "type":
                if ( $valor["tmp_name"] == "" ) {
                    return true;
                }
                $respuesta = false;
                $i = 1;
                while ( $i < count($tipoValidacion) ) { 
                    if ( $valor["type"] == $tipoValidacion[$i] ) {
                        $respuesta = true;
                        break;
                    }
                    $i++;
                }
                return $respuesta;
                break;
            case "maxSize":
                if ( $valor["tmp_name"] == "" ) {
                    return true;
                }

                $valorMaximo = (float) $tipoValidacion[1];
                return !($valor["size"] > $valorMaximo);
                break;
            case "unique":
                if ( $valor == "" ) {
                    return true;
                }

                $bdName = CONST_BD_SECURITY;

                $tableName = $tipoValidacion[1];
                $datos[$campo] = $valor;

                $query = "SELECT * FROM $tableName WHERE $campo = :$campo";

                if ( count($tipoValidacion) > 2 ) {

                    $datos[$tipoValidacion[2]] = $tipoValidacion[3];
                    $query .= " AND $tipoValidacion[2] <> :$tipoValidacion[2]";

                }

                $respuesta = \App\Conexion::queryUniqueTable($bdName, $query, $datos, $error);

                return !$respuesta;
                break;
            case "uniqueKeys":
                if ( $valor == "" ) {
                    return true;
                }

                $bdName = CONST_BD_SECURITY;
                $tableName = $tipoValidacion[1];
                $datos[$campo] = $valor;

                $whereKeys = "";
                $keyBlank = false; // Si hay un campo en blanco regresar validacion true

                $i = 0;
                while ( $i < count($tipoValidacion) ) { 
                    if ( $i >= 2 && $tipoValidacion[$i] != "id") {
                        $whereKeys .= $tipoValidacion[$i];
                        $whereKeys .= " = :";
                        $whereKeys .= $tipoValidacion[$i];
                        
                        $datos[$tipoValidacion[$i]] = $tipoValidacion[$i+1];
                        if ( $tipoValidacion[$i+1] == "" ) {
                            $keyBlank = true; // Hay un campo key en blanco
                        }
                    }   
                    $i += 2;
                }

                if ( $keyBlank ) {
                    return true; // No es necesario seguir con la validacion
                }

                if ( $whereKeys != "") {
                    $whereKeys .= " AND ";
                }

                $query = "SELECT * FROM $tableName WHERE $whereKeys $campo = :$campo";

                if ( count($tipoValidacion) > 4 ) {

                    $datos[$tipoValidacion[4]] = $tipoValidacion[5];
                    $query .= " AND $tipoValidacion[4] <> :$tipoValidacion[4]";

                }

                $respuesta = \App\Conexion::queryUniqueTable($bdName, $query, $datos, $error);

                return !$respuesta;
                break;

            case "uniqueFields":
                if ( $valor == "" ) {
                    return true;
                }

                $bdName = CONST_BD_SECURITY;
                $tableName = $tipoValidacion[1];
                $datos[$campo] = $valor;

                $whereKeys = "";

                $query = "SELECT * FROM $tableName WHERE $campo = :$campo";

                $i = 0;
                while ( $i < count($tipoValidacion) ) { 
                    if ( $tipoValidacion[$i] == "id") {
                        break;
                    }
                    if ( $i >= 2 ) { // Indice 0 es el nombre de la regla, el indice 1 es el nombre de la tabla
                        $whereKeys .= " AND ";
                        // Si el campo está vacio el query debe preguntar ISNULL
                        if ( \App\Requests\Request::value()[$tipoValidacion[$i]] == "") {
                            $whereKeys .= "ISNULL(";
                            $whereKeys .= $tipoValidacion[$i];
                            $whereKeys .= ")";
                        } else {
                            $whereKeys .= $tipoValidacion[$i];
                            $whereKeys .= " = :";
                            $whereKeys .= $tipoValidacion[$i];

                            $datos[$tipoValidacion[$i]] = \App\Requests\Request::value()[$tipoValidacion[$i]];
                        }
                    }   
                    $i++;
                }

                if ( $whereKeys != "") {
                    $query .= $whereKeys;
                }

                if ( count($tipoValidacion) > $i ) {
                    $datos[$tipoValidacion[$i]] = $tipoValidacion[$i+1];
                    $query .= " AND $tipoValidacion[$i] <> :$tipoValidacion[$i]";
                }

                $respuesta = \App\Conexion::queryUniqueTable($bdName, $query, $datos, $error);

                return !$respuesta;
                break;

            case "exists":
                if ( $valor == "" ) {
                    return true;
                }

                $bdName = CONST_BD_SECURITY;
                $tableName = $tipoValidacion[1];
                $datos[$tipoValidacion[2]] = $valor;

                $query = "SELECT * FROM $tableName WHERE $tipoValidacion[2] = :$tipoValidacion[2]";

                $respuesta = \App\Conexion::queryUniqueTable($bdName, $query, $datos, $error);

                // var_dump($query);
                // var_dump($datos);
                // var_dump($respuesta);
                // die();

                return $respuesta;
                break;

            case "active":
                if ( $valor == "" ) {
                    return true;
                }

                $bdName = CONST_BD_SECURITY;
                $tableName = $tipoValidacion[1];
                $datos[$tipoValidacion[2]] = $valor;

                $query = "SELECT * FROM $tableName WHERE $tipoValidacion[2] = :$tipoValidacion[2] AND $tipoValidacion[3] = 1";

                $respuesta = \App\Conexion::queryUniqueTable($bdName, $query, $datos, $error);

                return $respuesta;
                break;
            case "accepted":
                return $valor !== "0";
                break;
            case "requiredArchivo":
                return is_array($valor) && isset($valor['name']) && !empty($valor['name']);
                break;
            default:
                return false;
        }

    }

    static function validaToken($valor) {
        
        return $valor == token();

    }

    static function validaRequired($valor) {
        
        if ( is_null($valor) || $valor == "") {

            return false;

        }

        return true;
    }

    static function validaValor($valor, $valor2) {

        $valor2 = trim($valor2);

        return mb_strtolower($valor) == mb_strtolower($valor2);

    }

    // FILTER_VALIDATE_BOOLEAN
    static function validaInteger($valor) {
                
        return filter_var($valor, FILTER_VALIDATE_INT);
    
    }

    static function validaDecimal($valor) {

        $valor = str_replace(',', '', $valor);
                
        return filter_var($valor, FILTER_VALIDATE_FLOAT);    

    }

    static function validaEmail($valor) {
                
        return filter_var($valor, FILTER_VALIDATE_EMAIL);    

    }

    static function validaLongitudMinima($valor, int $longitudMinima) {
                
        return !(strlen($valor) < $longitudMinima);

    }

    static function validaValorMinimo($valor, float $valorMinimo) {

        $valor = (float) str_replace(',', '', $valor);
    
        return !($valor < $valorMinimo);

    }

    static function validaLongitudMaxima($valor, int $longitudMaxima) {
                
        return !(strlen($valor) > $longitudMaxima);

    }

    static function validaDate(array $fechaArray) {
        
        if  ( count($fechaArray) != 3 ) {
            return false;
        }

        $dia = $fechaArray[0];
        $mes = $fechaArray[1];
        $year = $fechaArray[2];

        if ( !filter_var($mes, FILTER_VALIDATE_INT) ) {
            $mes = fNumeroMes($mes);
        }

        return checkdate($mes, $dia, $year);

    }
}
