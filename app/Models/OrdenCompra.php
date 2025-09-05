<?php

namespace App\Models;

if ( file_exists ( "app/Policies/OrdenCompraPolicy.php" ) ) {
    require_once "app/Policies/OrdenCompraPolicy.php";
} else {
    require_once "../Policies/OrdenCompraPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\OrdenCompraPolicy;

class OrdenCompra extends OrdenCompraPolicy
{
    static protected $fillable = [
        'id', 'folio','actualEstatusId', 'estatusId', 'detalles', 'proveedorId', 'requisicionId', 'condicionPagoId', 'monedaId', 'fechaRequerida', 'retencionIva', 'retencionIsr', 'descuento', 'iva', 'direccion', 'especificaciones','observacion','justificacion', 'reposicion_gastos','categoriaId','datoBancarioId', 'tiempoEntrega', 'total', 'subtotal', 'comprobanteArchivos'
    ];

    static protected $type = [
        'id' => 'integer',
        'folio' => 'integer',
        'estatusId' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'usuarioIdAutorizacion' => 'integer',
        'usuarioIdAprobacion' => 'integer',
        'monedaId' => 'integer',
        'proveedorId' => 'integer',
        'requisicionId' => 'integer',
        'condicionPagoId' => 'integer',
        'fechaRequerida' => 'date',
        'retencionIva' => 'decimal',
        'retencionIsr' => 'decimal',
        'descuento' => 'decimal',
        'iva' => 'decimal',
        'observacion' => 'string',
        'direccion' => 'string',
        'especificaciones' => 'string',
        'justificacion' => 'string',
        'reposicion_gastos' => 'integer',
        'categoriaId' => 'integer',
        'datoBancarioId' => 'integer',
        'tiempoEntrega' => 'string',
        'total' => 'decimal',
        'subtotal' => 'decimal',
        'usuarioAutorizacionAdicional' => 'integer'

    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "ordencompra";

    protected $keyName = "id";

    public $id = null;    
    public $obraDetalleId;
    public $periodo;
    public $folio;
    public $estatus;
    public $idObra;
    public $justificacion;
    public $estatusId;
    public $detalles;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR ORDENES DE COMPRA
    =============================================*/
    public function consultar($item = null, $valor = null, $divisa = 1) {

        if ( is_null($valor) ) {
            $fechaActual = date('Y-m-d', strtotime('+1 days'));
            // Calcular la fecha de dos meses
            $fechaInicio = date('Y-m-d', strtotime('-2 months'));
            return Conexion::queryAll($this->bdName, 
            "SELECT OC.*, O.nombreCorto as 'obra.nombreCorto', O.prefijo as 'prefijo', R.folio as 'requisicion.folio', R.id as 'requisicion.id',
            concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo',
            S.descripcion AS 'estatus.descripcion', S.colorTexto AS 'estatus.colorTexto', S.colorFondo AS 'estatus.colorFondo',
                            CASE    WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, '')))
                                    WHEN P.personaFisica = 0 THEN P.razonSocial
                            END AS 'proveedor',
            DBP.nombreBanco AS 'datoBancario.nombreBanco', DBP.cuentaClave AS 'datoBancario.clabe'
            FROM        $this->tableName OC
            INNER JOIN requisiciones R ON OC.requisicionId = R.id
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN  usuarios US ON R.usuarioIdCreacion = US.id
            INNER JOIN  estatus S ON OC.estatusId = S.id
            INNER JOIN proveedores P ON OC.proveedorId = P.id
            LEFT JOIN datos_bancarios_proveedores DBP ON OC.datoBancarioId = DBP.id
            WHERE       OC.estatusId <> 3
            ORDER BY    OC.fechaCreacion DESC", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName,"SELECT 
                                                                    OC.* 
                                                                FROM $this->tableName OC 
                                                                INNER JOIN requisiciones R ON OC.requisicionId = R.id
                                                                INNER JOIN  obras O ON R.fk_idObra = O.id
                                                                WHERE OC.$this->keyName = $valor
                                                                ", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName,"SELECT 
                                                                    OC.* 
                                                                FROM $this->tableName OC
                                                                INNER JOIN requisiciones R ON OC.requisicionId = R.id
                                                                INNER JOIN  obras O ON R.fk_idObra = O.id
                                                                WHERE OC.$item = '$valor'
                                                                ", $error);

            }            

            
            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->folio = $respuesta["folio"];
                $this->requisicionId = $respuesta["requisicionId"];
                $this->proveedorId = $respuesta["proveedorId"];
                $this->condicionPagoId = $respuesta["condicionPagoId"];
                $this->monedaId = $respuesta["monedaId"];
                $this->estatusId = $respuesta["estatusId"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->usuarioIdAutorizacion = $respuesta["usuarioIdAutorizacion"];
                $this->usuarioIdAprobacion = $respuesta["usuarioIdAprobacion"];
                $this->fechaCreacion = $respuesta["fechaCreacion"];
                $this->fechaActualizacion = $respuesta["fechaActualizacion"];
                $this->fechaRequerida = $respuesta["fechaRequerida"];
                $this->retencionIva = $respuesta["retencionIva"];
                $this->retencionIsr = $respuesta["retencionIsr"];
                $this->descuento = $respuesta["descuento"];
                $this->iva = $respuesta["iva"];
                $this->direccion = $respuesta["direccion"];
                $this->especificaciones = $respuesta["especificaciones"];
                $this->justificacion = $respuesta["justificacion"];
                $this->reposicion_gastos = $respuesta["reposicion_gastos"];
                $this->categoriaId = $respuesta["categoriaId"];
                $this->datoBancarioId = $respuesta["datoBancarioId"];
                $this->tiempoEntrega = $respuesta["tiempoEntrega"];
                $this->total = $respuesta["total"];
                $this->subtotal = $respuesta["subtotal"];
                $this->usuarioAutorizacionAdicional = $respuesta["usuarioAutorizacionAdicional"];

                if ( file_exists ( "app/Models/Estatus.php" ) ) {
                    require_once "app/Models/Estatus.php";
                } else {
                    require_once "../Models/Estatus.php";
                }
                $estatus = New Estatus;
                $this->estatus = $estatus->consultar(null, $this->estatusId);

            }

            return $respuesta;

        }

    }

    public function consultarRoal(){
        return Conexion::queryAll($this->bdName, 
            "SELECT OC.*, O.nombreCorto as 'obra.nombreCorto', O.prefijo as 'prefijo', R.folio as 'requisicion.folio', R.id as 'requisicion.id',
            concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo',
            S.descripcion AS 'estatus.descripcion', S.colorTexto AS 'estatus.colorTexto', S.colorFondo AS 'estatus.colorFondo',
                            CASE    WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, '')))
                                    WHEN P.personaFisica = 0 THEN P.razonSocial
                            END AS 'proveedor',
            DBP.nombreBanco AS 'datoBancario.nombreBanco', DBP.cuentaClave AS 'datoBancario.clabe'
            FROM        $this->tableName OC
            INNER JOIN requisiciones R ON OC.requisicionId = R.id
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN  usuarios US ON R.usuarioIdCreacion = US.id
            INNER JOIN  estatus S ON OC.estatusId = S.id
            INNER JOIN proveedores P ON OC.proveedorId = P.id
            LEFT JOIN datos_bancarios_proveedores DBP ON OC.datoBancarioId = DBP.id
            WHERE       OC.estatusId <> 3 AND O.id = 109
            ORDER BY    OC.fechaCreacion DESC", $error);
    }

    /*=============================================
    MOSTRAR ORDENES DE COMPRA CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT OC.*, O.nombreCorto as 'obra.nombreCorto', O.prefijo as 'prefijo', R.folio as 'requisicion.folio', R.id as 'requisicion.id',
            concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo',
            S.descripcion AS 'estatus.descripcion', S.colorTexto AS 'estatus.colorTexto', S.colorFondo AS 'estatus.colorFondo',
            S.descripcion AS 'estatus.descripcion', S.colorTexto AS 'estatus.colorTexto', S.colorFondo AS 'estatus.colorFondo',
            CASE    WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, '')))
                    WHEN P.personaFisica = 0 THEN P.razonSocial
            END AS 'proveedor',
            DBP.nombreBanco AS 'datoBancario.nombreBanco', DBP.cuentaClave AS 'datoBancario.clabe'
            FROM        $this->tableName OC
            INNER JOIN requisiciones R ON OC.requisicionId = R.id
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN proveedores P ON OC.proveedorId = P.id
            INNER JOIN  usuarios US ON OC.usuarioIdCreacion = US.id
            LEFT JOIN datos_bancarios_proveedores DBP ON OC.datoBancarioId = DBP.id
            INNER JOIN  estatus S ON OC.estatusId = S.id";

        if ( count($arrayFiltros) == 0 ) {
            $query .= " WHERE       OC.estatusId <> 4";
        } else {
            $filtroEstatus = false;
            foreach ($arrayFiltros as $key => $value) {
                if ( $value['campo'] == 'OC.estatusId' ) $filtroEstatus = true;

                if ( $key == 0 ) $query .= " WHERE";
                if ( $key > 0 ) $query .= " AND";
                $query .= " {$value['campo']} {$value['operador']} {$value['valor']}";
            }
            if ( !$filtroEstatus ) $query .= " AND OC.estatusId <> 4";
        }

        $query .= " ORDER BY    OC.fechaCreacion DESC";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    public function consultarObservaciones()
    {
        $query = "SELECT    OO.*, SE.descripcion AS 'servicio_estatus.descripcion',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        ordenes_observaciones OO
                INNER JOIN  estatus SE ON OO.estatusId = SE.id
                INNER JOIN  usuarios US ON OO.usuarioIdCreacion = US.id
                WHERE       OO.ordenCompraId = {$this->id}
                ORDER BY    OO.id DESC";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $this->observaciones = $resultado;
    }

    public function consultarOrdenCompraProveedor()
    {

        $resultado = Conexion::queryAll($this->bdName, "SELECT    OC.*, O.nombreCorto as 'obra.nombreCorto', O.prefijo as 'prefijo', R.folio as 'requisicion.folio',
                    D.descripcion as 'moneda.descripcion',R.id as 'requisicion.id',
                    concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo'
            FROM        $this->tableName OC
            INNER JOIN requisiciones R ON OC.requisicionId = R.id
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN  usuarios US ON OC.usuarioIdCreacion = US.id
            INNER JOIN divisas D ON OC.monedaId = D.id
            WHERE       OC.proveedorId = {$this->id}", $error);

        return $resultado;
    }

    //TODO AGREGAR A ORDENES DE COMPRA GLOBALES PARA OBTENER
    public function consultarArchivos()
    {
        $resultado = Conexion::queryAll($this->bdName, 
            "SELECT RA.*
            FROM requisicion_archivos RA
            WHERE RA.requisicionId = $this->requisicionId AND RA.proveedorId = $this->proveedorId AND RA.eliminado = 1 AND (RA.tipo = 1 OR RA.tipo = 3)", $error);

        $this->archivos = $resultado;
    }

    public function consultarDetalles()
    {
        $resultado = Conexion::queryAll($this->bdName, 
                "SELECT OCD.*, P.concepto,
                        COALESCE(D.descripcion, I.descripcion) AS 'descripcion',
                        U.descripcion AS 'unidad',
                        COALESCE(D.codigo, I.numero) AS 'codigo'
                FROM ordencompra_detalles OCD
                INNER JOIN partidas P ON P.id = OCD.partidaId
                INNER JOIN obra_detalles OD ON OD.id = P.obraDetalleId
                LEFT JOIN insumos D ON D.id = OD.insumoId
                LEFT JOIN indirectos I ON I.id = OD.indirectoId
                LEFT JOIN unidades U ON U.id = P.unidadId
                WHERE OCD.ordenId = $this->id", $error);

        $this->detalles = $resultado;
    }

    public function consultarDetallesObra($obraId, $divisa = 1)
    {
        return Conexion::queryAll($this->bdName,
            "SELECT R.folio, par.requisicionId,
            IF($divisa = 1,od.presupuesto, od.presupuesto_dolares) as presupuesto, 
            ocd.importeUnitario as costo, ocd.cantidad, R.periodo, od.insumoId, od.indirectoId, 
            od.id as obraDetalleId, COALESCE(IT.id, DT.id) AS tipo, COALESCE(IT.descripcion, DT.descripcion) AS descripcion
            FROM ordencompra_detalles ocd
            INNER JOIN partidas par ON ocd.partidaId = par.id
            INNER JOIN obra_detalles od ON od.id = par.obraDetalleId
            INNER JOIN obras o ON o.id = od.obraId
            INNER JOIN requisiciones R ON R.id = par.requisicionId

            LEFT JOIN indirectos I ON I.id = od.indirectoId
            LEFT JOIN indirecto_tipos IT ON IT.id = I.indirectoTipoId

            LEFT JOIN insumos D ON D.id = od.insumoId
            LEFT JOIN insumo_tipos DT ON DT.id = D.insumoTipoId
            WHERE o.id  = $obraId AND R.divisa = $divisa", $error);
    }

    public function consultarCotizaciones()
    {


        $respuesta = Conexion::queryAll($this->bdName, 
            "SELECT RA.*
            FROM requisicion_archivos RA
            WHERE RA.requisicionId = $this->requisicionId and RA.proveedorId = $this->proveedorId and RA.tipo = 4 and RA.eliminado = 1", $error);

        $this->cotizaciones = $respuesta;
    }

    public function consultarLastId()
    {
        $query = "SELECT MAX(OC.folio) as 'folio' FROM $this->tableName OC";
        $respuesta = Conexion::queryUnique($this->bdName, $query, $error);
        return $respuesta;
    }

    public function crear($datos) {


        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["fechaRequerida"] = fFechaSQL($datos["fechaRequerida"]);
        $datos["retencionIva"] = str_replace(',', '', $datos["retencionIva"]);
        $datos["retencionIsr"] = str_replace(',', '', $datos["retencionIsr"]);
        $datos["descuento"] = str_replace(',', '', $datos["descuento"]);
        $datos["iva"] = str_replace(',', '', $datos["iva"]);
        $datos["subtotal"] = str_replace(',', '', $datos["subtotal"]);
        $datos["total"] = str_replace(',', '', $datos["total"]);

        $datos["reposicion_gastos"] = (isset($datos["reposicion_gastos"]) && $datos["reposicion_gastos"] === 'on') ? 1 : 0;
        if (!isset($datos["folio"]) || $datos["folio"] == "" || $datos["folio"] == 0) {
            $lastId = $this->consultarLastId();
            $datos["folio"] = isset($lastId["folio"]) ? $lastId["folio"] + 1 : 1;
        }
        // Agregar al request
        $arrayPDOParam = array();
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["condicionPagoId"] = self::$type["condicionPagoId"];
        $arrayPDOParam["monedaId"] = self::$type["monedaId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["fechaRequerida"] = self::$type["fechaRequerida"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["retencionIva"] = self::$type["retencionIva"];
        $arrayPDOParam["retencionIsr"] = self::$type["retencionIsr"];
        $arrayPDOParam["descuento"] = self::$type["descuento"];
        $arrayPDOParam["iva"] = self::$type["iva"];
        $arrayPDOParam["direccion"] = self::$type["direccion"];
        $arrayPDOParam["especificaciones"] = self::$type["especificaciones"];
        $arrayPDOParam["justificacion"] = self::$type["justificacion"];
        $arrayPDOParam["reposicion_gastos"] = self::$type["reposicion_gastos"]; 
        
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["datoBancarioId"] = self::$type["datoBancarioId"];
        $arrayPDOParam["total"] = self::$type["total"];
        $arrayPDOParam["subtotal"] = self::$type["subtotal"];


        $campos = fCreaCamposInsert($arrayPDOParam);

        $ordenId = 0;
        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $ordenId);

        if ( $respuesta ) {

            $this->id = $ordenId;
            
            $arrayDetalles = isset($datos['detalles']) ? $datos['detalles'] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles);

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
        $datos["fechaRequerida"] = fFechaSQL($datos["fechaRequerida"]);
        $datos["retencionIva"] = str_replace(',', '', $datos["retencionIva"]);
        $datos["retencionIsr"] = str_replace(',', '', $datos["retencionIsr"]);
        $datos["descuento"] = str_replace(',', '', $datos["descuento"]);
        $datos["iva"] = str_replace(',', '', $datos["iva"]);
        $datos["subtotal"] = str_replace(',', '', $datos["subtotal"]);
        $datos["total"] = str_replace(',', '', $datos["total"]);
        $datos["reposicion_gastos"] = (isset($datos["reposicion_gastos"]) && $datos["reposicion_gastos"] === 'on') ? 1 : 0;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        if ( isset($datos["estatusId"]) ){ 
            if ($datos["estatusId"] == 18 && $datos["estatusId"] !== $_POST["actualEstatusId"]) {
                $datos["usuarioIdAprobacion"] = usuarioAutenticado()["id"];
                $arrayPDOParam["usuarioIdAprobacion"] = self::$type["usuarioIdAprobacion"];
            } else if ($datos["estatusId"] == 21 && $datos["estatusId"] !== $_POST["actualEstatusId"]) {
                $datos["usuarioIdAutorizacion"] = usuarioAutenticado()["id"];
                $arrayPDOParam["usuarioIdAutorizacion"] = self::$type["usuarioIdAutorizacion"];
            }
        }

        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["condicionPagoId"] = self::$type["condicionPagoId"];
        $arrayPDOParam["monedaId"] = self::$type["monedaId"];
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["fechaRequerida"] = self::$type["fechaRequerida"];
        $arrayPDOParam["retencionIva"] = self::$type["retencionIva"];
        $arrayPDOParam["retencionIsr"] = self::$type["retencionIsr"];
        $arrayPDOParam["descuento"] = self::$type["descuento"];
        $arrayPDOParam["iva"] = self::$type["iva"];
        $arrayPDOParam["direccion"] = self::$type["direccion"];
        $arrayPDOParam["especificaciones"] = self::$type["especificaciones"];
        $arrayPDOParam["justificacion"] = self::$type["justificacion"];
        $arrayPDOParam["reposicion_gastos"] = self::$type["reposicion_gastos"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["tiempoEntrega"] = self::$type["tiempoEntrega"];
        $arrayPDOParam["datoBancarioId"] = self::$type["datoBancarioId"];
        $arrayPDOParam["total"] = self::$type["total"];
        $arrayPDOParam["subtotal"] = self::$type["subtotal"];


            //Ingresa las nuevas observaciones
            if ( isset($datos["observacion"]) ) {
                $insertar = array();
                $insertar["ordenCompraId"] = $this->id;
                $insertar["estatusId"] = $datos["estatusId"];
                $insertar["observacion"] = $datos["observacion"];
                $insertar["usuarioIdCreacion"] = $datos["usuarioIdActualizacion"];

                $insertarPDOParam = array();
                $insertarPDOParam["ordenCompraId"] = 'string';
                $insertarPDOParam["estatusId"] = self::$type["estatusId"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO ordenes_observaciones" . $campos, $insertar, $insertarPDOParam, $error);
                
            }

        $campos = fCreaCamposUpdate($arrayPDOParam);
        // Actualizar la requisicion
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            $this->requisicionId = $datos["requisicionId"];
            $this->proveedorId = $datos["proveedorId"];
            
            $comprobantes = isset($datos['comprobanteArchivos']) ? $datos['comprobanteArchivos'] : null;
            
            if ( $comprobantes ) {
                $respuesta = $this->insertarArchivos($comprobantes);
                if ( $respuesta ) {
                    $datosREQ = array();
                    $datosREQ["estatusId"] = 4;
                    $datosREQ["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
                    $datosREQ["id"] = $this->requisicionId;

                    $arrayPDOParamREQ = array();
                    $arrayPDOParamREQ["estatusId"] = self::$type["estatusId"];
                    $arrayPDOParamREQ["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
                    $arrayPDOParamREQ["id"] = self::$type["id"];

                    $campos = fCreaCamposUpdate($arrayPDOParamREQ);
                    
                    $respuesta = Conexion::queryExecute($this->bdName, "UPDATE requisiciones SET " . $campos . " WHERE id = :id", $datosREQ, $arrayPDOParamREQ, $error);

                    $insertar = array();
                    $insertar["requisicionId"] = $this->requisicionId;
                    $insertar["estatusId"] = 4;
                    $insertar["observacion"] = isset($datos["observacion"]) && $datos["observacion"] !== "" ? $datos["observacion"] : "PAGADO";
                    $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

                    $insertarPDOParam = array();
                    $insertarPDOParam["requisicionId"] = self::$type["id"];
                    $insertarPDOParam["estatusId"] = self::$type["estatusId"];
                    $insertarPDOParam["observacion"] = self::$type["observacion"];
                    $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                    $campos = fCreaCamposInsert($insertarPDOParam);

                    $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_observaciones " . $campos, $insertar, $insertarPDOParam, $error);

                }
            }

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

    function insertarDetalles(array $arrayDetalles = null) {

        $respuesta = false;
    
        if ( $arrayDetalles ) {

            $insertarPDOParam = array();
            $insertarPDOParam["ordenId"] = self::$type[$this->keyName];
            $insertarPDOParam["partidaId"] = "integer";
            $insertarPDOParam["cantidad"] = "decimal";
            $insertarPDOParam["importeUnitario"] = "decimal";

            for ($i = 0; $i < count($arrayDetalles["cantidad"]); $i++) { 

                $insertar = array();
                $insertar["ordenId"] = $this->id;
                $insertar["cantidad"] = $arrayDetalles["cantidad"][$i];
                $insertar["partidaId"] = $arrayDetalles["partidaId"][$i];
                $insertar["importeUnitario"] = str_replace(',', '', $arrayDetalles["importeUnitario"][$i]);

                // Quitar las comas de los campos decimal
                $insertar["cantidad"] = str_replace(',', '', $insertar["cantidad"]);

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO ordencompra_detalles ".$campos, $insertar, $insertarPDOParam, $error);

            }
            
        }

        return $respuesta;

    }

    function insertarArchivos($archivos, $dir="") {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio =  "vistas/uploaded-files/requisiciones/comprobantes-pago/";
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
            $insertar["requisicionId"] = $this->requisicionId;
            $insertar["tipo"] = 1; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];
            $insertar["proveedorId"] = $this->proveedorId;

            $arrayPDOParam = array();        
            $arrayPDOParam["requisicionId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
            $arrayPDOParam["proveedorId"] = "integer";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $dir.$ruta);
            }

        }

        return $respuesta;

    }

    function insertarFacturas($archivos,$dir="") {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/requisiciones/facturas/";

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
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["requisicionId"] = $this->requisicionId;
            $insertar["tipo"] = 3; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = 1;
            $insertar["proveedorId"] = usuarioAutenticadoProveedor()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["requisicionId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
            $arrayPDOParam["proveedorId"] = "integer";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $dir.$ruta);
            }

        }

        return $respuesta;

    }

    function autorizarAdicional()
    {
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["usuarioAutorizacionAdicional"] = \usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["usuarioAutorizacionAdicional"] = self::$type["usuarioAutorizacionAdicional"];

        $campos = fCreaCamposUpdate($arrayPDOParam);
            
        return Conexion::queryExecute($this->bdName, "UPDATE ordencompra SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
