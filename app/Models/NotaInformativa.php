<?php

namespace App\Models;

if ( file_exists ( "app/Policies/NotaInformativaPolicy.php" ) ) {
    require_once "app/Policies/NotaInformativaPolicy.php";
    require_once "app/Models/Usuario.php";

} else {
    require_once "../Policies/NotaInformativaPolicy.php";
    require_once "../Models/Usuario.php";

}

use App\Conexion;
use PDO;
use App\Policies\NotaInformativaPolicy;
use App\Models\Usuario;

class NotaInformativa extends NotaInformativaPolicy
{
    static protected $fillable = [
        'lugar', 'fecha', 'descripcion', 'fotos', 'requisicionId'
    ];

    static protected $type = [
        'id' => 'integer',
        'lugar' => 'string',
        'fecha' => 'date',
        'descripcion' => 'string',
        'requisicionId' => 'integer',
        'usuarioIdCreacion' => 'integer',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "nota_informativa";

    protected $keyName = "id";

    public $id = null;


    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR OBRAS
    =============================================*/
    public function consultar($item = null, $valor = null)
    {

        if ( is_null($valor) ) {
            return Conexion::queryAll($this->bdName, 
                                                    "SELECT NI.*, CONCAT(O.prefijo,'-', RQ.folio) AS requisicion, CONCAT(US.nombre,' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS creo
                                                    FROM $this->tableName NI
                                                    INNER JOIN requisiciones RQ ON RQ.id = NI.requisicionId
                                                    INNER JOIN obras O ON O.id = RQ.fk_idObra
                                                    INNER JOIN usuarios US ON US.id = NI.usuarioIdCreacion
                                                    ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->fecha = fFechaLarga($respuesta["fecha"]);
                $this->lugar = mb_strtoupper(fString($respuesta["lugar"]));
                $this->descripcion = mb_strtoupper(fString($respuesta["descripcion"]));
            }

            return $respuesta;

        }
    }

    public function consultarArchivos()
    {
        $respuesta = Conexion::queryAll($this->bdName, 
            "SELECT * FROM norma_informativa_archivos WHERE notaInformativaId = $this->id", $error);
        $this->imagenes = array();
        if ( $respuesta ) {
            foreach ($respuesta as $archivo) {
                $this->imagenes[] = array(
                    "id" => $archivo["id"],
                    "titulo" => $archivo["titulo"],
                    "archivo" => $archivo["archivo"],
                    "formato" => $archivo["formato"],
                    "ruta" => $archivo["ruta"]
                );
            }
        }
    }

    public function crear($datos)
    {
        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fecha"] = fFechaSQL($datos["fecha"]);

        $arrayPDOParam = array();
        $arrayPDOParam["lugar"] = self::$type["lugar"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["id"];
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {

            $this->id = $lastId;

            if ( isset($datos['fotos']) && $datos['fotos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['fotos']);

        } 

        return $respuesta;
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
                $directorio =  "../../vistas/uploaded-files/requisiciones/nota-informativa/";

                // $aleatorio = mt_rand(10000000,99999999);

                $extension = '';
                if (!is_dir($directorio)) {
                    // Crear el directorio si no existe
                    mkdir($directorio, 0777, true);
                }
                
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                if ($extension != '') {
                    $extension = '.' . $extension;
                }

                if ( $extension != '') {
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["notaInformativaId"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = substr($ruta, 6);
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["notaInformativaId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO norma_informativa_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

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
