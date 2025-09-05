<?php

namespace App\Models;

// if ( file_exists ( "app/Policies/InsumoTipoPolicy.php" ) ) {
//     require_once "app/Policies/InsumoTipoPolicy.php";
// } else {
//     require_once "../Policies/InsumoTipoPolicy.php";
// }

use App\Conexion;
use PDO;
// use App\Policies\InsumoTipoPolicy;

class InventarioDetalles
{
    static protected $fillable = [
        'inventario', 'indirecto', 'directo', 'numeroParte', 'cantidad'
    ];

    static protected $type = [
        'id' => 'integer',
        'inventario' => 'integer',
        'indirecto' => 'integer',
        'directo' => 'integer',
        'cantidad' => 'decimal',
        'numeroParte' => 'string',
        'partida' => 'integer',
        'partidaTraslado' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "inventario_detalles";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE INSUMOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT IT.* FROM $this->tableName IT ORDER BY IT.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->orden = $respuesta["orden"];
                $this->perfilesCrearRequis = json_decode($respuesta["perfilesCrearRequis"]);

            }

            return $respuesta;

        }

    }

    public function consultarDetalles()
    {
            return Conexion::queryAll($this->bdName, "SELECT ID.id, ID.numeroParte, ID.cantidad, IFNULL(I.descripcion, INS.descripcion) AS descripcion, 
            U.descripcion as 'unidad.descripcion', IFNULL(P.concepto,'') AS 'concepto', 
            (SELECT SUM(cantidad) FROM inventario_salida_detalles WHERE partida = ID.id) AS cantidadDisponible, 
            IFNULL((SELECT SUM(cantidad) FROM inventario_salida_detalles WHERE partida = ID.id),0) AS 'cantidadSalidas'
                                                    FROM $this->tableName ID
                                                    LEFT JOIN indirectos I ON I.id = ID.indirecto
                                                    LEFT JOIN insumos INS ON INS.id = ID.directo
                                                    left JOIN partidas P ON P.id = ID.partida
                                                    LEFT JOIN unidades U ON U.id = I.unidadId OR U.id = INS.unidadId
                                                    WHERE ID.inventario = $this->inventario", $error);
    }

    public function consultarDisponibles()
    {
        return Conexion::queryAll($this->bdName, "SELECT ID.id, ID.numeroParte, ID.cantidad, IFNULL(I.descripcion, INS.descripcion) AS descripcion, 
        U.descripcion as 'unidad.descripcion', IFNULL(P.concepto,'') as 'concepto', IFNULL((SELECT SUM(cantidad) FROM inventario_salida_detalles 
        WHERE partida = ID.id),0) AS 'cantidadSalidas', IFNULL(I.resguardos, INS.resguardos) AS 'resguardo'
                                                  FROM $this->tableName ID
                                                  LEFT JOIN indirectos I ON I.id = ID.indirecto
                                                  LEFT JOIN insumos INS ON INS.id = ID.directo
                                                  left JOIN partidas P ON P.id = ID.partida
                                                  LEFT JOIN unidades U ON U.id = I.unidadId OR U.id = INS.unidadId
                                                  WHERE ID.inventario = $this->inventario HAVING cantidadSalidas < ID.cantidad;", $error);
    }

    public function consultarInventarios($id, $permiso){

        $query = "SELECT AL.nombreCorto AS 'almacen.descripcion', SUM(ID.cantidad) AS cantidad,
            U.descripcion as 'unidad.descripcion', MIN(INV.folio) AS folio,  -- Se usa MIN() para folio
            IFNULL(I.descripcion, INS.descripcion) AS descripcion,
            INV.ordenCompra, CONCAT(O.prefijo,'-',R.folio) AS requisicion,
            INV.id,
            ID.inventario as inventario
            FROM inventario_detalles ID
            INNER JOIN inventarios INV ON INV.id = ID.inventario
            INNER JOIN almacenes AL ON AL.id = INV.almacen
            LEFT JOIN requisiciones R ON R.id = INV.requisicionId
            LEFT JOIN obras O ON O.id = R.fk_idObra
            LEFT JOIN indirectos I ON I.id = ID.indirecto
            LEFT JOIN insumos INS ON INS.id = ID.directo
            LEFT JOIN unidades U ON U.id = I.unidadId OR U.id = INS.unidadId";

        if (!$permiso) {
            $query .= " WHERE INV.id = $id";
        }

        $query .= " GROUP BY 
                    AL.nombreCorto,
                    INV.ordenCompra,
                    O.prefijo,
                    R.folio,
                    INV.id,
                    IFNULL(I.descripcion, INS.descripcion),
                    U.descripcion;"; // Se ordena por el alias "folio" que ahora tiene el valor mínimo de folio
        

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);
        return $respuesta;
        
    }

    public function consultarExistencias($indirectos,$directos){

        $query = "SELECT ID.id, ID.numeroParte, ID.cantidad, IFNULL(I.descripcion, INS.descripcion) AS descripcion, 
        U.descripcion as 'unidad.descripcion', IFNULL(P.concepto,'') as 'concepto', 
        IFNULL((SELECT SUM(cantidad) FROM inventario_salida_detalles WHERE partida = ID.id),0) AS 'cantidadSalidas', 
        IFNULL((SELECT SUM(cantidad) FROM inventario_detalles WHERE partidaTraslado = ID.id),0) AS 'cantidadTraslados',
        IFNULL(I.resguardos, INS.resguardos) AS 'resguardo',
        AL.nombreCorto as 'almacen.descripcion', AL.id as 'almacen.id', I.id as 'indirecto', INS.id as 'directo'
                                                  FROM $this->tableName ID
                                                  inner join inventarios INV ON INV.id = ID.inventario
                                                  inner join almacenes AL ON AL.id = INV.almacen
                                                  LEFT JOIN indirectos I ON I.id = ID.indirecto
                                                  LEFT JOIN insumos INS ON INS.id = ID.directo
                                                  left JOIN partidas P ON P.id = ID.partida
                                                  LEFT JOIN unidades U ON U.id = I.unidadId OR U.id = INS.unidadId
                                                  WHERE ID.partidaTraslado is null
                                                 ";
            if ($indirectos !== "") {
                $query.= " AND ID.indirecto IN ($indirectos)";
            }
            if ($directos !== "") {
                if ($indirectos !== "") {
                    $query.= " OR ";
                }else {
                    $query.= " AND ";
                }
                $query.= " ID.directo IN ($directos)";
            }

        $query.= "  HAVING cantidadSalidas < ID.cantidad;";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["inventario"] = self::$type["inventario"];
        $arrayPDOParam["indirecto"] = self::$type["indirecto"];
        $arrayPDOParam["directo"] = self::$type["directo"];
        $arrayPDOParam["numeroParte"] = self::$type["numeroParte"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["partida"] = self::$type["partida"];
        if (isset($datos["partidaTraslado"])) {
            $arrayPDOParam["partidaTraslado"] = self::$type["partidaTraslado"];
        }

        $datos["inventario"] = $this->inventario;
        $datos["cantidad"] = (float) str_replace(',','',$datos["cantidad"]);

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["orden"] = self::$type["orden"];
        $arrayPDOParam["perfilesCrearRequis"] = self::$type["perfilesCrearRequis"];


        if (!isset($datos["perfilesCrearRequis"])) {
            $datos["perfilesCrearRequis"] = "[]";
        }else{
            $datos["perfilesCrearRequis"] = json_encode($datos["perfilesCrearRequis"]);
        }

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, orden = :orden, perfilesCrearRequis = :perfilesCrearRequis WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function insertarImagen($id, $archivos) {
        for ($i = 0; $i < count($archivos['name']); $i++) {

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN                
                $directorio = "../../vistas/uploaded-files/inventarios/detalle-imagenes/";

                do {
                    $ruta = fRandomNameImageFile($directorio, $tipo);
                } while ( file_exists($ruta) );

            }
            // Request con el nombre del archivo
            $insertar = array();
            $insertar["inventario_detalle"] = $id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;

            $arrayPDOParam = array();        
            $arrayPDOParam["inventario_detalle"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO inventario_detalle_imagenes " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                // move_uploaded_file($tmp_name, $ruta);
                fSaveImageFile($tmp_name, $tipo, $ruta);
            }

        }
    }

    public function consultarImagenes($id) {
        return Conexion::queryAll($this->bdName, "SELECT * FROM inventario_detalle_imagenes WHERE inventario_detalle = $id", $error);
    }
}
