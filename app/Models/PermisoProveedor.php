<?php

namespace App\Models;

use App\Conexion;
use PDO;

class PermisoProveedor
{
    static protected $fillable = [
        'id', 'titulo', 'descripcion', 'proveedorId'
    ];

    static protected $type = [
        'id' => 'integer',
        'titulo' => 'string',
        'proveedorId' => 'integer',
        'estatus' => 'integer',
        'usuarioIdActualizacion' => 'integer',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "proveedor_permisos";

    protected $keyName = "id";

    public $id = null;
    public $titulo;
    public $nombre;
    public $descripcion;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

                return Conexion::queryAll($this->bdName, "SELECT FROM $this->tableName ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->titulo = $respuesta["titulo"];
                $this->estatus = $respuesta["estatus"];
            }

            return $respuesta;

        }

    }

    public function consultarPermisos($idProveedor) {

        $respuesta = Conexion::queryAll($this->bdName, "SELECT *, 
            CASE 
            WHEN estatus = 1 THEN 'Por revisar' 
            WHEN estatus = 2 THEN 'Aprobado' 
            WHEN estatus = 3 THEN 'Rechazado' 
            ELSE 'Desconocido' 
            END AS estatus 
            FROM $this->tableName 
            WHERE proveedorId = $idProveedor", 
            $error
        );

        return $respuesta;

    }

    public function crear($datos, $archivos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["titulo"] = self::$type["titulo"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el permiso
            $this->id = $lastId;

            $respuesta = $this->insertarArchivos($archivos);

        }

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminarArchivos($idArchivo) {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $idArchivo;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM permiso_proveedor_archivos WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function verArchivos() {

        $respuesta = Conexion::queryAll($this->bdName, "SELECT * FROM permiso_proveedor_archivos WHERE permisoId = $this->id", $error);

        return $respuesta;

    }

    function insertarArchivos($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                $directorio = "../../vistas/uploaded-files/datos-fiscales/permisos/";//Esta sobrando los ../../ ya que como se usa esta funcion en ajax, hay problemas con las rutas
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';
                if (!is_dir($directorio)) {
                    // Crear el directorio si no existe
                    mkdir($directorio, 0777, true);
                }
                
                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["permisoId"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = substr($ruta,6);

            $arrayPDOParam = array();        
            $arrayPDOParam["permisoId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO permiso_proveedor_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);//Estoy haciendo un substring por que como se usa esta funcion en ajax, hay problemas con las rutas
            }

        }

        return $respuesta;

    }

    public function autorizar(){
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["estatus"] = 2;
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatus"] = self::$type["estatus"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET estatus = :estatus, usuarioIdActualizacion = :usuarioIdActualizacion WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function rechazar(){
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["estatus"] = 3;
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatus"] = self::$type["estatus"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET estatus = :estatus, usuarioIdActualizacion = :usuarioIdActualizacion WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}
