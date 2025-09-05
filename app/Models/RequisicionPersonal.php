<?php

namespace App\Models;

if ( file_exists ( "app/Policies/RequisicionPersonalPolicy.php" ) ) {
    require_once "app/Policies/RequisicionPersonalPolicy.php";
} else {
    require_once "../Policies/RequisicionPersonalPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\RequisicionPersonalPolicy;

class RequisicionPersonal extends RequisicionPersonalPolicy
{
    static protected $fillable = [
        'id', 'folio', 'fk_obraDetalleId', 'cantidad', 'salario_semanal', 'fecha_inicio', 'fecha_fin', 'otros', 'viaticos', 'contrato', 'orden_trabajo', 'jefe_inmediato', 'cargo', 'area', 'departamento', 'categoria', 'trabajadores', 'razon', 'origen', 'fecha_cubrir', 'edad_init', 'edad_end', 'especialidad', 'posgrado', 'licenciatura', 'carrera', 'otros_estudios', 'dedicacion', 'horario', 'funciones', 'observacion'
    ];

    static protected $type = [
        'id' => 'integer',
        'folio' => 'integer',
        'fk_obraDetalleId' => 'integer',
        'cantidad' => 'integer',
        'salario_semanal' => 'float',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'otros' => 'float',
        'viaticos' => 'float',
        'contrato' => 'integer',
        'orden_trabajo' => 'integer',
        'jefe_inmediato' => 'string',
        'cargo' => 'string',
        'area' => 'string',
        'departamento' => 'string',
        'categoria' => 'integer',
        'trabajadores' => 'string',
        'razon' => 'integer',
        'origen' => 'integer',
        'fecha_cubrir' => 'date',
        'edad_init' => 'integer',
        'edad_end' => 'integer',
        'especialidad' => 'string',
        'posgrado' => 'string',
        'licenciatura' => 'string',
        'carrera' => 'string',
        'otros_estudios' => 'string',
        'puesto' => 'string',
        'dedicacion' => 'integer',
        'horario' => 'integer',
        'funciones' => 'string',
        'usuarioIdCreacion' => 'integer',
        'observacion' => 'string',
        'usuarioIdAutorizacion' => 'integer',
        'usuarioIdAuthRH' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "requisiciones_personal";

    protected $keyName = "id";

    public $id = null;    
    public $folio;
    public $fk_obraDetalleId;
    public $cantidad;
    public $salario_semanal;
    public $fecha_inicio;
    public $fecha_fin;
    public $otros;
    public $viaticos;
    public $fecha_requisicion;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item=null,$valor=null){

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT RP.id, RP.cantidad, RP.folio, RP.salario_semanal, RP.fecha_inicio, RP.fecha_fin, D.descripcion AS descripcionD, 
            I.descripcion AS descripcionI, O.descripcion AS descripcionObra, RP.usuarioIdAutorizacion, RP.usuarioIdAuthRH
            FROM requisiciones_personal RP
            INNER JOIN obra_detalles OD ON OD.id = RP.fk_obraDetalleId
            INNER JOIN obras O ON O.id = OD.obraId
            LEFT JOIN insumos D ON D.id = OD.insumoId
            LEFT JOIN indirectos I ON I.id = OD.indirectoId", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = $valor", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->fk_obraDetalleId = $respuesta["fk_obraDetalleId"];
                $this->cantidad = $respuesta["cantidad"];
                $this->salario_semanal = $respuesta["salario_semanal"];
                $this->fecha_inicio = $respuesta["fecha_inicio"];
                $this->fecha_fin = $respuesta["fecha_fin"];
                $this->otros = $respuesta["otros"];
                $this->viaticos = $respuesta["viaticos"];
                $this->folio = $respuesta["folio"];
                $this->fecha_requisicion = $respuesta["fecha_requisicion"];
                $this->contrato = $respuesta["contrato"];
                $this->orden_trabajo = $respuesta["orden_trabajo"];
                $this->jefe_inmediato = $respuesta["jefe_inmediato"];
                $this->cargo = $respuesta["cargo"];
                $this->area = $respuesta["area"];
                $this->departamento = $respuesta["departamento"];
                $this->categoria = $respuesta["categoria"];
                $this->trabajadores = $respuesta["trabajadores"];
                $this->razon = $respuesta["razon"];
                $this->origen = $respuesta["origen"];
                $this->fecha_cubrir = $respuesta["fecha_cubrir"];
                $this->edad_init = $respuesta["edad_init"];
                $this->edad_end = $respuesta["edad_end"];
                $this->especialidad = $respuesta["especialidad"];
                $this->postgrado = $respuesta["posgrado"];
                $this->licenciatura = $respuesta["licenciatura"];
                $this->carrera = $respuesta["carrera"];
                $this->otros_estudios = $respuesta["otros_estudios"];
                $this->dedicacion = $respuesta["dedicacion"];
                $this->horario = $respuesta["horario"];
                $this->funciones = $respuesta["funciones"];
                $this->observacion = $respuesta["observacion"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdAutorizacion = $respuesta["usuarioIdAutorizacion"];
                $this->usuarioIdAuthRH = $respuesta["usuarioIdAuthRH"];


            }

            return $respuesta;

        }
    }

    public function consultarPuesto(){
        $respuesta = Conexion::queryUnique($this->bdName,
                                        "SELECT I.descripcion AS descripcionI, D.descripcion AS descripcionD
                                        FROM obras O
                                        INNER JOIN obra_detalles OD ON OD.obraId = O.id
                                        LEFT JOIN insumos D ON D.id = OD.insumoId
                                        LEFT JOIN indirectos I ON I.id = OD.indirectoId
                                        WHERE OD.id = $this->fk_obraDetalleId");
        $descripcion = is_null($respuesta["descripcionI"]) ? $respuesta["descripcionD"] : $respuesta["descripcionI"] ;

        return $descripcion;
    }

    public function consultarPorObra($obra){
        return Conexion::queryAll($this->bdName,"SELECT RP.salario_semanal,RP.trabajadores, RP.fk_obraDetalleId, IF(D.id is not null,D.descripcion,I.descripcion) as descripcion
                                                FROM $this->tableName RP
                                                INNER JOIN obra_detalles OD ON OD.id = RP.fk_obraDetalleId
                                                LEFT JOIN insumos D on D.id = OD.insumoId
                                                LEFT JOIN indirectos I on I.id = OD.indirectoId
                                                INNER JOIN obras O ON O.id = OD.obraId
                                                WHERE O.id =$obra",$error);
    }

    public function crear($datos){
        $arrayPDOParam = array();        
        $arrayPDOParam["fk_obraDetalleId"] = self::$type["fk_obraDetalleId"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["salario_semanal"] = self::$type["salario_semanal"];
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        // $arrayPDOParam["otros"] = self::$type["otros"];
        // $arrayPDOParam["viaticos"] = self::$type["viaticos"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $requisicionId = 0;

        $respuesta = Conexion::queryExecute($this->bdName,"INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $requisicionId);
        
        if ( $respuesta ) $this->id = $requisicionId;

        return $respuesta;
    }

    public function actualizar($datos){
        $arrayPDOParam = array();        
        $arrayPDOParam["salario_semanal"] = self::$type["salario_semanal"];
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        // $arrayPDOParam["otros"] = self::$type["otros"];
        // $arrayPDOParam["viaticos"] = self::$type["viaticos"];
        $arrayPDOParam["contrato"] = self::$type["contrato"];
        $arrayPDOParam["orden_trabajo"] = self::$type["orden_trabajo"];
        $arrayPDOParam["jefe_inmediato"] = self::$type["jefe_inmediato"];
        $arrayPDOParam["cargo"] = self::$type["cargo"];
        $arrayPDOParam["area"] = self::$type["area"];
        $arrayPDOParam["departamento"] = self::$type["departamento"];
        $arrayPDOParam["categoria"] = self::$type["categoria"];
        $arrayPDOParam["trabajadores"] = self::$type["trabajadores"];
        $arrayPDOParam["razon"] = self::$type["razon"];
        $arrayPDOParam["origen"] = self::$type["origen"];
        $arrayPDOParam["fecha_cubrir"] = self::$type["fecha_cubrir"];
        $arrayPDOParam["edad_init"] = self::$type["edad_init"];
        $arrayPDOParam["edad_end"] = self::$type["edad_end"];
        $arrayPDOParam["especialidad"] = self::$type["especialidad"];
        $arrayPDOParam["posgrado"] = self::$type["posgrado"];
        $arrayPDOParam["licenciatura"] = self::$type["licenciatura"];
        $arrayPDOParam["carrera"] = self::$type["carrera"];
        $arrayPDOParam["otros_estudios"] = self::$type["otros_estudios"];
        $arrayPDOParam["dedicacion"] = self::$type["dedicacion"];
        $arrayPDOParam["horario"] = self::$type["horario"];
        $arrayPDOParam["funciones"] = self::$type["funciones"];
        $arrayPDOParam["observacion"] = self::$type["observacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $datos["id"] = $this->id;
        
        
        if (!isset($datos["trabajadores"])) {
            $datos["trabajadores"] = "[]";
        }else{
            $datos["trabajadores"] = json_encode($datos["trabajadores"]);
        }

        if (isset($datos["categoria "])) {
            $datos["categoria"] = 0;
        }
        
        if (isset($datos["razon  "])) {
            $datos["razon"] = 0;
        }

        if (isset($datos["origen  "])) {
            $datos["origen"] = 0;
        }

        if (isset($datos["dedicacion  "])) {
            $datos["dedicacion"] = 0;
        }

        if (isset($datos["horario  "])) {
            $datos["horario"] = 0;
        }

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function getLastFolio($idObra){

        $respuesta = Conexion::queryUnique($this->bdName,"SELECT RP.folio FROM $this->tableName RP
                                                            INNER JOIN obra_detalles OD ON OD.id = RP.fk_obraDetalleId
                                                            INNER JOIN obras O ON O.id = OD.obraId
                                                            WHERE OD.obraId = $idObra
                                                            ORDER BY RP.folio DESC");
        $folio = 1;
        if($respuesta){
            $folio = intval($respuesta["folio"])+1;
        } 
        

        return $folio;
    }

    public function auth($id,$perfil)
    {
        $arrayPDOParam = array();
        if ($perfil == 'jefe de RH') {
            $arrayPDOParam["usuarioIdAuthRH"] = self::$type["usuarioIdAuthRH"];
            $datos["usuarioIdAuthRH"] = usuarioAutenticado()["id"];
        }else{
            $arrayPDOParam["usuarioIdAutorizacion"] = self::$type["usuarioIdAutorizacion"];
            $datos["usuarioIdAutorizacion"] = usuarioAutenticado()["id"];
        }

        $datos["id"] = $id;
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        // 
        Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
        return true;
    }

    public function getEmpleados($reqPersonalId){
        return Conexion::queryUnique($this->bdName,
            "SELECT trabajadores
            FROM requisiciones_personal
            WHERE id = $reqPersonalId
        ");
    }
}