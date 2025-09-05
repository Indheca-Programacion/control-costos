<?php

namespace App\Models;

// if ( file_exists ( "app/Policies/IndicadorPolicy.php" ) ) {
//     require_once "app/Policies/IndicadorPolicy.php";
// } else {
//     require_once "../Policies/IndicadorPolicy.php";
// }

use App\Conexion;
use PDO;
// use App\Policies\IndicadorPolicy;

// class Indicador extends IndicadorPolicy
class Partida
{
    static protected $fillable = [
        'diasSinCargaTitulo', 'requisicionId', 'obraDetalleId', 'costo', 'cantidad', 'periodo', 'concepto', 'unidadId', 'costo_unitario'
    ];

    static protected $type = [
        'id' => 'integer',
        'requisicionId' => 'integer',
        'obraDetalleId' => 'integer',
        'costo' => 'decimal',
        'cantidad' => 'decimal',
        'cantidad_disponible' => 'decimal',
        'periodo' => 'integer',
        'concepto' => 'string',
        'unidadId' => 'integer',
        'costo_unitario' => 'decimal'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "partidas";

    protected $keyName = "id";

    public $id = null;
    public $requisicionId;
    public $obraDetalleId;
    public $costo;
    public $cantidad;
    public $periodo;
    public $concepto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR partidas
    =============================================*/
    public function consultar($item = null, $valor = null)
    {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT I.* FROM {$this->tableName} I ORDER BY I.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT I.* FROM {$this->tableName} I WHERE I.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT I.* FROM {$this->tableName} I WHERE I.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->cantidad_disponible = $respuesta["cantidad_disponible"];
                
            }

            return $respuesta;

        }

    }

    public function consultarPartidaPorRequisicion($requisicionId = null)
    {
         return  $respuesta = Conexion::queryAll($this->bdName, "SELECT 
                                                                    P.*,
                                                                    U.descripcion as unidadNombre
                                                                FROM 
                                                                    partidas P 
                                                                INNER JOIN 
                                                                    unidades U ON P.unidadId = U.id
                                                                WHERE 
                                                                    P.requisicionId = $requisicionId", $error);
    }

    public function crear($datos,$imagenes)
    {
        $arrayPDOParam = array();

        $datos["cantidad_disponible"] = $datos["cantidad"];
        $arrayPDOParam["obraDetalleId"] = self::$type["obraDetalleId"];
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["cantidad_disponible"] = self::$type["cantidad_disponible"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["periodo"] = self::$type["periodo"];
        $arrayPDOParam["concepto"] = self::$type["concepto"];
        $arrayPDOParam["unidadId"] = self::$type["unidadId"];
        $arrayPDOParam["costo_unitario"] = self::$type["costo_unitario"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastIdPartida= 0;

        $respuesta = Conexion::queryExecute($this->bdName,"INSERT INTO $this->tableName ". $campos, $datos, $arrayPDOParam, $error, $lastIdPartida);

        if ( $respuesta) {
            if (isset($datos["partida"])) {
                $partida = $datos["partida"];
                if ($imagenes['name'][$partida][0] != '') $respuestaImg = $this->insertarImagenes($imagenes,$partida,$lastIdPartida);
            }
        }

        return $respuesta;
    }

    public function actualizar($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];
        $arrayPDOParam["obraDetalleId"] = self::$type["obraDetalleId"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["concepto"] = self::$type["concepto"];
        $arrayPDOParam["unidadId"] = self::$type["unidadId"];
        $arrayPDOParam["costo_unitario"] = self::$type["costo_unitario"];
        
        $datos["id"] = $this->id;
        $datos["costo"] = str_replace(",","",$datos["costo"]);
        $datos["cantidad"] = str_replace(",","",$datos["cantidad"]);
        $datos["costo_unitario"] = str_replace(",","",$datos["costo_unitario"]);
        
        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET $campos WHERE $this->keyName = :id", $datos, $arrayPDOParam, $error);
    }

    public function eliminar()
    {
    }

    public function actualizarPartida($id)
    {
        $partidas = Conexion::queryAll($this->bdName, 
                                        "SELECT P.id, SUM(P.cantidad_disponible + ID.cantidad) AS cantidad_disponible
                                        FROM inventario_detalles ID
                                        INNER JOIN partidas P ON P.id = ID.partida
                                        WHERE ID.inventario = $id
                                        GROUP BY P.id", $error);
        
        $datos = array();
        foreach ($partidas as $partida) {
            array_push($datos, ["id" => $partida["id"], "cantidad_disponible" => $partida["cantidad_disponible"]]);
        }

        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];
        $arrayPDOParam["cantidad_disponible"] = self::$type["cantidad_disponible"];
        $campos = fCreaCamposUpdate($arrayPDOParam);

        foreach ($datos as $dato) {
            $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET $campos WHERE id = :id", $dato, $arrayPDOParam, $error);
        }

        return $respuesta;
    }
    
    public function insertarImagenes($archivos, $partida, $requisicionDetalleId) {
        for ($i = 0; $i < count($archivos['name'][$partida]); $i++) {

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            
            if ( $archivos["tmp_name"][$partida][$i] != "" ) {
                $archivo = $archivos["name"][$partida][$i];
                $tipo = $archivos["type"][$partida][$i];
                $tmp_name = $archivos["tmp_name"][$partida][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN                
                $directorio = "../../vistas/uploaded-files/requisiciones/detalle-imagenes/";

                do {
                    $ruta = fRandomNameImageFile($directorio, $tipo);
                } while ( file_exists($ruta) );
            }
            // Request con el nombre del archivo
            $insertar = array();
            $insertar["requisicionDetalleId"] = $requisicionDetalleId;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;

            $arrayPDOParam = array();        
            $arrayPDOParam["requisicionDetalleId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_detalle_imagenes " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                fSaveImageFile($tmp_name, $tipo, $ruta);
            }

        }

        return $respuesta;

    }

    public function consultarDetalles($id){
        return Conexion::queryAll($this->bdName, 
            "SELECT
                par.costo,
                par.periodo,
                CASE WHEN OD.insumoId IS NULL then 'indirecto'
                ELSE 'directo' END AS tipo
                FROM $this->tableName par
                INNER JOIN requisiciones R ON R.id = par.requisicionId
                INNER JOIN obras O ON O.id = R.fk_idObra
                INNER JOIN obra_detalles OD ON OD.id = par.obraDetalleId
                WHERE O.id = $id
                ORDER BY tipo", $error);
    }

}
