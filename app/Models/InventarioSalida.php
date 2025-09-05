<?php

namespace App\Models;

if ( file_exists ( "app/Policies/InventarioSalidaPolicy.php" ) ) {
    require_once "app/Policies/InventarioSalidaPolicy.php";
} else {
    require_once "../Policies/InventarioSalidaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\InventarioSalidaPolicy;

class InventarioSalida extends InventarioSalidaPolicy
{
    static protected $fillable = [
        'inventario', 'firma', 'usuarioIdRecibe', 'detalles', 'usuarioIdAutoriza'
    ];

    static protected $type = [
        'id' => 'integer',        
        'folio' => 'integer',
        'inventario' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdRecibe' => 'integer',
        'usuarioIdAutoriza' => 'integer',
        'firma' => 'string',
        'fechaCreacion' => 'date',
        'fechaEntrega' => 'date',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "inventario_salidas";

    protected $keyName = "id";

    public $id = null;

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
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {
            $id = usuarioAutenticado()["id"];
            return Conexion::queryAll($this->bdName, "SELECT 
                                                        INS.id, 
                                                        I.ordenCompra,
                                                        INS.fechaEntrega,
                                                        INS.inventario as 'entradaId',
                                                        CONCAT(UA.nombre, ' ', UA.apellidoPaterno, ' ', IFNULL(UA.apellidoMaterno, '')) AS 'nombreAutoriza',
                                                        CONCAT(UE.nombre, ' ', UE.apellidoPaterno, ' ', IFNULL(UE.apellidoMaterno, '')) AS 'nombreEntrega',
                                                        CONCAT(UR.nombre, ' ', UR.apellidoPaterno, ' ', IFNULL(UR.apellidoMaterno, '')) AS 'nombreRecibe',
                                                        A.nombre AS nombreAlmacen
                                                    FROM inventario_salidas INS
                                                    INNER JOIN usuarios UA ON INS.usuarioIdAutoriza = UA.id
                                                    INNER JOIN usuarios UE ON INS.usuarioIdCreacion = UE.id
                                                    INNER JOIN usuarios UR ON INS.usuarioIdRecibe = UR.id
                                                    INNER JOIN inventarios I ON INS.inventario = I.id
                                                    INNER JOIN almacenes A ON I.almacen = A.id;", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT INVS.*, 
                    CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'recibe',
                    CONCAT(U2.nombre, ' ', U2.apellidoPaterno, ' ', IFNULL(U2.apellidoMaterno, '')) AS 'entrega'
                    FROM $this->tableName INVS
                    LEFT JOIN usuarios U ON U.id = INVS.usuarioIdRecibe
                    INNER JOIN usuarios U2 ON U2.id = INVS.usuarioIdCreacion
                    WHERE INVS.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {

                $this->id = $respuesta["id"];
                $this->folio = $respuesta["folio"];
                $this->inventario = $respuesta["inventario"];
                $this->usuarioRecibe = $respuesta["recibe"];
                $this->usuarioEntrega = $respuesta["entrega"];
                $this->usuarioIdAutoriza = $respuesta["usuarioIdAutoriza"];
                $this->observaciones = $respuesta["observaciones"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->firma = $respuesta["firma"];
                $this->fechaCreacion = fFechaLarga($respuesta["fechaCreacion"]);         
                $this->fechaActualizacion = fFechaLarga($respuesta["fechaActualizacion"]);            
                

            }

            return $respuesta;

        }
    }
    
    public function consultarLastFolio(){
        return Conexion::queryUnique($this->bdName,"SELECT folio 
                                                    FROM $this->tableName
                                                    WHERE folio = (SELECT MAX(folio) FROM $this->tableName)");
    }

    public function consultarSalidas($id)
    {
        return Conexion::queryAll($this->bdName, "SELECT INVS.*, 
            CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'recibe'
        FROM $this->tableName  INVS
        LEFT JOIN usuarios U ON U.id = INVS.usuarioIdCreacion
        INNER JOIN inventarios INV ON INV.id = INVS.inventario
        WHERE INVS.inventario = $id
        ", $error);
    }

    public function consultarDetalles($id)
    {
        return Conexion::queryAll($this->bdName, 
        "SELECT ISD.partida, ISD.id, ISD.cantidad, U.descripcion as 'unidad.descripcion', ID.numeroParte , IFNULL(I.descripcion, INS.descripcion) AS descripcion, IFNULL( P.concepto , '') AS concepto, IFNULL((SELECT SUM(cantidad) FROM resguardo_detalles RD WHERE RD.partida = ISD.partida),0) AS 'cantidadSalidas' FROM inventario_salida_detalles ISD INNER JOIN inventario_detalles ID ON ID.id = ISD.partida left JOIN partidas P ON P.id = ID.partida LEFT JOIN indirectos I ON I.id = ID.indirecto LEFT JOIN insumos INS ON INS.id = ID.directo LEFT JOIN unidades U ON U.id = I.unidadId OR U.id = INS.unidadId WHERE ISD.inventario =  $id", $error);
    }

    public function crear($datos)
    {

        $arrayPDOParam = array();
        $arrayPDOParam["inventario"] = self::$type["inventario"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

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
        $arrayPDOParam["usuarioIdRecibe"] = self::$type["usuarioIdRecibe"];
        $arrayPDOParam["fechaEntrega"] = self::$type["fechaEntrega"];
        $arrayPDOParam["firma"] = self::$type["firma"];

        $datos["fechaEntrega"] = gmdate("Y-m-d H:i:s", time() - 6 * 3600);
        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;

    }


    public function autorizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["usuarioIdAutoriza"] = self::$type["usuarioIdAutoriza"];

        $datos["usuarioIdAutoriza"] = usuarioAutenticado()["id"];
        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

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

    public function insertarDetalles($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["inventario"] = self::$type["inventario"];
        $arrayPDOParam["partida"] = "integer";
        $arrayPDOParam["cantidad"] = "float";

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO inventario_salida_detalles " . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

}
