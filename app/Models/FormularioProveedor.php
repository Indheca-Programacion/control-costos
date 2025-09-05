<?php

namespace App\Models;

use App\Conexion;
use PDO;

class FormularioProveedor
{
    static protected $fillable = [
        'razonSocial', 'rfc', 'correoElectronico', 'nombreApellido', 'telefono','origenProveedor','tipoProveedor','claveProveedor','entregaMaterial','diasCredito','terminosCondiciones','constanciaFiscal','opinionCumplimiento','comprobanteDomicilio','datosBancarios'
    ];

    static protected $type = [
        'id' => 'integer',

        'razonSocial' => 'string',
        'rfc' => 'string',
        'correoElectronico' => 'string|email',
        'nombreApellido' => 'string',
        'telefono' => 'string',

        'origenProveedor' => 'string',
        'tipoProveedor' => 'string',
        'claveProveedor' => 'string',
        'entregaMaterial' => 'string',

        'diasCredito' => 'integer',
        'terminosCondiciones' => 'string',
        
        'constanciaFiscal' => 'string',
        'opinionCumplimiento' => 'string',
        'comprobanteDomicilio' => 'string',
        'datosBancarios' => 'string',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "formulario_proveedor";    

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                "SELECT T.id, T.descripcion, T.fecha_inicio, T.fecha_limite, T.fecha_creacion,
                CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS responsable,
                CONCAT(UC.nombre, ' ', UC.apellidoPaterno, ' ', IFNULL(UC.apellidoMaterno, '')) AS creo,
                T.estatus as estatus,
                CASE
                    WHEN T.estatus = 0 THEN 'SIN EMPEZAR'
                    WHEN T.estatus = 10 THEN 'COMPLETADO'	
                    ELSE 'EN CURSO'
                END AS estatusLabel
                FROM $this->tableName  T
                INNER JOIN usuarios U ON U.id = T.fk_usuario
                INNER JOIN usuarios UC ON UC.id = T.usuarioIdCreacion ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT* FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->responsable = $respuesta["fk_usuario"];
                $this->descripcion = $respuesta["descripcion"];
                $this->fecha_inicio = fFechaLarga($respuesta["fecha_inicio"]);
                $this->fecha_limite = fFechaLarga($respuesta["fecha_limite"]);
                $this->estatus = $respuesta["estatus"];
                $this->creo = $respuesta["usuarioIdCreacion"];
                $this->requisicionId = $respuesta["idRequisicion"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

            $arrayPDOParam = array();
            $arrayPDOParam["razonSocial"] = self::$type["razonSocial"];
            $arrayPDOParam["rfc"] = self::$type["rfc"];
            $arrayPDOParam["correoElectronico"] = self::$type["correoElectronico"];
            $arrayPDOParam["nombreApellido"] = self::$type["nombreApellido"];
            $arrayPDOParam["telefono"] = self::$type["telefono"];
            $arrayPDOParam["origenProveedor"] = self::$type["origenProveedor"];
            $arrayPDOParam["tipoProveedor"] = self::$type["tipoProveedor"];
            $arrayPDOParam["claveProveedor"] = self::$type["claveProveedor"];
            $arrayPDOParam["entregaMaterial"] = self::$type["entregaMaterial"];
            $arrayPDOParam["diasCredito"] = self::$type["diasCredito"];
            $arrayPDOParam["terminosCondiciones"] = self::$type["terminosCondiciones"];

            $lastId = 0;
            $respuesta = Conexion::queryExecute(
                $this->bdName,
                "INSERT INTO $this->tableName (
                    razonSocial, rfc, correoElectronico, nombreApellido, telefono,
                    origenProveedor, tipoProveedor, claveProveedor, entregaMaterial,
                    diasCredito, terminosCondiciones
                    -- ,constanciaFiscal,
                    -- opinionCumplimiento, comprobanteDomicilio, datosBancarios
                ) VALUES (
                    :razonSocial, :rfc, :correoElectronico, :nombreApellido, :telefono,
                    :origenProveedor, :tipoProveedor, :claveProveedor, :entregaMaterial,
                    :diasCredito, :terminosCondiciones
                    -- ,:constanciaFiscal,
                    -- :opinionCumplimiento, :comprobanteDomicilio, :datosBancarios
                )",
                $datos,
                $arrayPDOParam,
                $error,
                $lastId
            );

            if($respuesta){
                
                $this->id = $lastId;

                if ( isset($datos['constanciaFiscal']) && $datos['constanciaFiscal']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['constanciaFiscal'], 1);
                
                if ( isset($datos['opinionCumplimiento']) && $datos['opinionCumplimiento']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['opinionCumplimiento'], 2);

                if ( isset($datos['comprobanteDomicilio']) && $datos['comprobanteDomicilio']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['comprobanteDomicilio'], 3);

                if ( isset($datos['datosBancarios']) && $datos['datosBancarios']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['datosBancarios'], 4);
            }

            return $respuesta;

    }

