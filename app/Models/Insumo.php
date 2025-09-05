<?php

namespace App\Models;

if ( file_exists ( "app/Policies/InsumoPolicy.php" ) ) {
    require_once "app/Policies/InsumoPolicy.php";
} else {
    require_once "../Policies/InsumoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\InsumoPolicy;

class Insumo extends InsumoPolicy
{
    static protected $fillable = [
        'insumoTipoId', 'codigo', 'descripcion', 'unidadId', 'cantidad', 'presupuesto','obraId', 'insumoId', 'resguardos'
    ];

    static protected $type = [
        'id' => 'integer',
        'insumoTipoId' => 'integer',
        'codigo' => 'string',
        'descripcion' => 'string',
        'unidadId' => 'integer',
        'insumoId' => 'integer',
        'cantidad' => 'float',
        'presupuesto' => 'float',
        'obraId' => 'integer',
        'resguardos' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "insumos";
    protected $tableObra = "obra_detalles";

    protected $keyName = "id";

    public $id = null;
    public $insumoTipoId;
    public $codigo;
    public $descripcion;
    public $unidadId;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR INSUMOS CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT I.*, IT.descripcion AS 'insumo_tipos.descripcion', U.descripcion AS 'unidades.descripcion'
            FROM        insumos I
            INNER JOIN  insumo_tipos IT ON I.insumoTipoId = IT.id
            INNER JOIN  unidades U ON I.unidadId = U.id";

        foreach ($arrayFiltros as $key => $value) {
            if ( $key == 0 ) $query .= " WHERE";
            if ( $key > 0 ) $query .= " AND";
            $query .= " {$value['campo']} = {$value['valor']}";
        }

        $query .= " ORDER BY    IT.orden, IT.descripcion, I.codigo";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    public function consultarLasCode($tipoid){
        return Conexion::queryUnique($this->bdName,"SELECT I.codigo	
                                                from $this->tableName I
                                                INNER JOIN insumo_tipos IT ON IT.id = I.insumoTipoId
                                                WHERE IT.id = $tipoid 
                                                ORDER BY I.id DESC LIMIT 1;");
    }
    
    /*=============================================
    MOSTRAR INSUMOS SIN REPETIR EN OBRA DETALLES
    =============================================*/
    public function consultarFiltroDetalles($obraId)
    {
        $query = "SELECT I.*, IT.descripcion AS 'insumo_tipos.descripcion', U.descripcion AS 'unidades.descripcion'
            FROM        insumos I
            INNER JOIN  insumo_tipos IT ON I.insumoTipoId = IT.id
            INNER JOIN  unidades U ON I.unidadId = U.id
            WHERE I.id  NOT IN (
                SELECT insumoId
                FROM obra_detalles
                WHERE obraId = $obraId AND insumoId IS NOT NULL
            )";
        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }
    /*=============================================
    MOSTRAR INSUMOS POR ID OBRA
    =============================================*/
    public function consultarPorObra($idobra,$divisa=1)
    {
        return Conexion::queryAll($this->bdName,
        "SELECT I.*, IT.descripcion AS 'insumo_tipos.descripcion',od.presupuesto as presupuesto, od.presupuesto_dolares as presupuesto_dolares,U.id as 'unidad.id', U.descripcion AS 'unidades.descripcion', od.cantidad as 'cantidad',od.presupuesto as 'presupuesto', od.presupuesto_dolares as 'presupuesto.dolares', od.id as 'obraDetalleId', IT.perfilesCrearRequis FROM $this->tableName I INNER JOIN insumo_tipos IT ON I.insumoTipoId = IT.id INNER JOIN unidades U ON I.unidadId = U.id INNER JOIN obra_detalles od ON I.id = od.insumoId WHERE od.insumoId IS NOT null AND od.obraId = $idobra ORDER BY I.insumoTipoId",$error);
    }
    //Crear query para consultar los insumos desde la tabla obras

    /*=============================================
    MOSTRAR INSUMOS
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT I.*, IT.descripcion AS 'insumo_tipos.descripcion', U.descripcion AS 'unidades.descripcion' FROM $this->tableName I INNER JOIN insumo_tipos IT ON I.insumoTipoId = IT.id INNER JOIN unidades U ON I.unidadId = U.id ORDER BY IT.orden, IT.descripcion, I.codigo", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->insumoTipoId = $respuesta["insumoTipoId"];
                $this->codigo = $respuesta["codigo"];
                $this->descripcion = $respuesta["descripcion"];
                $this->unidadId = $respuesta["unidadId"];
                $this->resguardos = $respuesta["resguardos"];

                if ( file_exists ( "app/Models/InsumoTipo.php" ) ) {
                    require_once "app/Models/InsumoTipo.php";
                } else {
                    require_once "../Models/InsumoTipo.php";
                }
                $insumoTipo = New InsumoTipo;
                $this->insumoTipo = $insumoTipo->consultar(null, $this->insumoTipoId);
            }

            return $respuesta;

        }
    }

    public function consultarPorPlantilla($id){
        return Conexion::queryAll($this->bdName, "SELECT I.*, IT.descripcion AS 'insumo_tipos.descripcion', U.descripcion AS 'unidades.descripcion' FROM $this->tableName I INNER JOIN insumo_tipos IT ON I.insumoTipoId = IT.id INNER JOIN unidades U ON I.unidadId = U.id WHERE I.id NOT IN (
        SELECT directoId FROM plantilla_detalles WHERE fk_plantilla = $id AND indirectoId IS NOT NULL ) ORDER BY IT.orden, IT.descripcion, I.codigo", $error);
    }

    /*=============================================
    CONSULTAR INSUMOS POR OBRA Y OBTENER SU COSTO TOTAL POR FILTROS
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
                    dir.descripcion AS descripcion,
                    dir.codigo as codigo,
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
                INNER JOIN insumos dir ON dir.id = od.insumoId
                INNER JOIN insumo_tipos IT ON dir.insumoTipoId = IT.id
                INNER JOIN unidades U ON dir.unidadId = U.id
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

            
            $query .= " GROUP BY od.id, od.cantidad, od.presupuesto, od.presupuesto_dolares, IT.descripcion, dir.descripcion
            ORDER BY IT.orden, IT.descripcion, dir.codigo";
            
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
                    dir.descripcion AS descripcion,
                    dir.codigo as codigo,
                    U.descripcion AS unidad,
                    SUM(partidas.cantidad) AS cantidad_total,
                    SUM(partidas.costo) AS costo_total,
                    U.id AS unidadId
                    $camposCostos
                FROM obra_detalles od
                INNER JOIN insumos dir ON dir.id = od.insumoId
                INNER JOIN insumo_tipos IT ON dir.insumoTipoId = IT.id
                INNER JOIN unidades U ON dir.unidadId = U.id
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
            
            $query .= " GROUP BY od.id, od.cantidad, od.presupuesto, od.presupuesto_dolares, IT.descripcion, dir.descripcion
            ORDER BY IT.orden, IT.descripcion, dir.codigo";

            $resultados = Conexion::queryAll($this->bdName, $query, $error);
        }

        return $resultados;
    }

    public function crear($datos)
    {
        $datos["resguardos"] = ( isset($datos["resguardos"]) && mb_strtolower($datos["resguardos"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();        
        $arrayPDOParam["insumoTipoId"] = self::$type["insumoTipoId"];
        $arrayPDOParam["codigo"] = self::$type["codigo"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["unidadId"] = self::$type["unidadId"];
        $arrayPDOParam["resguardos"] = self::$type["resguardos"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $insumoId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $insumoId);

        if ( $respuesta ) $this->id = $insumoId;

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        $datos["resguardos"] = ( isset($datos["resguardos"]) && mb_strtolower($datos["resguardos"]) == "on" ) ? "1" : "0";
        
        $arrayPDOParam = array();
        // $arrayPDOParam["insumoTipoId"] = self::$type["insumoTipoId"];
        $arrayPDOParam["codigo"] = self::$type["codigo"];
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

    public function agregar($data){
        $arrayPDOParam = array();        
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["insumoId"]  = self::$type["insumoId"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];
        
        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableObra " . $campos, $data, $arrayPDOParam,);

        return $respuesta;
    }
}
