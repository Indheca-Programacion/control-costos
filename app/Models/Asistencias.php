<?php

namespace App\Models;

if ( file_exists ( "app/Policies/AsistenciasPolicy.php" ) ) {
    require_once "app/Policies/AsistenciasPolicy.php";
} else {
    require_once "../Policies/AsistenciasPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\AsistenciasPolicy;

class Asistencias extends AsistenciasPolicy
{
    static protected $fillable = [
        'fk_empleado', 'horaEntrada', 'horaSalida', 'horasExtras', 'fecha', 'empleado', 'jornada', 'fk_obraId', 'jornada_archivos'
    ];

    static protected $type = [
        'fk_empleado' => 'integer',
        'horaEntrada' => 'string',
        'horaSalida' => 'string',
        'horasExtras' => 'integer',
        'fecha' => 'date',
        'fk_obraId' => 'integer',
        'incidencia' => 'integer',
        'observacion' => 'string',
        'usuarioIdCreacion' => 'integer',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "asistencias";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT A.id, A.horaEntrada, A.horaSalida, A.fecha, A.incidencia,
                                                    U.nombre AS 'usuarios.nombre', U.apellidoPaterno AS 'usuarios.apellidoPaterno',
                                                    E.nombre AS 'empleados.nombre', CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto,
                                                    O.nombreCorto AS 'obra.nombre'
                                                    FROM $this->tableName A
                                                    INNER JOIN usuarios U ON U.id = A.usuarioIdCreacion
                                                    INNER JOIN empleados E ON E.id = A.fk_empleado
                                                    INNER JOIN obras O ON O.id = A.fk_obraId
                                                    ORDER BY A.fecha desc", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];                
                $this->nombre = $respuesta["nombre"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreBD = $respuesta["nombreBD"];
            }

            return $respuesta;

        }

    }

    public function consultarAsistencias($fechaInicial,$fechaFinal,$obra){
        return Conexion::queryAll($this->bdName,"SELECT A.* , CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto FROM $this->tableName A
                                                INNER JOIN empleados E ON E.id = A.fk_empleado
                                                WHERE A.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND A.fk_obraId = $obra ",$error);
    }

    public function crear($datos,$archivos=null) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["fk_empleado"] = self::$type["fk_empleado"];
        $arrayPDOParam["horaEntrada"] = self::$type["horaEntrada"];
        $arrayPDOParam["horaSalida"] = self::$type["horaSalida"];
        $arrayPDOParam["horasExtras"] = self::$type["horasExtras"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["incidencia"] = self::$type["incidencia"];
        $arrayPDOParam["fk_obraId"] = self::$type["fk_obraId"];
        $arrayPDOParam["observacion"] = self::$type["observacion"];
    
        $campos = fCreaCamposInsert($arrayPDOParam);

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);

        //TODO: Hacer la subida de archivos
        // if ( $respuesta) {
        //     $partida = $datos["partida"];
        //     if ($archivos['name'][$partida][0] != '') $respuestaImg = $this->insertarArchivos($archivos,$partida,$lastId);
        // }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreBD"] = self::$type["nombreBD"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreBD = :nombreBD WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function getPuestos($obraId) {
        $fecha = date('Y-m-d');
        return Conexion::queryAll($this->bdName, 
            "SELECT distinct RP.id as id, O.periodos , I.descripcion AS descripcionI, D.descripcion AS descripcionD
            FROM requisiciones_personal RP
            INNER JOIN obra_detalles OD ON OD.id = RP.fk_obraDetalleId
            LEFT JOIN insumos D ON D.id = OD.insumoId
            LEFT JOIN indirectos I ON I.id = OD.indirectoId
            INNER JOIN obras O ON O.id = OD.obraId
            WHERE obraId = $obraId AND RP.usuarioIdAutorizacion is not null AND RP.usuarioIdAuthRH is not null");
    }

    public function getEmpleados($empleados,$fecha){
        return Conexion::queryAll($this->bdName,
            "SELECT E.id, E.apellidoMaterno, E.apellidoPaterno, E.nombre, CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto
            FROM empleados E
            LEFT JOIN asistencias A ON E.id = A.fk_empleado AND A.fecha = '$fecha'
            WHERE E.id IN ($empleados) AND A.fk_empleado IS NULL" 
        );
    }

    function insertarArchivos($archivos,$partida,$asistenciaId) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/asistencias/";
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
            $insertar["asistenciaId"] = $asistenciaId;
            $insertar["tipo"] = $tipoArchivo; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["asistenciaId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO asistencias_archivos " . $campos, $insertar, $arrayPDOParam, $error);
            
            if ( $respuesta && $ruta != "" ) {
                echo "si se pudo";
                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }
}
