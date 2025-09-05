<?php

namespace App\Models;

if ( file_exists ( "app/Policies/UsuarioPolicy.php" ) ) {
    require_once "app/Policies/UsuarioPolicy.php";
} else {
    require_once "../Policies/UsuarioPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\UsuarioPolicy;

class Usuario extends UsuarioPolicy
{
    static protected $fillable = [
        'usuario', 'activo', 'contrasena', 'cambiarContrasena', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'correo', 'foto', 'fotoAnterior', 'firma', 'firmaAnterior', 'empresaId', 'perfiles','ubicacionId', 'obraId', 'sello', 'prefijo_usuario'
    ];

    static protected $type = [
        'id' => 'integer',
        'usuario' => 'string',
        'activo' => 'integer',
        'contrasena' => 'string',
        'cambiarContrasena' => 'integer',
        'nombre' => 'string',
        'apellidoPaterno' => 'string',
        'apellidoMaterno' => 'string',
        'correo' => 'string',
        'foto' => 'string',
        'firma' => 'string',
        'empresaId' => 'integer',
        'ubicacionId' => 'integer',
        'obraId' => 'integer',
        'ultimoIngreso' => 'date',
        'sello' => 'string',
        'prefijo_usuario' => 'string',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "usuarios";    

    protected $keyName = "id";

    public $id = null;
    public $usuario;
    public $activo;
    public $contrasena;
    public $cambiarContrasena;
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $correo;
    public $foto;
    public $firma;
    public $empresaId;
    public $ultimoIngreso;
    public $nombreCompleto;
    public $obraId;
    public $perfiles = array();
    public $permisos = array();

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR USUARIOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            // return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName ORDER BY usuario", $error);

            return Conexion::queryAll($this->bdName, "SELECT U.*, E.nombreCorto AS 'empresas.nombreCorto', CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'nombreCompleto' FROM $this->tableName U LEFT JOIN ".CONST_BD_SECURITY.".empresas E ON U.empresaId = E.id ORDER BY U.usuario", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT U.*, E.nombreCorto AS 'empresas.nombreCorto' FROM $this->tableName U LEFT JOIN ".CONST_BD_SECURITY.".empresas E ON U.empresaId = E.id WHERE U.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT U.* FROM $this->tableName U WHERE U.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->usuario = $respuesta["usuario"];
                $this->activo = $respuesta["activo"];
                $this->contrasena = $respuesta["contrasena"];
                $this->cambiarContrasena = $respuesta["cambiarContrasena"];
                $this->nombre = $respuesta["nombre"];
                $this->apellidoPaterno = $respuesta["apellidoPaterno"];
                $this->apellidoMaterno = $respuesta["apellidoMaterno"];
                $this->correo = $respuesta["correo"];
                $this->foto = $respuesta["foto"];
                $this->firma = $respuesta["firma"];
                $this->empresaId = $respuesta["empresaId"];
                $this->ultimoIngreso = $respuesta["ultimoIngreso"];
                $this->nombreCompleto = fNombreCompleto($respuesta["nombre"], $respuesta["apellidoPaterno"], $respuesta["apellidoMaterno"]);
                $this->fechaCreacion = $respuesta["fechaCreacion"];
                $this->ubicacionId = $respuesta["ubicacionId"];
                $this->obraId = $respuesta["obraId"];
                $this->sello = $respuesta["sello"];
                $this->puesto = $respuesta["puesto"];
                $this->prefijo_usuario = $respuesta["prefijo_usuario"];

            }

            return $respuesta;

        }

    }

    public function consultarPerfiles($item = null, $valor = null) {

        $resultado = Conexion::queryAll($this->bdName, "SELECT P.* FROM usuario_perfiles UP INNER JOIN perfiles P ON P.id = UP.perfilId WHERE UP.usuarioId = $this->id", $error);
        
        $this->perfiles = array_column($resultado, "nombre");
        $this->perfilesId = array_column($resultado, "id");

    }

    public function consultarPermisos($item = null, $valor = null) {

        $tipo = "ver";

        $query = "SELECT P.*, UP.". $tipo ." FROM usuario_permisos UP INNER JOIN permisos P ON UP.permisoId = P.id WHERE UP.usuarioId = $this->id AND UP.". $tipo." = 1 UNION ALL SELECT P.*, PP.". $tipo ." FROM usuario_perfiles UP INNER JOIN perfil_permisos PP ON UP.perfilId = PP.perfilId INNER JOIN permisos P ON PP.permisoId = P.id WHERE UP.usuarioId = $this->id AND PP.". $tipo." = 1";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);
        
        $this->permisos = array_column($resultado, "nombre");

    }

    public function consultarCompradores(){
        return Conexion::queryAll($this->bdName,"SELECT U.correo, U.id, CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS nombreCompleto FROM $this->tableName U INNER JOIN usuario_perfiles UP ON UP.usuarioId = U.id WHERE UP.perfilId = 5 GROUP BY
    U.id;",$error);
    }

    public function checkAdmin() {

        return in_arrayi(CONST_ADMIN, $this->perfiles);
        
    }

    public function checkPermiso(string $permiso) {

        return in_arrayi($permiso, $this->permisos);

    }

    public function crear($datos) {


        // Encriptar la contraseña
        $datos["contrasena"] = hash('sha256', $datos["contrasena"]);

        // Modificar el contenido de los checkboxes
        // $datos["activo"] = ( isset($datos["activo"]) && mb_strtolower($datos["activo"]) == "on" ) ? "1" : "0";

        // Agregar al request la ruta final de la foto
        $ruta = "";
        if ( $datos["foto"]["tmp_name"] != "" ) {

            $tmpName = $datos["foto"]["tmp_name"];
            $tipo = $datos["foto"]["type"];

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $directorio = "vistas/img/usuarios/";

            do {
                $ruta = fRandomNameImageFile($directorio, $tipo);
            } while ( file_exists($ruta) );

        }
        // Request con el nombre del archivo
        $datos["foto"] = $ruta;

        // Agregar al request la ruta final de la firma
        $rutaFirma = "";
        if ( $datos["firma"]["tmp_name"] != "" ) {

            $tmpNameFirma = $datos["firma"]["tmp_name"];
            $tipoFirma = $datos["firma"]["type"];

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA FIRMA
            $directorio = "vistas/img/usuarios/firmas/";

            do {
                $rutaFirma = fRandomNameImageFile($directorio, $tipoFirma);
            } while ( file_exists($rutaFirma) );

        }
        // Request con el nombre del archivo (firma)
        $datos["firma"] = $rutaFirma;

        $arrayPDOParam = array();
        $arrayPDOParam["usuario"] = self::$type["usuario"];
        // $arrayPDOParam["activo"] = self::$type["activo"];
        $arrayPDOParam["contrasena"] = self::$type["contrasena"];
        // $arrayPDOParam["cambiarContrasena"] = self::$type["cambiarContrasena"];
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
        $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["foto"] = self::$type["foto"];
        $arrayPDOParam["firma"] = self::$type["firma"];
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["ubicacionId"] = self::$type["ubicacionId"];
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["sello"] = self::$type["sello"];
        $arrayPDOParam["prefijo_usuario"] = self::$type["prefijo_usuario"];

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (usuario, contrasena, nombre, apellidoPaterno, apellidoMaterno, correo, foto, firma, empresaId,ubicacionId, obraId, sello, prefijo_usuario) VALUES (:usuario, :contrasena, :nombre, :apellidoPaterno, :apellidoMaterno, :correo, :foto, :firma, :empresaId,:ubicacionId, :obraId, :sello, :prefijo_usuario)", $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            if ( $ruta != "" ) {
                fSaveImageFile($tmpName, $tipo, $datos["foto"]);
            }

            if ( $rutaFirma != "" ) {
                fSaveImageFile($tmpNameFirma, $tipoFirma, $datos["firma"]);
            }

            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;

            $arrayPerfiles = isset($datos["perfiles"]) ? $datos["perfiles"] : null;

            if ( $arrayPerfiles ) $respuesta = $this->actualizarPerfiles($arrayPerfiles);

        }

        return $respuesta;

    }

    public function actualizar($datos) {
        
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Modificar el contenido de los checkboxes
        $datos["activo"] = ( isset($datos["activo"]) && mb_strtolower($datos["activo"]) == "on" ) ? "1" : "0";
        
        // Agregar al request la ruta final de la foto
        $tmpName = $datos["foto"]["tmp_name"];
        $tipo = $datos["foto"]["type"];

        if ( $datos["foto"]["tmp_name"] == "" ) {

            // Si no viene una nueva imágen dejamos la anterior
            $datos["foto"] = $datos["fotoAnterior"];

        } else {

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $ruta = "";
            $directorio = "vistas/img/usuarios/";

            do {
                $ruta = fRandomNameImageFile($directorio, $tipo);
            } while ( file_exists($ruta) );

            // Si viene una nueva imágen renombramos el Request con el nombre del archivo
            $datos["foto"] = $ruta;

        }

        // Agregar al request la ruta final de la firma
        $tmpNameFirma = $datos["firma"]["tmp_name"];
        $tipoFirma = $datos["firma"]["type"];

        if ( $datos["firma"]["tmp_name"] == "" ) {

            // Si no viene una nueva firma dejamos la anterior
            $datos["firma"] = $datos["firmaAnterior"];

        } else {

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA FIRMA
            $rutaFirma = "";
            $directorio = "vistas/img/usuarios/firmas/";

            do {
                $rutaFirma = fRandomNameImageFile($directorio, $tipoFirma);
            } while ( file_exists($rutaFirma) );

            // Si viene una nueva imágen renombramos el Request con el nombre del archivo
            $datos["firma"] = $rutaFirma;

        }

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["activo"] = self::$type["activo"];

        if ( $datos["contrasena"] != "") {

            $datos["contrasena"] = hash('sha256', $datos["contrasena"]);

            $arrayPDOParam["contrasena"] = self::$type["contrasena"];

        }
        
        // $arrayPDOParam["cambiarContrasena"] = self::$type["cambiarContrasena"];
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
        $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["foto"] = self::$type["foto"];
        $arrayPDOParam["firma"] = self::$type["firma"];
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["ubicacionId"] = self::$type["ubicacionId"];
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["sello"] = self::$type["sello"];
        $arrayPDOParam["prefijo_usuario"] = self::$type["prefijo_usuario"];

        if ( $datos["contrasena"] != "") {

            $respuesta = Conexion::queryExecute(
            $this->bdName,
            "UPDATE $this->tableName SET contrasena = :contrasena, nombre = :nombre, apellidoPaterno = :apellidoPaterno, apellidoMaterno = :apellidoMaterno, correo = :correo, foto = :foto, firma = :firma, empresaId = :empresaId, ubicacionId = :ubicacionId, obraId = :obraId, sello = :sello, activo = :activo, prefijo_usuario = :prefijo_usuario WHERE id = :id",
            $datos,
            $arrayPDOParam,
            $error
            );

        } else {

            $respuesta = Conexion::queryExecute(
            $this->bdName,
            "UPDATE $this->tableName SET nombre = :nombre, apellidoPaterno = :apellidoPaterno, apellidoMaterno = :apellidoMaterno, correo = :correo, foto = :foto, firma = :firma, empresaId = :empresaId, ubicacionId = :ubicacionId, obraId = :obraId, sello = :sello, activo = :activo, prefijo_usuario = :prefijo_usuario WHERE id = :id",
            $datos,
            $arrayPDOParam,
            $error
            );

        }

        if ( $respuesta ) {

            // Si viene una imagen en el POST foto actualizarla físicamente
            if ( $tmpName != "" ) {
                fSaveImageFile($tmpName, $tipo, $datos["foto"], $datos["fotoAnterior"]);
            }

            // Si viene una imagen en el POST firma actualizarla físicamente
            if ( $tmpNameFirma != "" ) {
                fSaveImageFile($tmpNameFirma, $tipoFirma, $datos["firma"], $datos["firmaAnterior"]);
            }

            $arrayPerfiles = isset($datos["perfiles"]) ? $datos["perfiles"] : null;

            if ( $this->eliminarPerfiles() ) {

                if ( $arrayPerfiles ) $respuesta = $this->actualizarPerfiles($arrayPerfiles);
            
            }

        }

        return $respuesta;

    }

    public function actualizarPerfil($datos) {
        
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request la ruta final de la foto
        $tmpName = $datos["foto"]["tmp_name"];
        $tipo = $datos["foto"]["type"];

        if ( $datos["foto"]["tmp_name"] == "" ) {

            // Si no viene una nueva imágen dejamos la anterior
            $datos["foto"] = $datos["fotoAnterior"];

        } else {

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $ruta = "";
            $directorio = "vistas/img/usuarios/";

            do {
                $ruta = fRandomNameImageFile($directorio, $tipo);
            } while ( file_exists($ruta) );

            // Si viene una nueva imágen renombramos el Request con el nombre del archivo
            $datos["foto"] = $ruta;

        }

        // Agregar al request la ruta final de la firma
        $tmpNameFirma = $datos["firma"]["tmp_name"];
        $tipoFirma = $datos["firma"]["type"];

        if ( $datos["firma"]["tmp_name"] == "" ) {

            // Si no viene una nueva firma dejamos la anterior
            $datos["firma"] = $datos["firmaAnterior"];

        } else {

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA FIRMA
            $rutaFirma = "";
            $directorio = "vistas/img/usuarios/firmas/";

            do {
                $rutaFirma = fRandomNameImageFile($directorio, $tipoFirma);
            } while ( file_exists($rutaFirma) );

            // Si viene una nueva firma renombramos el Request con el nombre del archivo
            $datos["firma"] = $rutaFirma;

        }
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        if ( $datos["contrasena"] != "") {

            $datos["contrasena"] = hash('sha256', $datos["contrasena"]);

            $arrayPDOParam["contrasena"] = self::$type["contrasena"];

        }
        $arrayPDOParam["nombre"] = self::$type["nombre"];
        $arrayPDOParam["apellidoPaterno"] = self::$type["apellidoPaterno"];
        $arrayPDOParam["apellidoMaterno"] = self::$type["apellidoMaterno"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["foto"] = self::$type["foto"];
        $arrayPDOParam["firma"] = self::$type["firma"];

        if ( $datos["contrasena"] != "") {

            $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET contrasena = :contrasena, nombre = :nombre, apellidoPaterno = :apellidoPaterno, apellidoMaterno = :apellidoMaterno, correo = :correo, foto = :foto, firma = :firma WHERE id = :id", $datos, $arrayPDOParam, $error);

        } else {

            $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET nombre = :nombre, apellidoPaterno = :apellidoPaterno, apellidoMaterno = :apellidoMaterno, correo = :correo, foto = :foto, firma = :firma WHERE id = :id", $datos, $arrayPDOParam, $error);

        }

        if ( $respuesta ) {

            // Si viene una imagen en el POST foto actualizarla físicamente
            if ( $tmpName != "" ) fSaveImageFile($tmpName, $tipo, $datos["foto"], $datos["fotoAnterior"]);

            // Si viene una imagen en el POST firma actualizarla físicamente
            if ( $tmpNameFirma != "" ) fSaveImageFile($tmpNameFirma, $tipoFirma, $datos["firma"], $datos["firmaAnterior"]);

        }

        return $respuesta;

    }

    public function actualizarIngreso() {

        // Agregar al request para actualizar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET ultimoIngreso = NOW() WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    function eliminarPerfiles() {

        $eliminar = array();
        $eliminar["usuarioId"] = $this->id;
        
        $eliminarPDOParam = array();
        $eliminarPDOParam["usuarioId"] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM usuario_perfiles WHERE usuarioId = :usuarioId", $eliminar, $eliminarPDOParam, $error);

    }

    function actualizarPerfiles(array $perfiles = null) {

        $respuesta = false;
    
        if ( $perfiles ) {

            foreach ($perfiles as $perfil) {

                $insertar = array();
                $insertar["usuarioId"] = $this->id;
                $insertar["perfil"] = $perfil;

                $insertarPDOParam = array();
                $insertarPDOParam["usuarioId"] = self::$type[$this->keyName];
                $insertarPDOParam["perfil"] = "string";

                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO usuario_perfiles (usuarioId, perfilId) VALUES (:usuarioId, (SELECT id FROM perfiles WHERE nombre = :perfil))", $insertar, $insertarPDOParam, $error);

            }
            
        }

        return $respuesta;

    }

    public function eliminar() {

        if ( $this->eliminarPerfiles() ) {

            // Agregar al request para eliminar el registro
            $datos = array();
            $datos[$this->keyName] = $this->id;
            
            $arrayPDOParam = array();
            $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

            $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

            if ( $respuesta && !is_null($this->foto) ) {

                // Eliminar físicamente la foto (si tiene)
                fDeleteFile($this->foto);

            }

            return $respuesta;

        } else {

            return false;

        }

    }

    // public function notificaciones()
    // {
    //     $query = "SELECT    S.id, S.folio, S.fechaActualizacion, SE.descripcion AS 'servicio_estatus.descripcion',
    //                         TIMESTAMPDIFF(SECOND, S.fechaActualizacion, NOW()) AS 'tiempoEnSegundos'
    //             FROM        servicios S
    //             INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
    //             WHERE       S.servicioEstatusId = 8
    //             ORDER BY    S.fechaActualizacion DESC";

    //     $servicios = Conexion::queryAll(CONST_BD_APP, $query, $error);

    //     $registros = array();
    //     foreach ($servicios as $key => $value) {
    //         $rutaEdit = \App\Route::names('servicios.edit', $value['id']);
    //         $folio = mb_strtoupper(fString($value['folio']));
    //         $estatusDescripcion = mb_strtoupper(fString($value['servicio_estatus.descripcion']));
    //         $tiempo = formatearTiempoUnidad($value['tiempoEnSegundos']);

    //         array_push( $registros, [
    //             "ruta" => $rutaEdit,
    //             "icono" => "fa-tools",
    //             "asunto" => "{$folio} - {$estatusDescripcion}",
    //             "tiempo" => $tiempo ] );
    //     }
    //     unset($servicios);

    //     return $registros;
    // }
}
