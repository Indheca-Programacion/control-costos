<?php

namespace App\Models;

if ( file_exists ( "app/Policies/IndirectoPolicy.php" ) ) {
    require_once "app/Policies/IndirectoPolicy.php";
} else {
    require_once "../Policies/IndirectoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\IndirectoPolicy;

class Indirecto extends IndirectoPolicy
{
    static protected $fillable = [
        'indirectoTipoId', 'numero', 'descripcion', 'unidadId','resguardos'
    ];

    static protected $type = [
        'id' => 'integer',
        'indirectoTipoId' => 'integer',
        'numero' => 'string',
        'segmento1' => 'integer',
        'segmento2' => 'integer',
        'segmento3' => 'integer',
        'descripcion' => 'string',
        'unidadId' => 'integer',
        'resguardos' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "indirectos";

    protected $keyName = "id";

    public $id = null;
    public $indirectoTipoId;
    public $numero;
    public $segmento1;
    public $segmento2;
    public $segmento3;
    public $descripcion;
    public $unidadId;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR INDIRECTOS
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT I.*, IT.numero AS 'indirecto_tipos.numero', IT.descripcion AS 'indirecto_tipos.descripcion', U.descripcion AS 'unidades.descripcion' FROM $this->tableName I INNER JOIN indirecto_tipos IT ON I.indirectoTipoId = IT.id INNER JOIN unidades U ON I.unidadId = U.id ORDER BY IT.segmento1, IT.segmento2, I.segmento1, I.segmento2, I.segmento3", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->indirectoTipoId = $respuesta["indirectoTipoId"];
                $this->numero = $respuesta["numero"];
                $this->segmento1 = $respuesta["segmento1"];
                $this->segmento2 = $respuesta["segmento2"];
                $this->segmento3 = $respuesta["segmento3"];
                $this->descripcion = $respuesta["descripcion"];
                $this->unidadId = $respuesta["unidadId"];
                $this->resguardos = $respuesta["resguardos"];

                if ( file_exists ( "app/Models/IndirectoTipo.php" ) ) {
                    require_once "app/Models/IndirectoTipo.php";
                } else {
                    require_once "../Models/IndirectoTipo.php";
                }
                $indirectoTipo = New IndirectoTipo;
                $this->indirectoTipo = $indirectoTipo->consultar(null, $this->indirectoTipoId);
            }

            return $respuesta;

        }
    }
    
    public function consultarLasCode($tipoid){
        return Conexion::queryUnique($this->bdName,"SELECT I.numero	
                                                    from $this->tableName I
                                                    INNER JOIN indirecto_tipos IT ON IT.id = I.indirectoTipoId
                                                    WHERE IT.id = $tipoid
                                                    ORDER BY I.id DESC LIMIT 1;");
    }
    /*=============================================
    MOSTRAR INDIRECTO POR ID OBRA
    =============================================*/
    public function consultarPorObra($idobra, $divisa = 1)
    {
        return Conexion::queryAll($this->bdName,
                                    "SELECT I.*, IT.numero AS 'indirecto_tipos.numero', IT.descripcion AS 'indirecto_tipos.descripcion',U.id as 'unidad.id', U.descripcion AS 'unidades.descripcion', od.cantidad as 'cantidad', od.presupuesto as 'presupuesto', od.presupuesto_dolares as 'presupuesto_dolares', od.id as 'obraDetalleId', IT.perfilesCrearRequis 
                                    FROM $this->tableName I 
                                    INNER JOIN indirecto_tipos IT ON I.indirectoTipoId = IT.id 
                                    INNER JOIN unidades U ON I.unidadId = U.id 
                                    INNER JOIN obra_detalles od on I.id = od.indirectoId 
                                    WHERE od.indirectoId IS NOT NULL AND od.obraId = $idobra 
                                    ORDER BY IT.segmento1, IT.segmento2, I.segmento1, I.segmento2, I.segmento3",$error);
    }

    public function consultarPorPlantilla($id){
        return Conexion::queryAll($this->bdName, "SELECT I.*, IT.numero AS 'indirecto_tipos.numero', IT.descripcion AS 'indirecto_tipos.descripcion', U.descripcion AS 'unidades.descripcion' FROM $this->tableName I INNER JOIN indirecto_tipos IT ON I.indirectoTipoId = IT.id INNER JOIN unidades U ON I.unidadId = U.id WHERE I.id NOT IN (
        SELECT indirectoId FROM plantilla_detalles WHERE fk_plantilla = $id AND indirectoId IS NOT NULL ) ORDER BY IT.segmento1, IT.segmento2, I.segmento1, I.segmento2, I.segmento3", $error);
    }

    /*=============================================
    CONSULTAR INDIRECTOS POR OBRA Y OBTENER SU COSTO TOTAL POR FILTROS
    =============================================*/
    public function consultarPorObraFiltros($obraId, $divisa, $presupuesto, $año = null)
    {
        $resultados = [];

        if ($año !== "all") {
            // Si se proporciona un año, calcular para todos los meses de ese año
            $query = "
                SELECT 
                    od.id,
                    od.cantidad,
                    od.presupuesto AS presupuesto,
                    od.presupuesto_dolares AS presupuesto_dolares,
                    IT.descripcion AS tipo, 
                    IND.descripcion AS descripcion,
                    IND.numero as numero,
                    U.descripcion AS unidad,
                    U.id AS unidadId,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 1 THEN partidas.costo ELSE 0 END) AS costo_enero,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 2 THEN partidas.costo ELSE 0 END) AS costo_febrero,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 3 THEN partidas.costo ELSE 0 END) AS costo_marzo,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 4 THEN partidas.costo ELSE 0 END) AS costo_abril,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 5 THEN partidas.costo ELSE 0 END) AS costo_mayo,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 6 THEN partidas.costo ELSE 0 END) AS costo_junio,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 7 THEN partidas.costo ELSE 0 END) AS costo_julio,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 8 THEN partidas.costo ELSE 0 END) AS costo_agosto,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 9 THEN partidas.costo ELSE 0 END) AS costo_septiembre,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 10 THEN partidas.costo ELSE 0 END) AS costo_octubre,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 11 THEN partidas.costo ELSE 0 END) AS costo_noviembre,
                    SUM(CASE WHEN MONTH(r.fechaRequerida) = 12 THEN partidas.costo ELSE 0 END) AS costo_diciembre,
                    SUM(partidas.cantidad) AS cantidad_total,
                    SUM(partidas.costo) AS costo_total,
                    count(partidas.id) AS partidas_count
                FROM obra_detalles od
                INNER JOIN indirectos IND ON IND.id = od.indirectoId
                INNER JOIN indirecto_tipos IT ON IND.indirectoTipoId = IT.id
                INNER JOIN unidades U ON IND.unidadId = U.id
                LEFT JOIN partidas ON partidas.obraDetalleId = od.id
                LEFT JOIN requisiciones r ON r.id = partidas.requisicionId AND YEAR(r.fechaRequerida) = $año
                WHERE od.obraId = $obraId
            ";

            if ($divisa !== null) {
                $query .= " AND (r.divisa = $divisa OR r.divisa IS NULL)";
            }
            if ($presupuesto !== null) {
                $query .= " AND (r.presupuesto = $presupuesto OR r.presupuesto IS NULL)";
            }

            $query .= " GROUP BY od.id, od.cantidad, od.presupuesto, od.presupuesto_dolares, IT.descripcion, IND.descripcion
            ORDER BY IT.descripcion, IND.numero";

            $resultados = Conexion::queryAll($this->bdName, $query, $error);
        } else {
            // Si no se proporciona año, calcular por año (sumarizado)
            // Obtener el año actual
            $añoActual = date('Y');
            $camposCostos = '';
            for ($anio = 2024; $anio <= $añoActual; $anio++) {
                $camposCostos .= ",
                    (
                        SELECT SUM(partidas.costo) 
                        FROM partidas 
                        INNER JOIN requisiciones r ON r.id = partidas.requisicionId 
                        WHERE partidas.obraDetalleId = od.id AND YEAR(r.fechaRequerida) = $anio
                    ) AS costo_$anio
                ";
            }

            $query = "
                SELECT 
                    od.id,
                    od.cantidad,
                    od.presupuesto AS presupuesto,
                    od.presupuesto_dolares AS presupuesto_dolares,
                    IT.descripcion AS tipo, 
                    IND.descripcion AS descripcion,
                    IND.numero as numero,
                    U.descripcion AS unidad,
                    SUM(partidas.cantidad) AS cantidad_total,
                    SUM(partidas.costo) AS costo_total,
                    U.id AS unidadId
                    $camposCostos
                FROM obra_detalles od
                INNER JOIN indirectos IND ON IND.id = od.indirectoId
                INNER JOIN indirecto_tipos IT ON IND.indirectoTipoId = IT.id
                INNER JOIN unidades U ON IND.unidadId = U.id
                LEFT JOIN partidas ON partidas.obraDetalleId = od.id
                LEFT JOIN requisiciones r ON r.id = partidas.requisicionId
                WHERE od.obraId = $obraId
            ";

            if ($divisa !== null) {
                $query .= " AND (r.divisa = $divisa OR r.divisa IS NULL)";
            }
            if ($presupuesto !== null) {
                $query .= " AND (r.presupuesto = $presupuesto OR r.presupuesto IS NULL)";
            }

            $query .= " GROUP BY od.id, od.cantidad, od.presupuesto, od.presupuesto_dolares, IT.descripcion, IND.descripcion
            ORDER BY IT.descripcion, IND.numero";

            $resultados = Conexion::queryAll($this->bdName, $query, $error);
        }

        return $resultados;
    }

    public function consultarFiltroDetalles($obraId)
    {
        $query = "SELECT I.*, IT.descripcion AS 'indirecto_tipos.descripcion', U.descripcion AS 'unidades.descripcion'
            FROM        indirectos I
            INNER JOIN  indirecto_tipos IT ON I.indirectoTipoId = IT.id
            INNER JOIN  unidades U ON I.unidadId = U.id
            WHERE I.id  NOT IN (
                SELECT indirectoId
                FROM obra_detalles
                WHERE obraId = $obraId AND indirectoId IS NOT NULL
            )";
        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    public function crear($datos)
    {
        $arraySegmentos = explode(".", str_replace('_', '', $datos["numero"]));
        $segmento1 = intval($arraySegmentos[0]);
        $segmento2 = intval($arraySegmentos[1]);
        $segmento3 = intval($arraySegmentos[2]);

        // Agregar al request para especificar los segmentos
        $datos["numero"] = "{$segmento1}.{$segmento2}.{$segmento3}";
        $datos["segmento1"] = $segmento1;
        $datos["segmento2"] = $segmento2;
        $datos["segmento3"] = $segmento3;

        $arrayPDOParam = array();
        $arrayPDOParam["indirectoTipoId"] = self::$type["indirectoTipoId"];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["segmento1"] = self::$type["segmento1"];
        $arrayPDOParam["segmento2"] = self::$type["segmento2"];
        $arrayPDOParam["segmento3"] = self::$type["segmento3"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["unidadId"] = self::$type["unidadId"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $indirectoId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $indirectoId);

        if ( $respuesta ) $this->id = $indirectoId;

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        $datos["resguardos"] = ( isset($datos["resguardos"]) && mb_strtolower($datos["resguardos"]) == "on" ) ? "1" : "0";

        $arraySegmentos = explode(".", str_replace('_', '', $datos["numero"]));
        $segmento1 = intval($arraySegmentos[0]);
        $segmento2 = intval($arraySegmentos[1]);
        $segmento3 = intval($arraySegmentos[2]);

        // Agregar al request para especificar los segmentos
        $datos["numero"] = "{$segmento1}.{$segmento2}.{$segmento3}";
        $datos["segmento1"] = $segmento1;
        $datos["segmento2"] = $segmento2;
        $datos["segmento3"] = $segmento3;

        $arrayPDOParam = array();
        // $arrayPDOParam["indirectoTipoId"] = self::$type["indirectoTipoId"];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["segmento1"] = self::$type["segmento1"];
        $arrayPDOParam["segmento2"] = self::$type["segmento2"];
        $arrayPDOParam["segmento3"] = self::$type["segmento3"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["unidadId"] = self::$type["unidadId"];
        $arrayPDOParam["resguardos"] = self::$type["resguardos"];

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
}
