<?php

namespace App\Models;

if ( file_exists ( "app/Policies/CotizacionPolicy.php" ) ) {
    require_once "app/Policies/CotizacionPolicy.php";
} else {
    require_once "../Policies/CotizacionPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\CotizacionPolicy;

class Cotizacion extends CotizacionPolicy
{
    static protected $fillable = [
        'requisicionId', 'proveedorId', 'estatus', 'fechaLimite'
    ];

    static protected $type = [
        'id' => 'integer',
        'requisicionId' => 'integer',
        'proveedorId' => 'integer',
        'estatus' => 'integer',
        'fechaLimite' => 'date',
        'fechaCreacion' => 'date',
        'observaciones' => 'string',
        'usuarioIdCreacion' => 'integer',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "cotizaciones";

    protected $keyName = "id";

    public $id = null;
    public $requisicionId;
    public $proveedorId;
    public $estatus;

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

    /*=============================================
    Consulta Cotizaciones
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->requisicionId = $respuesta["requisicionId"];
                $this->proveedorId = $respuesta["proveedorId"];
                $this->fechaLimite = $respuesta["fechaLimite"];
                $this->estatus = $respuesta["estatus"];

            }

            return $respuesta;

        }
    }

    public function consultarPorRequisicion($requisicionId = null)
    {
        if ( is_null($requisicionId) ) return false;

        $respuesta = Conexion::queryAll($this->bdName, "SELECT c.*, 
            p.razonSocial AS 'proveedores.razonSocial', p.id AS 'proveedores.id',
            CASE 
            WHEN c.estatus = 0 THEN 'Pendiente de Respuesta'
            WHEN c.estatus = 1 THEN 'Respondido'
            WHEN c.estatus = 2 THEN 'Expirado'
            ELSE 'Desconocido'
            END AS 'estatus.descripcion' 
            FROM $this->tableName c
            INNER JOIN proveedores p ON c.proveedorId = p.id
            WHERE c.requisicionId = $requisicionId", $error);

        return $respuesta;
    }

    public function consultarPorProveedor($proveedorId = null)
    {
        if ( is_null($proveedorId) ) return false;

        $respuesta = Conexion::queryAll($this->bdName, "SELECT c.*, 
            r.fechaRequerida AS 'requisiciones.fechaRequerida',
            CASE 
            WHEN NOW() > c.fechaLimite THEN 'Expirado'
            WHEN c.estatus = 0 THEN 'Pendiente de Respuesta'
            WHEN c.estatus = 1 THEN 'Respondido'
            ELSE 'Desconocido'
            END AS 'estatus.descripcion' 
            FROM $this->tableName c
            INNER JOIN requisiciones r ON c.requisicionId = r.id
            WHERE c.proveedorId = $proveedorId 
            ORDER BY c.fechaLimite DESC", $error);

        return $respuesta;
    }

    public function crear($datos)
    {

        $arrayPDOParam = array();        
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["fechaLimite"] = self::$type["fechaLimite"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["fechaLimite"] = fFechaSQLConHora($datos["fechaLimite"]);

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $cotizacionId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $cotizacionId);

        if ( $respuesta ) $this->id = $cotizacionId;

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        $datos["estatus"] = 1; // 0: Pendiente de Respuesta, 1: Respondido, 2: Expirado
        
        $arrayPDOParam = array();
        // $arrayPDOParam["insumoTipoId"] = self::$type["insumoTipoId"];
        $arrayPDOParam["estatus"] = self::$type["estatus"];

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

    public function insertarArchivos($requisicionId, $archivos, $dir="")
    {
        
        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/requisiciones/cotizaciones/";

                $extension = '';
                if (!is_dir($dir.$directorio)) {
                    // Crear el directorio si no existe
                    mkdir($dir.$directorio, 0777, true);
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
            $insertar["requisicionId"] = $requisicionId;
            $insertar["tipo"] = 4; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["proveedorId"] = usuarioAutenticadoProveedor()["id"] ?? $this->proveedorId ;

            $arrayPDOParam = array();        
            $arrayPDOParam["requisicionId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["proveedorId"] = "integer";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $dir.$ruta);
            }

        }

        return $respuesta;
    }

}