    public function actualizar($datos) {
        
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        
        if(isset($datos["descripcion"])) $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        if(isset($datos["fk_usuario"])) $arrayPDOParam["fk_usuario"] = self::$type["fk_usuario"];
        if(isset($datos["fecha_inicio"])) {
            $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
            $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        }
        if(isset($datos["fecha_limite"])) {
            $arrayPDOParam["fecha_limite"] = self::$type["fecha_limite"];
            $datos["fecha_limite"] = fFechaSQL($datos["fecha_limite"]);

        } 
        
        if(isset($datos["estatus"])){
            $arrayPDOParam["estatus"] = self::$type["estatus"];
            if($datos["estatus"]==10){
                $fecha_actual = date("Y-m-d H:i:s");
                $datos["fecha_finalizacion"] = $fecha_actual;
                $arrayPDOParam["fecha_finalizacion"] = self::$type["fecha_finalizacion"];
            }
        }
        
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);


        if ( $respuesta ) {
            $arrayPDOParam["estatus"] = self::$type["estatus"];
        }

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

    function insertarArchivos($archivos, $tipoArchivo, $dir="") {

        if (!is_array($archivos['name'])) {
            // Convertir a arreglo para evitar el error
            $archivos = [
                'name' => [$archivos['name']],
                'type' => [$archivos['type']],
                'tmp_name' => [$archivos['tmp_name']],
                'error' => [$archivos['error']],
                'size' => [$archivos['size']]
            ];
        }

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                if ( $tipoArchivo == 1 ) $directorio =  "vistas/uploaded-files/formulario-proveedor/constancia-fiscal/";
                elseif ( $tipoArchivo == 2 ) $directorio = "vistas/uploaded-files/formulario-proveedor/opinion-cumplimiento/";
                elseif ( $tipoArchivo == 3 ) $directorio = "vistas/uploaded-files/formulario-proveedor/comprobante-domicilio/";
                elseif ( $tipoArchivo == 4 ) $directorio = "vistas/uploaded-files/formulario-proveedor/datos-bancarios/";

                else $directorio = "vistas/uploaded-files/formulario-proveedor/";
                // $aleatorio = mt_rand(10000000,99999999);

                $extension = '';
                if (!is_dir($dir.$directorio)) {
                    // Crear el directorio si no existe
                    mkdir($dir.$directorio, 0777, true);
                }
                
                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";
                elseif ( $archivos["type"][$i] == "text/xml" ) $extension = ".xml";
                elseif ( $archivos["type"][$i] == "image/jpg" ) $extension = ".jpg";
                elseif ( $archivos["type"][$i] == "image/png" ) $extension = ".jpg";
                elseif ( $archivos["type"][$i] == "image/jpeg" ) $extension = ".jpg";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["formularioProveedorId"] = $this->id;
            $insertar["tipo"] = $tipoArchivo; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;

            $arrayPDOParam = array();        
            $arrayPDOParam["formularioProveedorId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO formulario_proveedor_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $dir.$ruta);
            }

        }

        return $respuesta;
    }

    public function consultarConstanciaFiscal() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM formulario_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 1 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->constancia_fiscal = $resultado;
    }

    public function consultarOpinionCumplimiento() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM formulario_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 1 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->opinion_cumplimiento = $resultado;
    }

    public function consultarComprobanteDomicilio() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM formulario_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 3 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->comprobante_domicilio = $resultado;
    }

    public function consultarDatosBancarios() {
        $resultado = Conexion::queryAll($this->bdName, "SELECT FPA.* FROM formulario_proveedor_archivos FPA WHERE FPA.formularioProveedorId = $this->id AND FPA.tipo = 4 AND FPA.eliminado = 1 ORDER BY FPA.id", $error);
        
        $this->datos_bancarios = $resultado;
    }

}
