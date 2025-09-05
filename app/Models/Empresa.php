<?php

namespace App\Models;

// require_once "app/conexion.php";
// require_once "app/Policies/EmpresaPolicy.php";

if ( file_exists ( "app/Policies/EmpresaPolicy.php" ) ) {
    require_once "app/Policies/EmpresaPolicy.php";
} else {
    require_once "../Policies/EmpresaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\EmpresaPolicy;

class Empresa extends EmpresaPolicy
{
    static protected $fillable = [
        'razonSocial', 'nombreCorto', 'rfc', 'calle', 'noExterior', 'noInterior', 'colonia', 'localidad', 'referencia', 'municipio', 'estado', 'pais', 'codigoPostal', 'nomenclaturaOT', 'logo', 'logoAnterior', 'imagen', 'imagenAnterior'
    ];

    static protected $type = [
        'id' => 'integer',
        'razonSocial' => 'string',
        'nombreCorto' => 'string',
        'rfc' => 'string',
        'calle' => 'string',
        'noExterior' => 'string',
        'noInterior' => 'string',
        'colonia' => 'string',
        'localidad' => 'string',
        'referencia' => 'string',
        'municipio' => 'string',
        'estado' => 'string',
        'pais' => 'string',
        'codigoPostal' => 'string',
        'nomenclaturaOT' => 'string',
        'logo' => 'string',
        'imagen' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "empresas";

    protected $keyName = "id";

    public $id = null;
    public $razonSocial;
    public $nombreCorto;
    public $rfc;
    public $calle;
    public $noExterior;
    public $noInterior;
    public $colonia;
    public $localidad;
    public $referencia;
    public $municipio;
    public $estado;
    public $pais;
    public $codigoPostal;
    public $nomenclaturaOT;
    public $logo;
    public $imagen;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR EMPRESAS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName ORDER BY razonSocial", $error);

        } else {

            $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->razonSocial = $respuesta["razonSocial"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->rfc = $respuesta["rfc"];
                $this->calle = $respuesta["calle"];
                $this->noExterior = $respuesta["noExterior"];
                $this->noInterior = $respuesta["noInterior"];
                $this->colonia = $respuesta["colonia"];
                $this->localidad = $respuesta["localidad"];
                $this->referencia = $respuesta["referencia"];
                $this->municipio = $respuesta["municipio"];
                $this->estado = $respuesta["estado"];
                $this->pais = $respuesta["pais"];
                $this->codigoPostal = $respuesta["codigoPostal"];
                $this->nomenclaturaOT = $respuesta["nomenclaturaOT"];
                $this->logo = $respuesta["logo"];
                $this->imagen = $respuesta["imagen"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR EL LOGO Y LA IMÁGEN (SI VIENE EN EL REQUEST)
        $directorio = "vistas/img/empresas/";

        // Agregar al request la ruta final del logo
        $rutaLogo = "";
        if ( $datos["logo"]["tmp_name"] != "" ) {

            $tmpNameLogo = $datos["logo"]["tmp_name"];
            $tipoLogo = $datos["logo"]["type"];

            do {
                $rutaLogo = fRandomNameImageFile($directorio, $tipoLogo);
            } while ( file_exists($rutaLogo) );

        }

        // Agregar al request la ruta final de la imágen
        $rutaImagen = "";
        if ( $datos["imagen"]["tmp_name"] != "" ) {

            $tmpNameImagen = $datos["imagen"]["tmp_name"];
            $tipoImagen = $datos["imagen"]["type"];

            do {
                $rutaImagen = fRandomNameImageFile($directorio, $tipoImagen);
            } while ( file_exists($rutaImagen) );

        }

        // Request con el nombre del archivo
        $datos["logo"] = $rutaLogo;
        $datos["imagen"] = $rutaImagen;

        $arrayPDOParam = array();
        $arrayPDOParam["razonSocial"] = self::$type["razonSocial"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["rfc"] = self::$type["rfc"];
        $arrayPDOParam["calle"] = self::$type["calle"];
        $arrayPDOParam["noExterior"] = self::$type["noExterior"];
        $arrayPDOParam["noInterior"] = self::$type["noInterior"];
        $arrayPDOParam["colonia"] = self::$type["colonia"];
        $arrayPDOParam["localidad"] = self::$type["localidad"];
        $arrayPDOParam["referencia"] = self::$type["referencia"];
        $arrayPDOParam["municipio"] = self::$type["municipio"];
        $arrayPDOParam["estado"] = self::$type["estado"];
        $arrayPDOParam["pais"] = self::$type["pais"];
        $arrayPDOParam["codigoPostal"] = self::$type["codigoPostal"];
        $arrayPDOParam["nomenclaturaOT"] = self::$type["nomenclaturaOT"];
        $arrayPDOParam["logo"] = self::$type["logo"];
        $arrayPDOParam["imagen"] = self::$type["imagen"];

        // return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (razonSocial, nombreCorto, rfc, calle, noExterior, noInterior, colonia, localidad, referencia, municipio, estado, pais, codigoPostal, nomenclaturaOT) VALUES (:razonSocial, :nombreCorto, :rfc, :calle, :noExterior, :noInterior, :colonia, :localidad, :referencia, :municipio, :estado, :pais, :codigoPostal, :nomenclaturaOT)", $datos, $arrayPDOParam, $error);

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO {$this->tableName} {$campos}", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {
            if ( $rutaLogo != "" ) move_uploaded_file($tmpNameLogo, $rutaLogo);
            if ( $rutaImagen != "" ) move_uploaded_file($tmpNameImagen, $rutaImagen);
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
    
        // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR EL LOGO Y LA IMÁGEN (SI VIENE EN EL REQUEST)
        $directorio = "vistas/img/empresas/";

        // Agregar al request la ruta final del logo
        $tmpNameLogo = $datos["logo"]["tmp_name"];
        $tipoLogo = $datos["logo"]["type"];

        if ( $datos["logo"]["tmp_name"] == "" ) {

            // Si no viene una nueva imágen dejamos la anterior
            $datos["logo"] = $datos["logoAnterior"];

        } else {

            $ruta = "";
            do {
                $ruta = fRandomNameImageFile($directorio, $tipoLogo);
            } while ( file_exists($ruta) );

            // Si viene una nueva imágen renombramos el Request con el nombre del archivo
            $datos["logo"] = $ruta;

        }

        // Agregar al request la ruta final de la imágen
        $tmpNameImagen = $datos["imagen"]["tmp_name"];
        $tipoImagen = $datos["imagen"]["type"];

        if ( $datos["imagen"]["tmp_name"] == "" ) {

            // Si no viene una nueva imágen dejamos la anterior
            $datos["imagen"] = $datos["imagenAnterior"];

        } else {

            $ruta = "";
            do {
                $ruta = fRandomNameImageFile($directorio, $tipoImagen);
            } while ( file_exists($ruta) );

            // Si viene una nueva imágen renombramos el Request con el nombre del archivo
            $datos["imagen"] = $ruta;

        }

        $arrayPDOParam = array();
        $arrayPDOParam["razonSocial"] = self::$type["razonSocial"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["rfc"] = self::$type["rfc"];
        $arrayPDOParam["calle"] = self::$type["calle"];
        $arrayPDOParam["noExterior"] = self::$type["noExterior"];
        $arrayPDOParam["noInterior"] = self::$type["noInterior"];
        $arrayPDOParam["colonia"] = self::$type["colonia"];
        $arrayPDOParam["localidad"] = self::$type["localidad"];
        $arrayPDOParam["referencia"] = self::$type["referencia"];
        $arrayPDOParam["municipio"] = self::$type["municipio"];
        $arrayPDOParam["estado"] = self::$type["estado"];
        $arrayPDOParam["pais"] = self::$type["pais"];
        $arrayPDOParam["codigoPostal"] = self::$type["codigoPostal"];
        $arrayPDOParam["nomenclaturaOT"] = self::$type["nomenclaturaOT"];
        $arrayPDOParam["logo"] = self::$type["logo"];
        $arrayPDOParam["imagen"] = self::$type["imagen"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE {$this->tableName} SET {$campos} WHERE id = :id", $datos, $arrayPDOParam, $error);

        // return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET razonSocial = :razonSocial, nombreCorto = :nombreCorto, rfc = :rfc, calle = :calle, noExterior = :noExterior, noInterior = :noInterior, colonia = :colonia, localidad = :localidad, referencia = :referencia, municipio = :municipio, estado = :estado, pais = :pais, codigoPostal = :codigoPostal, nomenclaturaOT = :nomenclaturaOT WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {
            // Si viene una imagen en el POST logo actualizarla físicamente
            if ( $tmpNameLogo != "" ) {
                move_uploaded_file($tmpNameLogo, $datos["logo"]);
                if ( $datos["logoAnterior"] != "" ) fDeleteFile($datos["logoAnterior"]);
            }

            // Si viene una imagen en el POST imagen actualizarla físicamente
            if ( $tmpNameImagen != "" ) {
                move_uploaded_file($tmpNameImagen, $datos["imagen"]);
                if ( $datos["imagenAnterior"] != "" ) fDeleteFile($datos["imagenAnterior"]);
            }
        }

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {
            if ( !is_null($this->logo) ) fDeleteFile($this->logo); // Eliminar físicamente el logo (si tiene)
            if ( !is_null($this->imagen) ) fDeleteFile($this->imagen); // Eliminar físicamente la imágen (si tiene)
        }

        return $respuesta;

    }
}
