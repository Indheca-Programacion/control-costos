<?php

namespace App\Models;

if ( file_exists ( "app/Policies/InventarioPolicy.php" ) ) {
    require_once "app/Policies/InventarioPolicy.php";
} else {
    require_once "../Policies/InventarioPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\InventarioPolicy;

class Inventario extends InventarioPolicy
{
    static protected $fillable = [
        'ordenCompra', 'almacen', 'observaciones', 'detalles', 'firma', 'entrega', 'requisicionId', 'fechaEntrega','numeroContrato'
    ];

    static protected $type = [
        'id' => 'integer',        
        'ordenCompra' => 'string',
        'almacen' => 'integer',
        'folio' => 'integer',
        'firma' => 'string',
        'observaciones' => 'string',
        'entrega' => 'string',
        'usuarioIdCreacion' => 'integer',
        'requisicionId' => 'integer',
	'fechaCreacion' => 'date',
	'numeroContrato' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "inventarios";

    protected $keyName = "id";

    public $id = null;
    public $observaciones = null;
    public $requisicionId;
    public $numeroContrato;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR INVENTARIOS CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $id = usuarioAutenticado()["id"];
        $query = "SELECT IV.*,
                    CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'nombreCompleto', A.nombre as 'almacen.descripcion'
                    FROM $this->tableName  IV
                    INNER JOIN usuarios U ON U.id = IV.usuarioIdCreacion
                    INNER JOIN almacenes A ON A.id = IV.almacen
                    WHERE A.usuarioIdCreacion = $id";

        foreach ($arrayFiltros as $key => $value) {
            $query .= " AND";
            $query .= " {$value['campo']} = {$value['valor']}";
        }

        $query .= " ORDER BY IV.fechaCreacion desc";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR INVENTARIOS
    =============================================*/
    public function consultar($item = null, $valor = null,$permiso=false)
    {
        if ( is_null($valor) ) {
            $id = usuarioAutenticado()["id"];
            $query = "SELECT IV.*,
                    CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'nombreCompleto', A.nombre as 'almacen.descripcion'
                    FROM $this->tableName  IV
                    INNER JOIN usuarios U ON U.id = IV.usuarioIdCreacion
                    INNER JOIN almacenes A ON A.id = IV.almacen";
            if(!$permiso){
                $query .= " WHERE A.usuarioIdCreacion = $id";
            }
            $query .= " ORDER BY IV.folio desc";
            return Conexion::queryAll($this->bdName, $query, $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {

                $this->id = $respuesta["id"];
                $this->observaciones = $respuesta["observaciones"];
                $this->ordenCompra = $respuesta["ordenCompra"];
                $this->entrega = $respuesta["entrega"];
                $this->almacen = $respuesta["almacen"];
                $this->firma = $respuesta["firma"];                
                $this->folio = $respuesta["folio"];                
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];                
                $this->requisicionId = $respuesta["requisicionId"];                
                $this->fechaCreacion = fFechaLarga($respuesta["fechaCreacion"]);         
                $this->fechaActualizacion = fFechaLarga($respuesta["fechaActualizacion"]);            
                $this->numeroContrato = $respuesta["numeroContrato"];            
            }

            return $respuesta;

        }
    }
    
    public function consultarLastFolio(){
        return Conexion::queryUnique($this->bdName,"SELECT folio 
                                                    FROM $this->tableName
                                                    WHERE folio = (SELECT MAX(folio) FROM $this->tableName)");
    }

    public function consultarDisponibles()
    {
        return Conexion::queryAll($this->bdName, "SELECT IV.*,
        CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'nombreCompleto', A.nombre as 'almacen.descripcion', UN.descripcion as 'unidad.descripcion', UN.id as 'unidad.id'
        FROM $this->tableName  IV
        INNER JOIN usuarios U ON U.id = IV.usuarioIdCreacion
        INNER JOIN almacenes A ON A.id = IV.almacen
        INNER JOIN unidades UN ON UN.id = IV.unidad
        ", $error);
    }

    public function consultarPorRequisicion()
    {
        return Conexion::queryAll($this->bdName, "SELECT IV.*
                    FROM $this->tableName  IV
                    WHERE IV.requisicionId = $this->requisicionId
        ", $error);
    }

    public function crear($datos)
    {

        $arrayPDOParam = array();
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        if(isset($datos["ordenCompra"])) $arrayPDOParam["ordenCompra"] = self::$type["ordenCompra"];
        $arrayPDOParam["almacen"] = self::$type["almacen"];
        $arrayPDOParam["entrega"] = self::$type["entrega"];
        $arrayPDOParam["firma"] = self::$type["firma"];
	$arrayPDOParam["folio"] = self::$type["folio"];
	$arrayPDOParam["numeroContrato"] = self::$type["numeroContrato"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        if( isset($datos["requisicionId"]) && $datos["requisicionId"] > 0 ) $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["fechaCreacion"] = self::$type["fechaCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $lastId = $this->consultarLastFolio();
        
        $datos["folio"] = (int) ($lastId["folio"] ?? 0) + 1;

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId=0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error,$lastId);
        if ( $respuesta ){

            $this->id = $lastId;
        } 

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];
        $arrayPDOParam["numeroParte"] = self::$type["numeroParte"];
        $arrayPDOParam["marca"] = self::$type["marca"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["fechaAdquisicion"] = self::$type["fechaAdquisicion"];
        $arrayPDOParam["costo"] = self::$type["costo"];
	$arrayPDOParam["almacen"] = self::$type["almacen"];
	$arrayPDOParam["numeroContrato"] = self::$type["numeroContrato"];
        $arrayPDOParam["estante"] = self::$type["estante"];
        $arrayPDOParam["pasillo"] = self::$type["pasillo"];
        $arrayPDOParam["ordenCompra"] = self::$type["ordenCompra"];
        $arrayPDOParam["nivel"] = self::$type["nivel"];
        
        $datos["fechaAdquisicion"] = fFechaSQL($datos["fechaAdquisicion"]);
        $datos["costo"] =   str_replace(',', '', $datos["costo"]);
        $datos["cantidad"] =  str_replace(',', '', $datos["cantidad"]);

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ($respuesta) {
            
            if ( isset($datos["observacion"]) ) {

                $insertarPDOParam = array();

                $insertar["inventarioId"] = $this->id;
                $insertar["observacion"] = $datos["observacion"];
                $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

                $insertarPDOParam["inventarioId"] = self::$type["id"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO inventario_observaciones " . $campos, $insertar, $insertarPDOParam, $error);

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
