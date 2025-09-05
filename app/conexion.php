<?php

namespace App;

use PDO;

class Conexion
{


	static public function conectar(){

        $link = new PDO("mysql:host=".CONST_BD_SERVER.";
                            dbname=".CONST_BD_SECURITY,
                            CONST_BD_USER,
                            CONST_BD_PASSWORD);

        $link->exec("set names utf8");

        return $link;

	}

	static public function conectarBD($BD){

        $link = new PDO("mysql:host=".CONST_BD_SERVER.";
                            dbname=".$BD,
                            CONST_BD_USER,
                            CONST_BD_PASSWORD);

        $link->exec("set names utf8");

        return $link;

	}

    /*=============================================
    CONSULTA QUERY ALL
    =============================================*/
    static public function queryAll($BD, $query, &$error = null) {

        try {

            $con = self::conectarBD($BD);

            $stmt = $con->prepare($query);

            $stmt -> execute();
            
            $respuesta = $stmt -> fetchAll();

        }

        catch( PDOException $Exception ) {

            $error = (int)$Exception->getCode() . ": ". $Exception->getMessage();

        }

        $stmt = null;
        $con = null;

        return $respuesta;

    }

    /*=============================================
    CONSULTA QUERY UNIQUE
    =============================================*/
    static public function queryUnique($BD, $query, &$error = null) {

        try {

            $con = self::conectarBD($BD);

            $stmt = $con->prepare($query);

            $stmt -> execute();

            $respuesta = $stmt -> fetch();            

        }

        catch( PDOException $Exception ) {

            $error = (int)$Exception->getCode() . ": ". $Exception->getMessage();

        }

        $stmt = null;
        $con = null;

        return $respuesta;

    }

    /*=============================================
    CONSULTA QUERY UNIQUE TABLE (SE USA EN BUSQUEDAS DONDE EL UNIQUE LO CONFORMAN VARIOS CAMPOS)
    =============================================*/
    static public function queryUniqueTable($BD, $query, $datos, &$error = null) {

        try {

            $con = self::conectarBD($BD);

            $stmt = $con->prepare($query);

            foreach ($datos as $key => $value) {

                if ($key == "id" || strpos($key, "Id")) {

                    $stmt->bindParam(":$key", $datos[$key], PDO::PARAM_INT);

                } else {

                    $stmt->bindParam(":$key", $datos[$key], PDO::PARAM_STR);
                    
                }

            }

            $stmt -> execute();

            $respuesta = $stmt -> fetch();

        }

        catch( PDOException $Exception ) {

            $error = (int)$Exception->getCode() . ": ". $Exception->getMessage();

        }

        $stmt = null;
        $con = null;

        return $respuesta;

    }

    /*=============================================
    EJECUTA QUERY EXECUTE
    =============================================*/
    static public function queryExecute($BD, $query, $datos, $arrayPDOParam, &$error = null, &$lastId = null) {
        
        try {

            $con = self::conectarBD($BD);

            $stmt = $con->prepare($query);
            
            foreach ($arrayPDOParam as $key => $value) {

                switch ($value) {

                    case "integer":
                        $parametro = PDO::PARAM_INT;
                        break;

                    case "string":
                        $parametro = PDO::PARAM_STR;
                        break;

                    case "date":
                        $parametro = PDO::PARAM_STR;
                        break;

                    case "boolean":
                        $parametro = PDO::PARAM_BOOL;
                        break;

                    case "null":
                        $parametro = PDO::PARAM_NULL;
                        break;

                    default:
                        $parametro = PDO::PARAM_STR;

                }

                if ( $datos[$key] === "" ) {
                    $null = CONST_NULL;
                    $stmt->bindParam(":$key", $null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindParam(":$key", $datos[$key], $parametro);
                }

            }
    
            $respuesta = $stmt -> execute();

            if ( $respuesta && !is_null($lastId) ) {

                $lastId =  $con->lastInsertId();

            }

        }

        catch( PDOException $Exception ) {

            $error = (int)$Exception->getCode() . ": ". $Exception->getMessage();

        }

        $stmt = null;
        $con = null;

        return $respuesta;

    }

    public static function transaction($sqls, $parameteres) { // TODO: falla.
        try {
            $con = self::conectarBD($BD);
            // DB::connect()->beginTransaction();
            $con->beginTransaction();
            foreach( $sqls as $index => $sql ) {
                // $rs = DB::connect()->prepare($sql);
                $stmt = $con->prepare($query);
                $rs->execute($parameteres[$index]);
            }
            // DB::connect()->commit();
            $con->commit();
            return true;
        } catch (PDOException $e) {
            print_r($e);
            // DB::connect()->rollBack();
            $con->rollBack();
            return false;
        }
    }

}
