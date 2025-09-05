<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ProveedorPolicy.php" ) ) {
    require_once "app/Policies/ProveedorPolicy.php";
} else {
    require_once "../Policies/ProveedorPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ProveedorPolicy;

class Proveedor extends ProveedorPolicy
{
    static protected $fillable = [
        'activo', 'personaFisica', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'razonSocial', 'nombreComercial', 'rfc', 'correo', 'credito', 'limiteCredito', 'telefono', 'estrellas', 'zona','domicilio', 'idCategoria','direccion'
    ];

    static protected $type = [
        'id' => 'integer',
        'activo' => 'integer',
        'codigo' => 'integer',
        'personaFisica' => 'integer',
        'nombre' => 'string',
        'apellidoPaterno' => 'string',
        'apellidoMaterno' => 'string',
        'razonSocial' => 'string',
        'nombreComercial' => 'string',
        'rfc' => 'string',
        'correo' => 'string',
        'credito' => 'integer',
        'limiteCredito' => 'decimal',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'telefono' => 'string',
        'zona' => 'integer',
        'domicilio' => 'string',
        'estrellas' => 'integer',
        'idCategoria' => 'integer',
        'contrasena' => 'string',
        'direccion' => 'string',
        'infomacionCompleta' => 'integer',

        'proveedorId' => 'integer',
        'observacion' => 'string',
        'tipoObservacion' => 'string',
        'archivoProveedorId' => 'string',
        'categoriaId' => 'string'
    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "proveedores";

    protected $keyName = "id";

    public $id = null;
    public $codigo;
    public $activo;
    public $personaFisica;
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $razonSocial;
    public $nombreComercial;
    public $rfc;
    public $correo;
    public $credito;
    public $limiteCredito;
    public $usuarioIdCreacion;
    public $usuarioIdActualizacion;
    public $telefono;
    public $domicilio;
    public $direccion;
    public $fechaCreacion;
    public $fechaActualizacion;
    public $nombreCompleto;



    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    CONSULTAR ULTIMO VALOR DEL CAMPO codigo
    =============================================*/
    public function consultarLastCodigo() {

        $respuesta = Conexion::queryUnique($this->bdName, "SELECT MAX(codigo) AS 'codigo' FROM $this->tableName", $error);

        return $respuesta;

    }

    /*=============================================
    MOSTRAR PROVEEDORES ACTIVOS
    =============================================*/
    public function consultarActivos()
    {
        $query = "SELECT    P.*,
                            CASE    WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, '')))
                                    WHEN P.personaFisica = 0 THEN P.razonSocial
                            END AS 'proveedor'
                FROM        {$this->tableName} P
                WHERE       P.activo = 1
                ORDER BY    proveedor";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    /*=============================================
    MOSTRAR PROVEEDORES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

                        return Conexion::queryAll($this->bdName, "SELECT *, 
                                                            CASE 
                                                                    WHEN personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', IFNULL(apellidoPaterno, ''), ' ', IFNULL(apellidoMaterno, '')))
                                                                    WHEN personaFisica = 0 THEN razonSocial 
                                                            END AS proveedor
                                                        FROM $this->tableName
                                                        ORDER BY proveedor;", $error);


        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT *, CASE WHEN personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', apellidoPaterno, ' ', IFNULL(apellidoMaterno, ''))) WHEN personaFisica = 0 THEN razonSocial END AS 'proveedor' FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT *, CASE WHEN personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', apellidoPaterno, ' ', IFNULL(apellidoMaterno, ''))) WHEN personaFisica = 0 THEN razonSocial END AS 'proveedor' FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->telefono = $respuesta["telefono"];
                $this->activo = $respuesta["activo"];
                $this->personaFisica = $respuesta["personaFisica"];
                $this->nombre = $respuesta["nombre"];
                $this->apellidoPaterno = $respuesta["apellidoPaterno"];
                $this->apellidoMaterno = $respuesta["apellidoMaterno"];
                $this->razonSocial = $respuesta["razonSocial"];
                $this->nombreComercial = $respuesta["nombreComercial"];
                $this->rfc = $respuesta["rfc"];
                $this->correo = $respuesta["correo"];
                $this->credito = $respuesta["credito"];
                $this->limiteCredito = $respuesta["limiteCredito"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->fechaCreacion = $respuesta["fechaCreacion"];
                $this->fechaActualizacion = $respuesta["fechaActualizacion"];
                $this->nombreCompleto = ( $this->personaFisica ) ? $respuesta["proveedor"] : null;
                $this->zona = $respuesta["zona"];
                $this->domicilio = $respuesta["domicilio"];
                $this->direccion = $respuesta["direccion"];
                $this->ubicacion = $respuesta["ubicacion"];
                $this->condicionContado = $respuesta["condicionContado"];
                $this->condicionCredito = $respuesta["condicionCredito"];
                $this->tiempoEntrega = $respuesta["tiempoEntrega"];
                $this->modalidadEntrega = $respuesta["modalidadEntrega"];
                $this->distribuidorAutorizado = $respuesta["distribuidorAutorizado"];
                $this->recursos = $respuesta["recursos"];
                $this->idCategoria = $respuesta["idCategoria"];
                $this->infomacionCompleta = $respuesta["infomacionCompleta"];
                
                $this->tags = ( !is_null($respuesta["tags"]) ) ? json_decode($respuesta["tags"], true) : array();

                $this->estrellas = $respuesta["estrellas"];
                // $this->nombreCompleto = ( $this->personaFisica ) ? fNombreCompleto($respuesta["nombre"], $respuesta["apellidoPaterno"], $respuesta["apellidoMaterno"]) : null;
            }
            
            return $respuesta;

        }

    }

    public function crear($datos) {

        // Agregar al request para actualizar el registro
        $lastCodigo = $this->consultarLastCodigo();

        if ( $lastCodigo === false ) {
            return false;
        }

        // Agregar al request para especificar el usuario que creó el Proveedor
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        // Agregar al request para especificar el consecutivo del Proveedor
        $datos["codigo"] = (int) $lastCodigo["codigo"] + 1;

        // Quitar las comas de los campos decimal
        if ( isset($datos["limiteCredito"]) ) {
            $datos["limiteCredito"] = str_replace(',', '', $datos["limiteCredito"]);
        }
        // Modificar el contenido de los checkboxes
        $datos["activo"] = ( isset($datos["activo"]) && mb_strtolower($datos["activo"]) == "on" ) ? "1" : "0";
        $datos["personaFisica"] = ( isset($datos["personaFisica"]) && mb_strtolower($datos["personaFisica"]) == "on" ) ? "1" : "0";
        $datos["credito"] = ( isset($datos["credito"]) && mb_strtolower($datos["credito"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();
        $arrayPDOParam["codigo"] = self::$type["codigo"];
        $arrayPDOParam["activo"] = self::$type["activo"];
        $arrayPDOParam["personaFisica"] = self::$type["personaFisica"];
        if ( !isset($datos["razonSocial"]) ) {
            $arrayPDOParam["nombre"] = self::$type["nombre"];
            $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
            $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        } else {
            $arrayPDOParam["razonSocial"] = self::$type["razonSocial"];
        }
        $arrayPDOParam["nombreComercial"] = self::$type["nombreComercial"];
        $arrayPDOParam["rfc"] = self::$type["rfc"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["credito"] = self::$type["credito"];
        if ( isset($datos["limiteCredito"]) ) {
            $arrayPDOParam["limiteCredito"] = self::$type["limiteCredito"];
        }
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó el Proveedor
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Quitar las comas de los campos decimal
        if ( isset($datos["limiteCredito"]) ) {
            $datos["limiteCredito"] = str_replace(',', '', $datos["limiteCredito"]);
        }
        // Modificar el contenido de los checkboxes
        $datos["activo"] = ( isset($datos["activo"]) && mb_strtolower($datos["activo"]) == "on" ) ? "1" : "0";
        // $datos["personaFisica"] = ( isset($datos["personaFisica"]) && mb_strtolower($datos["personaFisica"]) == "on" ) ? "1" : "0";
        $datos["credito"] = ( isset($datos["credito"]) && mb_strtolower($datos["credito"]) == "on" ) ? "1" : "0";
        
        $arrayPDOParam = array();
        // $arrayPDOParam["codigo"] = self::$type["codigo"];
        $arrayPDOParam["activo"] = self::$type["activo"];
        // $arrayPDOParam["personaFisica"] = self::$type["personaFisica"];
        if ( !isset($datos["razonSocial"]) ) {
            $arrayPDOParam["nombre"] = self::$type["nombre"];
            $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
            $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        } else {
            $arrayPDOParam["razonSocial"] = self::$type["razonSocial"];
        }
        $arrayPDOParam["nombreComercial"] = self::$type["nombreComercial"];
        $arrayPDOParam["rfc"] = self::$type["rfc"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["telefono"] = self::$type["telefono"];
        $arrayPDOParam["direccion"] = self::$type["direccion"];
        $arrayPDOParam["credito"] = self::$type["credito"];
        if ( isset($datos["limiteCredito"]) ) {
            $arrayPDOParam["limiteCredito"] = self::$type["limiteCredito"];
        }
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // return false; // No se permiten eliminar Proveedores

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    /*=============================================
    FUNCION PARA ACTUALIZAR DATOS PRINCIPALES DEL PROVEEDOR

    @params $datos Arreglo de datos
    @return boolean Respuesta de la consulta
    =============================================*/
    public function actualizarDatosIncialesProveedor($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $datos["idProveedor"];
        $datos["infomacionCompleta"] = 1;

        $arrayPDOParam = array();

        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
        $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        $arrayPDOParam["zona"] = self::$type["zona"];
        $arrayPDOParam["domicilio"] = self::$type["domicilio"];
        $arrayPDOParam["infomacionCompleta"] = self::$type["infomacionCompleta"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

        /*=============================================
    FUNCION PARA MOVER LOS ARCHIVOS DE SOLICITUD 
    PROVEEDOR A ARCHIVOS PROVEEDOR
    
    @params $archivos Array de archivos de solicitud
    @return 
    =============================================*/
    public function moverArchivosSolicitudAProveedores($archivos){

        $respuesta = true;

        $solicitudProveedor = new SolicitudProveedor();
        $proveedorArchivos = new ProveedorArchivos();

        foreach ($archivos as $key => $value) {
            
            $directorioNuevoAbsoluto = $_SERVER['DOCUMENT_ROOT'] . CONST_APP_FOLDER . $proveedorArchivos->directorioArchivo($value["tipo"]);
            $directorioArchivoNuevoAbsoluto = $directorioNuevoAbsoluto . $value["archivo"];
            $rutaNuevaRelativa = $proveedorArchivos->directorioArchivo($value["tipo"]) . $value["archivo"];

            $directorioOriginalAbsoluto =  $_SERVER['DOCUMENT_ROOT'] . CONST_APP_FOLDER . $value["ruta"];

            if (!is_dir($directorioNuevoAbsoluto)) { 
               mkdir($directorioNuevoAbsoluto, 0775, true);
            }

            // MOVER ARCHIVOS
            if(moverArchivos($directorioOriginalAbsoluto,$directorioArchivoNuevoAbsoluto)){

                $proveedorArchivos = new ProveedorArchivos();
                $proveedorArchivos->proveedorId = $this->id;
                
                // INSERTAR DATOS EN TABLA DE PROVEEDOR ARCHIVOS
                $insercionExitosa = $proveedorArchivos->insertarDatosProveedorArchivos($value,$rutaNuevaRelativa);   

                // ACTUALIZAR RUTA EN TABLA SOLICITUD ARCHIVOS
                if($insercionExitosa){
                    $respuesta = $solicitudProveedor->actualizarRutaArchivoSolicitud($value,$rutaNuevaRelativa);
                }

            };
        }
        return $respuesta;
    }

    public function observacionePorProveedor(){

        return Conexion::queryAll($this->bdName, "SELECT 
                                                    PO.*,
                                                    PA.tipo as tipoArchivo,
                                                    PA.titulo as tituloArchivo
                                                FROM 
                                                    proveedor_observaciones PO 
                                                LEFT JOIN proveedor_archivos PA ON PA.id = PO.archivoProveedorId
                                                where PO.proveedorId =$this->id
                                                ORDER BY PO.fechaCreacion DESC
                                                ", $error);

    }

    public function ultimaObservacion(){
        return Conexion::queryUnique(
            $this->bdName,
            "SELECT 
                PO.*,
                PA.tipo AS tipoArchivo,
                PA.titulo AS tituloArchivo
            FROM proveedor_observaciones PO
            INNER JOIN proveedor_archivos PA 
                ON PA.id = PO.archivoProveedorId
            WHERE PO.proveedorId = $this->id
            ORDER BY PO.fechaCreacion DESC
            LIMIT 1",
            $error
        );
    }
    
    public function crearObservacioProveedor($datos){

        $arrayPDOParam = array();
        $arrayPDOParam["observacion"] = self::$type["observacion"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["tipoObservacion"] = self::$type["tipoObservacion"];
        $arrayPDOParam["archivoProveedorId"] = self::$type["archivoProveedorId"];

        $datos["observacion"] = $datos["detalleObservacion"];
        $datos["proveedorId"] =$datos["proveedorId"];
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["categoriaId"] = $datos["categoriaIdObservacion"];
        $datos["tipoObservacion"] = $datos["tipoObservacion"];
 
        if($datos["tipoObservacion"] === "ARCHIVO"){
            $datos["archivoProveedorId"] = $this->id;
        }else{
            $datos["archivoProveedorId"] = null;
        }

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO proveedor_observaciones " . $campos, $datos, $arrayPDOParam, $error);
    }
}
