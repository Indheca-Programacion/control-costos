<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ResguardoPolicy.php" ) ) {
    require_once "app/Policies/ResguardoPolicy.php";
} else {
    require_once "../Policies/ResguardoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ResguardoPolicy;

class Resguardo extends ResguardoPolicy
{
    static protected $fillable = [
        'obra','inventario', 'cantidad', 'fechaAsignacion','usuarioRecibio','firma','fk_usuarioEntrego', 'archivos', 'observaciones', 'empresa', 'estatus'
    ];

    static protected $type = [
        'id' => 'integer',
        'usuarioRecibio' => 'integer',
        'empresa' => 'integer',
        'usuarioEntrego' => 'integer',
        'obra' => 'integer',
        'inventario' => 'integer',
        'estatus' => 'integer',
        'firma' => 'string',
        'cantidad' => 'integer',
        'observaciones' => 'string',
        'fechaAsignacion' => 'date',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "resguardos";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR UNIDADES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                "SELECT 
                R.id ,
                O.descripcion as 'obra.descripcion',
                R.fechaAsignacion,
                R.observaciones,
                CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombre.recibio',
                CONCAT(USEN.nombre, ' ', USEN.apellidoPaterno, ' ', IFNULL(USEN.apellidoMaterno, '')) AS 'nombre.entrego'
                FROM resguardos R
                LEFT JOIN usuarios US ON US.id = R.usuarioRecibio
                LEFT JOIN obras O ON R.obra = O.id
                LEFT JOIN usuarios USEN ON USEN.id = R.usuarioEntrego", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT 
                R.id,
                R.obra,
                R.inventario,
                R.fechaAsignacion,
                R.usuarioEntrego,
                CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', U.apellidoMaterno) as usuarioRecibio,
                R.usuarioRecibio as usuarioRecibioId,
                R.firma,
                R.observaciones,
                I.almacen
                FROM $this->tableName R
                LEFT JOIN usuarios U on R.usuarioRecibio = U.id
                INNER JOIN inventario_salidas INS ON R.inventario = INS.id
                INNER JOIN inventarios I ON INS.inventario = I.id
                WHERE R.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT R.*, IV.unidad, IV.descripcion FROM $this->tableName R INNER JOIN inventarios IV ON IV.id = R.inventario WHERE R.$item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->usuarioEntrego = $respuesta["usuarioEntrego"];
                $this->usuarioRecibio = $respuesta["usuarioRecibio"];
                $this->obra = $respuesta["obra"];
                $this->fechaAsignacion = $respuesta["fechaAsignacion"];
                $this->inventario = $respuesta["inventario"];
                $this->observaciones = $respuesta["observaciones"];
                $this->firma = $respuesta["firma"];
                $this->usuarioRecibioId = $respuesta["usuarioRecibioId"];
                $this->almacen = $respuesta["almacen"];


            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        
   
        $arrayPDOParam = array();        
        if (isset($datos["obra"])) $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["inventario"] = self::$type["inventario"];
        if (isset($datos["fechaAsignacion"]))  $arrayPDOParam["fechaAsignacion"] = self::$type["fechaAsignacion"];
        if (isset($datos["usuarioRecibio"])) $arrayPDOParam["usuarioRecibio"] = self::$type["usuarioRecibio"];
        if (isset($datos["observaciones"])) $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        if (isset($datos["empresa"])) $arrayPDOParam["empresa"] = self::$type["empresa"];
        if (isset($datos["firma"])) $arrayPDOParam["firma"] = self::$type["firma"];
        $arrayPDOParam["usuarioEntrego"] = self::$type["usuarioEntrego"];
        if (isset($datos["estatus"])) $arrayPDOParam["estatus"] = self::$type["estatus"];

        $datos["usuarioEntrego"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;

            if (isset($datos["archivos"]) && $datos['archivos']['name'][0] != '') {
                
                $respuesta = $this->insertarArchivos($datos['archivos']);
            }
        }
        return $respuesta;
    }

    public function insertarDetalles($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["cantidad"] = 'integer';
        $arrayPDOParam["inventario"] = 'integer';
        $arrayPDOParam["partida"] = 'integer';

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO resguardo_detalles" . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function partidasResguardo(){

        return Conexion::queryAll($this->bdName, "SELECT RD.*, 
		ID.numeroParte, 
        IFNULL(D.descripcion,IND.descripcion) AS descripcion,
        IFNULL(UISU.descripcion,UIND.descripcion) AS unidad
        FROM resguardo_detalles RD 
        INNER JOIN inventario_detalles ID ON RD.partida = ID.id
        LEFT JOIN indirectos IND ON ID.indirecto = IND.id
        LEFT JOIN insumos D ON ID.directo = D.id
        LEFT JOIN unidades UISU ON D.unidadId = UISU.id
        LEFT JOIN unidades UIND ON IND.unidadId = UIND.id

        WHERE RD.inventario = $this->id", $error);
    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario

            if (isset($datos["archivos"]) && $datos['archivos']['name'][0] != '') {
                
                $respuesta = $this->insertarArchivos($datos['archivos']);
            }

        }

        return $respuesta;


    }

    public function actualizarConFirma($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["usuarioRecibio"] = self::$type["usuarioRecibio"];
        $arrayPDOParam["fechaAsignacion"] = self::$type["fechaAsignacion"];
        $arrayPDOParam["firma"] = self::$type["firma"];

        $datos["usuarioRecibio"] =  $datos["usuarioIdRecibe"];
        $datos["fechaAsignacion"] = gmdate("Y-m-d H:i:s", time() - 6 * 3600);

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];


        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE inventario = :id", $datos, $arrayPDOParam, $error);

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

    public function consultarArchivos(){
        $respuesta = Conexion::queryAll($this->bdName,"SELECT * FROM resguardo_archivos where resguardo = $this->id");
        $this->archivos = $respuesta;
    }

    function insertarArchivos($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/resguardos/";
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = ".pdf";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["resguardo"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["resguardo"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = "integer";

            $campos = fCreaCamposInsert($arrayPDOParam);
            
            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO resguardo_archivos " . $campos, $insertar, $arrayPDOParam, $error);
            
            if ( $respuesta && $ruta != "" ) {

                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }
    
}
