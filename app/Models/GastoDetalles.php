<?php

namespace App\Models;

if ( file_exists ( "app/Policies/GastosPolicy.php" ) ) {
    require_once "app/Policies/GastosPolicy.php";
} else {
    require_once "../Policies/GastosPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\GastosPolicy;

class GastoDetalles extends GastosPolicy
{
    static protected $fillable = [
        'fecha', 'tipoGasto', 'costo', 'obra', 'obraDetalle', 'observaciones', 'gastoId', 'economico', 'proveedor', 'factura', 'cantidad', 'archivos','solicito', 'id'
    ];

    static protected $type = [
        'id' => 'integer',
        'fecha' => 'date',
        'tipoGasto' => 'integer',
        'costo' => 'float',
        'obra' => 'integer',
        'obraDetalle' => 'integer',
        'observaciones' => 'string',
        'gastoId' => 'integer',
        'economico' => 'string',
        'proveedor' => 'string',
        'solicito' => 'string',
        'factura' => 'string',
        'cantidad' => 'integer',
        'usuarioIdCreacion' => 'integer',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "gasto_detalles";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT G.*,  CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto, G.fecha_inicio
                                                    FROM  $this->tableName G
                                                    INNER JOIN empleados E ON E.id = G.encargado", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->fecha = fFechaLarga($respuesta["fecha"]);
                $this->tipoGasto = $respuesta["tipoGasto"];
                $this->costo = $respuesta["costo"];
                $this->obraDetalle = $respuesta["obraDetalle"];
                $this->observaciones = $respuesta["observaciones"];
                $this->gastoId = $respuesta["gastoId"];
                $this->economico = $respuesta["economico"];
                $this->proveedor = $respuesta["proveedor"];
                $this->factura = $respuesta["factura"];
                $this->cantidad = $respuesta["cantidad"];
                $this->solicito = $respuesta["solicito"];
                
            }

            return $respuesta;

        }
    }

    public function consultarPorGasto($id)
    {
        return Conexion::queryAll($this->bdName,
        "SELECT GD.*,GD.id, GD.fecha, GT.descripcion AS tipoGasto,
            GD.costo, O.nombreCorto AS obra,GD.factura, GD.proveedor, GD.economico, COALESCE(D.descripcion, I.descripcion) AS descripcion, GD.observaciones, COALESCE(D.unidadId, I.unidadId) AS unidadId FROM 
            $this->tableName GD
            INNER JOIN obras O ON O.id = GD.obra
            INNER JOIN obra_detalles OD ON OD.id = GD.obraDetalle
            INNER JOIN gastos_tipos GT ON GT.id = GD.tipoGasto
            LEFT JOIN insumos D ON D.id = OD.insumoId
            LEFT JOIN indirectos I ON I.id = OD.indirectoId
            WHERE GD.gastoId = $id");
    }

    public function consultarArchivos($id){
        $query = "SELECT    SA.*
                FROM        gasto_archivos SA
                WHERE       SA.gastoDetalleId = {$id}
                ORDER BY    SA.id";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    public function crear($datos) {

        // Agregar al request para especificar los segmentos
        $datos["fecha"] = fFechaSQL($datos["fecha"]);
        $datos["costo"] = floatval(str_replace(",", "",$datos["costo"]));
        $datos["cantidad"] = floatval(str_replace(",", "",$datos["cantidad"]));

        $arrayPDOParam = array();        
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["tipoGasto"] = self::$type["tipoGasto"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["solicito"] = self::$type["solicito"];
        $arrayPDOParam["obraDetalle"] = self::$type["obraDetalle"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["gastoId"] = self::$type["gastoId"];
        $arrayPDOParam["economico"] = self::$type["economico"];
        if(isset($datos["proveedor"])) $arrayPDOParam["proveedor"] = self::$type["proveedor"];
        if(isset($datos["factura"])) $arrayPDOParam["factura"] = self::$type["factura"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId= 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error,$lastId);

        if ($respuesta &&  isset($datos['archivos'])) {
            $this->id = $lastId;
            $this->tipo = 1;
            $respuesta = $this->insertarArchivos($datos['archivos']);
        }

        return $respuesta;
    }

    public function actualizar($datos) {

        // Agregar al request para especificar los segmentos
        $datos["fecha"] = fFechaSQL($datos["fecha"]);
        $datos["costo"] = floatval(str_replace(",", "",$datos["costo"]));
        $datos["cantidad"] = floatval(str_replace(",", "",$datos["cantidad"]));

        $arrayPDOParam = array();        
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["tipoGasto"] = self::$type["tipoGasto"];
        $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["solicito"] = self::$type["solicito"];
        $arrayPDOParam["obraDetalle"] = self::$type["obraDetalle"];
        $arrayPDOParam["economico"] = self::$type["economico"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        if(isset($datos["proveedor"])) $arrayPDOParam["proveedor"] = self::$type["proveedor"];
        if(isset($datos["factura"])) $arrayPDOParam["factura"] = self::$type["factura"];
        
        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam["id"] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    function insertarArchivos($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                $directorio = "../../vistas/uploaded-files/gastos/";//Esta sobrando los ../../ ya que como se usa esta funcion en ajax, hay problemas con las rutas
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';
                if (!is_dir($directorio)) {
                    // Crear el directorio si no existe
                    mkdir($directorio, 0777, true);
                }
                
                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";
                elseif ( $archivos["type"][$i] == "text/xml" ) $extension = ".xml";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["gastoDetalleId"] = $this->id;
            $insertar["tipo"] = $this->tipo;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = substr($ruta,6);
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["gastoDetalleId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = 'integer';
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO gasto_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);//Estoy haciendo un substring por que como se usa esta funcion en ajax, hay problemas con las rutas
            }

        }

        return $respuesta;

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
}