<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ObraPolicy.php" ) ) {
    require_once "app/Policies/ObraPolicy.php";
    require_once "app/Models/Usuario.php";

} else {
    require_once "../Policies/ObraPolicy.php";
    require_once "../Models/Usuario.php";

}

use App\Conexion;
use PDO;
use App\Policies\ObraPolicy;
use App\Models\Usuario;


class Obra extends ObraPolicy
{
    static protected $fillable = [
        'empresaId', 'descripcion', 'nombreCorto', 'estatusId', 'periodos', 'fechaInicio', 'fechaFinalizacion', 'prefijo','semanaExtra','id','usuariosCompras', 'almacen'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresaId' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'estatusId' => 'integer',
        'periodos' => 'integer',
        'fechaInicio' => 'date',
        'fechaFinalizacion' => 'date',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'prefijo' => 'string',
        'semanaExtra' => 'integer',
        'usuariosCompras' => 'string',
        'almacen' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "obras";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $descripcion;
    public $nombreCorto;
    public $estatusId;
    public $periodos;
    public $fechaInicio;
    public $fechaFinalizacion;
    public $usuarioIdCreacion;
    public $usuarioIdActualizacion;


    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR OBRAS
    =============================================*/
    public function consultar($item = null, $valor = null)
    {

        // $ubicacionId = ubicacionUsuario();

        if ( is_null($valor) ) {
            return Conexion::queryAll($this->bdName, "SELECT 
                                                        O.*, 
                                                        E.nombreCorto AS 'empresas.nombreCorto', 
                                                        ES.descripcion AS 'estatus.descripcion', 
                                                        ES.colorTexto AS 'estatus.colorTexto', 
                                                        ES.colorFondo AS 'estatus.colorFondo', 
                                                        US.nombre AS 'usuarios.nombre', 
                                                        US.apellidoPaterno AS 'usuarios.apellidoPaterno', 
                                                        US.apellidoMaterno AS 'usuarios.apellidoMaterno' 
                                                    FROM $this->tableName O 
                                                    INNER JOIN empresas E ON O.empresaId = E.id 
                                                    INNER JOIN estatus ES ON O.estatusId = ES.id 
                                                    INNER JOIN usuarios US ON O.usuarioIdCreacion = US.id
                                                    ORDER BY E.id, O.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                        O.*,
                                                                        A.nombreCorto AS nombreAlmacen
                                                                    FROM 
                                                                        $this->tableName O
                                                                    LEFT JOIN almacenes A ON O.almacen = A.id
                                                                    WHERE 
                                                                        O.$this->keyName = $valor"
                                                                    , $error);
            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                        O.*,
                                                                        A.nombreCorto AS nombreAlmacen
                                                                    FROM 
                                                                        $this->tableName O
                                                                    LEFT JOIN almacenes A ON O.almacen = A.id
                                                                    WHERE 
                                                                        O.$item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresaId = $respuesta["empresaId"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->estatusId = $respuesta["estatusId"];
                $this->periodos = $respuesta["periodos"];
                $this->fechaInicio = $respuesta["fechaInicio"];
                $this->fechaFinalizacion = $respuesta["fechaFinalizacion"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->semanaExtra = $respuesta["semanaExtra"];
                $this->prefijo = $respuesta["prefijo"];
                $this->almacen = $respuesta["almacen"];
                $this->nombreAlmacen = $respuesta["nombreAlmacen"];

                // $this->ubicacionId = $respuesta["ubicacionId"];
                $this->usuariosCompras = json_decode($respuesta["usuariosCompras"]);

                if ( file_exists ( "app/Models/Estatus.php" ) ) {
                    require_once "app/Models/Estatus.php";
                } else {
                    require_once "../Models/Estatus.php";
                }
                $estatus = New Estatus;
                $this->estatus = $estatus->consultar(null, $this->estatusId);
            }

            return $respuesta;

        }
    }

    public function consultarObraActivas(){
        return Conexion::queryAll($this->bdName, "SELECT 
                                                    O.*, 
                                                    E.nombreCorto AS 'empresas.nombreCorto', 
                                                    ES.descripcion AS 'estatus.descripcion', 
                                                    ES.colorTexto AS 'estatus.colorTexto', 
                                                    ES.colorFondo AS 'estatus.colorFondo', 
                                                    US.nombre AS 'usuarios.nombre', 
                                                    US.apellidoPaterno AS 'usuarios.apellidoPaterno', 
                                                    US.apellidoMaterno AS 'usuarios.apellidoMaterno' 
                                                FROM $this->tableName O 
                                                INNER JOIN empresas E ON O.empresaId = E.id 
                                                INNER JOIN estatus ES ON O.estatusId = ES.id 
                                                INNER JOIN usuarios US ON O.usuarioIdCreacion = US.id
                                                WHERE O.estatusId = 1
                                                ORDER BY E.id, O.descripcion", $error);
    }

    public function crear($datos)
    {
        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fechaInicio"] = fFechaSQL($datos["fechaInicio"]);
        if (!isset($datos["usuariosCompras"])) {
            $datos["usuariosCompras"] = "[]";
        }else{
            $datos["usuariosCompras"] = json_encode($datos["usuariosCompras"]);
        }
        $arrayPDOParam = array();        
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["periodos"] = self::$type["periodos"];
        $arrayPDOParam["fechaInicio"] = self::$type["fechaInicio"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["prefijo"] = self::$type["prefijo"];
        $arrayPDOParam["usuariosCompras"] = self::$type["usuariosCompras"];
        $arrayPDOParam["almacen"] = self::$type["almacen"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) $this->id = $lastId;

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fechaInicio"] = fFechaSQL($datos["fechaInicio"]);
        if ( $datos["fechaFinalizacion"] != '' ) $datos["fechaFinalizacion"] = fFechaSQL($datos["fechaFinalizacion"]);
        if (!isset($datos["usuariosCompras"])) {
            $datos["usuariosCompras"] = "[]";
        }else{
            $datos["usuariosCompras"] = json_encode($datos["usuariosCompras"]);
        }
        $arrayPDOParam = array();
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["periodos"] = self::$type["periodos"];
        $arrayPDOParam["fechaInicio"] = self::$type["fechaInicio"];
        // if ( $datos["fechaFinalizacion"] != '' ) $arrayPDOParam["fechaFinalizacion"] = self::$type["fechaFinalizacion"];
        $arrayPDOParam["fechaFinalizacion"] = self::$type["fechaFinalizacion"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["usuariosCompras"] = self::$type["usuariosCompras"];
        $arrayPDOParam["prefijo"] = self::$type["prefijo"];
        $arrayPDOParam["almacen"] = self::$type["almacen"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function eliminar()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function actualizarSemana($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["semanaExtra"] = self::$type["semanaExtra"];
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function consultarLotes()
    {
        return Conexion::queryAll($this->bdName, "SELECT id, descripcion FROM presupuestos_obra WHERE obraId = $this->id", $error);
    }

    public function crearPresupuesto($datos)
    {
        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["obraId"] = self::$type["id"];
        $arrayPDOParam["descripcion"] = "string";
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO presupuestos_obra " . $campos, $datos, $arrayPDOParam, $error);
    }

    public function crearAnuncio($datos)
    {
        // Agregar al request para especificar el usuario que creó el Anuncio
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["obraId"] = self::$type["id"];
        $arrayPDOParam["mensaje"] = "string";
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["fechaHora"] = "date";

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO anuncios_obra " . $campos, $datos, $arrayPDOParam, $error);
    }

    public function consultarAnuncios()
    {
        return Conexion::queryAll($this->bdName, "SELECT anuncios_obra.mensaje, anuncios_obra.fechaHora, Concat(usuarios.nombre, ' ', usuarios.apellidoPaterno) AS usuarioNombre FROM anuncios_obra inner join usuarios on anuncios_obra.usuarioIdCreacion = usuarios.id WHERE anuncios_obra.obraId = $this->id order by anuncios_obra.fechaHora desc", $error);
    }
}
