<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ConfiguracionCorreoElectronicoPolicy.php" ) ) {
    require_once "app/Policies/ConfiguracionCorreoElectronicoPolicy.php";
} else {
    require_once "../Policies/ConfiguracionCorreoElectronicoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ConfiguracionCorreoElectronicoPolicy;

class ConfiguracionCorreoElectronico extends ConfiguracionCorreoElectronicoPolicy
{
    static protected $fillable = [
        'servidor', 'puerto', 'puertoSSL', 'usuario', 'contrasena', 'visualizacionCorreo', 'visualizacionNombre', 'respuestaCorreo', 'respuestaNombre', 'comprobacionCorreo'
    ];

    static protected $type = [
        'id' => 'integer',
        'servidor' => 'string',
        'puerto' => 'integer',
        'puertoSSL' => 'integer',
        'usuario' => 'string',
        'contrasena' => 'string',
        'visualizacionCorreo' => 'string',
        'visualizacionNombre' => 'string',
        'respuestaCorreo' => 'string',
        'respuestaNombre' => 'string',
        'comprobacionCorreo' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "configuracion_correos";

    protected $keyName = "id";

    public $id = null;    
    public $servidor;
    public $puerto;
    public $puertoSSL;
    public $usuario;
    public $contrasena;
    public $visualizacionCorreo;
    public $visualizacionNombre;
    public $respuestaCorreo;
    public $respuestaNombre;
    public $comprobacionCorreo;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CONFIGURACION CORREO ELECTRÓNICO
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT CC.* FROM $this->tableName CC ORDER BY CC.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->servidor = $respuesta["servidor"];
                $this->puerto = $respuesta["puerto"];
                $this->puertoSSL = $respuesta["puertoSSL"];
                $this->usuario = $respuesta["usuario"];
                $this->contrasena = $respuesta["contrasena"];
                $this->visualizacionCorreo = $respuesta["visualizacionCorreo"];
                $this->visualizacionNombre = $respuesta["visualizacionNombre"];
                $this->respuestaCorreo = $respuesta["respuestaCorreo"];
                $this->respuestaNombre = $respuesta["respuestaNombre"];
                $this->comprobacionCorreo = $respuesta["comprobacionCorreo"];
            }

            return $respuesta;

        }
    }

    public function consultarPerfilesCerrarGasto()
    {
        $query = "SELECT    CCR.perfilesGastos
                FROM        configuracion_correo_gastos CCR
                WHERE       CCR.configuracionCorreoId = {$this->id}";

        $resultado = Conexion::queryUnique($this->bdName, $query, $error);

        $this->perfilesCerrarGasto = json_decode($resultado["perfilesGastos"]);
    }
    
    public function consultarPerfilesCrear()
    {
        $query = "SELECT    CCR.perfilesCrear
                FROM        configuracion_correo_requisiciones CCR
                WHERE       CCR.configuracionCorreoId = {$this->id}";

        $resultado = Conexion::queryUnique($this->bdName, $query, $error);

        $this->perfilesCrear = json_decode($resultado["perfilesCrear"]);
    }

    public function consultarEstatusModificarUsuarioCreacion()
    {
        $query = "SELECT    CCR.estatusModificarUsuarioCreacion
                FROM        configuracion_correo_requisiciones CCR
                WHERE       CCR.configuracionCorreoId = {$this->id}";

        $resultado = Conexion::queryUnique($this->bdName, $query, $error);

        $this->estatusModificarUsuarioCreacion = json_decode($resultado["estatusModificarUsuarioCreacion"]);
    }

    public function consultarEstatusModificarPerfiles()
    {
        $query = "SELECT    CCR.estatusModificarPerfiles
                FROM        configuracion_correo_requisiciones CCR
                WHERE       CCR.configuracionCorreoId = {$this->id}";

        $resultado = Conexion::queryUnique($this->bdName, $query, $error);

        // $this->estatusModificarPerfiles = json_decode($resultado["estatusModificarPerfiles"]);

        $resultado = json_decode($resultado["estatusModificarPerfiles"]);

        $arrayEstatusModificarPerfiles = [];
        foreach ($resultado as $key => $value) {
            $estatus = $value->id;
            // if ( !isset($arrayEstatusModificarPerfiles[$estatus]) ) $arrayEstatusModificarPerfiles[$estatus] = [];
            
            // array_push($arrayEstatusModificarPerfiles[$estatus], $value->perfiles);
            $arrayEstatusModificarPerfiles[$estatus] = $value->perfiles;
        }

        $this->estatusModificarPerfiles = $arrayEstatusModificarPerfiles;
    }

    public function consultarDocumentos()
    {
        $query = "SELECT    CCR.uploadDocumentos, CCR.usuarioUploadDocumento, CCR.perfilesUploadDocumento
                FROM        configuracion_correo_requisiciones CCR
                WHERE       CCR.configuracionCorreoId = {$this->id}";

        $resultado = Conexion::queryUnique($this->bdName, $query, $error);

        $this->documentos = (object) [ 
            'uploadDocumentos' => json_decode($resultado['uploadDocumentos']),
            'usuarioUploadDocumento' => $resultado['usuarioUploadDocumento'],
            'perfilesUploadDocumento' => json_decode($resultado['perfilesUploadDocumento'])
        ];
    }

    public function crear($datos)
    {
        return false;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Configuración
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Modificar el contenido de los checkboxes
        $datos["puertoSSL"] = ( isset($datos["puertoSSL"]) && mb_strtolower($datos["puertoSSL"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();
        $arrayPDOParam["servidor"] = self::$type["servidor"];
        $arrayPDOParam["puerto"] = self::$type["puerto"];
        $arrayPDOParam["puertoSSL"] = self::$type["puertoSSL"];
        $arrayPDOParam["usuario"] = self::$type["usuario"];
        if ( $datos["contrasena"] != "" ) {
            $datos["contrasena"] = base64_encode($datos["contrasena"]);
            
            $arrayPDOParam["contrasena"] = self::$type["contrasena"];
        } 
        $arrayPDOParam["visualizacionCorreo"] = self::$type["visualizacionCorreo"];
        $arrayPDOParam["visualizacionNombre"] = self::$type["visualizacionNombre"];
        $arrayPDOParam["respuestaCorreo"] = self::$type["respuestaCorreo"];
        $arrayPDOParam["respuestaNombre"] = self::$type["respuestaNombre"];
        $arrayPDOParam["comprobacionCorreo"] = self::$type["comprobacionCorreo"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function actualizarAvisos($datos)
    {
        // Agregar al request para actualizar el registro
        $datos["configuracionCorreoId"] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Configuración
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["perfilesCrear"] = "string";
        $arrayPDOParam["estatusModificarUsuarioCreacion"] = "string";
        $arrayPDOParam["estatusModificarPerfiles"] = "string";
        $arrayPDOParam["uploadDocumentos"] = "string";
        $arrayPDOParam["usuarioUploadDocumento"] = "integer";
        $arrayPDOParam["perfilesUploadDocumento"] = "string";
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam["configuracionCorreoId"] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE configuracion_correo_requisiciones SET " . $campos . " WHERE configuracionCorreoId = :configuracionCorreoId", $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function eliminar()
    {
        return false;
    }
}
