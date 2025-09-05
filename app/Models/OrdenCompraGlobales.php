<?php

namespace App\Models;

if ( file_exists ( "app/Policies/OrdenCompraGlobalesPolicy.php" ) ) {
    require_once "app/Policies/OrdenCompraGlobalesPolicy.php";
} else {
    require_once "../Policies/OrdenCompraGlobalesPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\OrdenCompraGlobalesPolicy;

class OrdenCompraGlobales extends OrdenCompraGlobalesPolicy
{
    static protected $fillable = [
        'id', 'folio','actualEstatusId', 'estatusId', 'detalles', 'proveedorId', 'requisicionId', 'condicionPagoId', 'monedaId', 'fechaRequerida', 'retencionIva', 'retencionIsr', 'descuento', 'iva', 'direccion', 'especificaciones','observacion','justificacion', 'reposicion_gastos','categoriaId','datoBancarioId', 'tiempoEntrega','detalles','tipoRequisicion', 'total', 'subtotal','requisicionIds','comprobanteArchivos'
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
        'requisicionId' => 'string',
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
        'tipoRequisicion' => 'integer',
        'total' => 'decimal',
        'subtotal' => 'decimal',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "ordencompraglobal";

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
            "SELECT 
                OCG.*,
                GROUP_CONCAT(DISTINCT RQ.folio ORDER BY RQ.folio) AS REQUISICIONES,
                GROUP_CONCAT(DISTINCT RQ.id ORDER BY RQ.id) AS REQUISICIONES_ID,
                GROUP_CONCAT(DISTINCT O.nombreCorto ORDER BY O.nombreCorto) AS OBRAS,
                GROUP_CONCAT(DISTINCT O.prefijo ORDER BY O.prefijo) AS PREFIJO_OBRA,
                GROUP_CONCAT(DISTINCT E.nombreCorto ORDER BY E.nombreCorto) AS EMPRESAS,
                concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo', 
                S.descripcion AS 'estatus.descripcion', 
                S.colorTexto AS 'estatus.colorTexto', 
                S.colorFondo AS 'estatus.colorFondo',
                CASE    WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, '')))
            	WHEN P.personaFisica = 0 THEN P.razonSocial
    			END AS 'proveedor',
                DBP.nombreBanco AS 'datoBancario.nombreBanco', 
                DBP.cuentaClave AS 'datoBancario.clabe'
            FROM 
                ordencompraglobal_requisicion OCGR
            LEFT JOIN 
                ordencompraglobal OCG ON OCGR.idOrdenCompra = OCG.id
            INNER JOIN 
             	proveedores P ON OCG.proveedorId = P.id
            LEFT JOIN 
                requisiciones RQ ON RQ.id = OCGR.idRequisicion
            LEFT JOIN 
                obras O ON RQ.fk_idObra = O.id
            LEFT JOIN 
                empresas E ON O.empresaId = E.id
            LEFT JOIN 
                usuarios US ON OCG.usuarioIdCreacion = US.id 
            LEFT JOIN 
                estatus S ON OCG.estatusId = S.id 
            LEFT JOIN 
                datos_bancarios_proveedores DBP ON OCG.datoBancarioId = DBP.id
            GROUP BY 
                OCGR.idOrdenCompra", $error);


        } else {

            if ( is_null($item) ) {


                $respuesta = Conexion::queryUnique($this->bdName,"SELECT 
                                                                    OCG.*,
                                                                    GROUP_CONCAT(DISTINCT OCGR.idRequisicion ORDER BY OCGR.idRequisicion) AS REQUISICIONES,
                                                                    GROUP_CONCAT(DISTINCT O.almacen ORDER BY O.almacen) AS almacen_obra,
                                                                    GROUP_CONCAT(DISTINCT O.descripcion ORDER BY O.descripcion) AS descripcion_obra,
                                                                    GROUP_CONCAT(DISTINCT E.id ORDER BY E.id) AS empresaId,
                                                                    concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo', 
                                                                    S.descripcion AS 'estatus.descripcion', 
                                                                    S.colorTexto AS 'estatus.colorTexto', 
                                                                    S.colorFondo AS 'estatus.colorFondo' 
                                                                FROM 
                                                                    ordencompraglobal_requisicion OCGR
                                                                LEFT JOIN 
                                                                    ordencompraglobal OCG ON OCGR.idOrdenCompra = OCG.id
                                                                LEFT JOIN 
                                                                    requisiciones RQ ON RQ.id = OCGR.idRequisicion
                                                                LEFT JOIN 
                                                                    obras O ON RQ.fk_idObra = O.id
                                                                LEFT JOIN 
                                                                    empresas E ON O.empresaId = E.id
                                                                LEFT JOIN 
                                                                    usuarios US ON OCG.usuarioIdCreacion = US.id 
                                                                LEFT JOIN 
                                                                    estatus S ON OCG.estatusId = S.id 
                                                                WHERE 
                                                                    OCGR.idOrdenCompra = $valor
                                                                GROUP BY 
                                                                    OCGR.idOrdenCompra", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName,"SELECT 
                                                                    OC.* 
                                                                FROM $this->tableName OC    
                                                                LEFT  JOIN ordencompraglobal_requisicion OCR on OCR.idOrdenCompra = OC.id
                                                                LEFT JOIN requisiciones R ON OCR.idRequisicion = R.id
                                                                LEFT JOIN  obras O ON R.fk_idObra = O.id
                                                                WHERE OC.$item = '$valor'
                                                                ", $error);

            }            

            
            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->folio = $respuesta["folio"];
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
                $this->tipoRequisicion = $respuesta["tipoRequisicion"];

                $this->REQUISICIONES = $respuesta["REQUISICIONES"];

                $this->empresaId = $respuesta["empresaId"];
                $this->almacen_obra = $respuesta["almacen_obra"];
                $this->descripcion_obra = $respuesta["descripcion_obra"];

                $this->total = $respuesta["total"];
                $this->subtotal = $respuesta["subtotal"];
                $this->retencionIva = $respuesta["retencionIva"];
                $this->retencionIsr = $respuesta["retencionIsr"];
                $this->iva = $respuesta["iva"];
                $this->descuento = $respuesta["descuento"];

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

    /*=============================================
    MOSTRAR ORDENES DE COMPRA CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT OC.*, O.nombreCorto as 'obra.nombreCorto', O.prefijo as 'prefijo', R.folio as 'requisicion.folio',
            concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo',
            S.descripcion AS 'estatus.descripcion', S.colorTexto AS 'estatus.colorTexto', S.colorFondo AS 'estatus.colorFondo'
            FROM        $this->tableName OC
            INNER JOIN requisiciones R ON OC.requisicionId = R.id
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN  usuarios US ON OC.usuarioIdCreacion = US.id
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
            if ( !$filtroEstatus ) $query .= " AND R.estatusId <> 4";
        }

        $query .= " ORDER BY    OC.fechaCreacion DESC";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    public function consultarObservaciones()
    {
        $query = "SELECT    OO.*, SE.descripcion AS 'servicio_estatus.descripcion',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        ordenesglobales_observaciones OO
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
                    D.descripcion as 'moneda.descripcion',
                    concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo',
                    (SELECT SUM(OCD.importeUnitario * OCD.cantidad) FROM ordencompra_detalles OCD WHERE OCD.ordenId = OC.id) as 'subtotal'
            FROM        $this->tableName OC
            INNER JOIN requisiciones R ON OC.requisicionId = R.id
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN  usuarios US ON OC.usuarioIdCreacion = US.id
            INNER JOIN divisas D ON OC.monedaId = D.id
            WHERE       OC.proveedorId = {$this->id}", $error);

        return $resultado;
    }

    public function consultarDetalles()
    {
        $resultado = Conexion::queryAll($this->bdName, 
                "SELECT OCD.*, P.concepto,
                        COALESCE(D.descripcion, I.descripcion) AS 'descripcion',
                        COALESCE(U.descripcion, UI.descripcion) AS 'unidad',
                        COALESCE(D.codigo, I.numero) AS 'codigo'
                FROM ordencompraglobal_detalles OCD
                INNER JOIN partidas P ON P.id = OCD.partidaId
                INNER JOIN obra_detalles OD ON OD.id = P.obraDetalleId
                LEFT JOIN insumos D ON D.id = OD.insumoId
                LEFT JOIN indirectos I ON I.id = OD.indirectoId
                LEFT JOIN unidades U ON U.id = D.unidadId
                LEFT JOIN unidades UI ON UI.id = I.unidadId
                WHERE OCD.ordenId = $this->id", $error);

        $this->detalles = $resultado;
    }

    public function consultarRequisicionesPorOrdenCompra()
    {
        $resultado = Conexion::queryAll($this->bdName, 
                "SELECT OCGR.*
                FROM ordencompraglobal_requisicion OCGR
                WHERE OCGR.idOrdenCompra = $this->id", $error);

        $this->requisiciones = $resultado;
    }

    public function consultarLastId()
    {
        $query = "SELECT MAX(OC.folio) as 'folio' FROM $this->tableName OC";
        $respuesta = Conexion::queryUnique($this->bdName, $query, $error);
        return $respuesta;
    }

    public function consultarCotizaciones()
    {

        $respuesta = Conexion::queryAll($this->bdName, 
            "SELECT RA.*
            FROM requisicion_archivos RA
            WHERE RA.requisicionId = $this->requisicionId and RA.proveedorId = $this->proveedorId and RA.tipo = 4 and RA.eliminado = 1", $error);

        $this->cotizaciones = $respuesta;
    }

    public function crear($datos) {


        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["fechaRequerida"] = fFechaSQL($datos["fechaRequerida"]);

        $datos["reposicion_gastos"] = (isset($datos["reposicion_gastos"]) && $datos["reposicion_gastos"] === 'on') ? 1 : 0;
        if (!isset($datos["folio"]) || $datos["folio"] == "" || $datos["folio"] == 0) {
            $lastId = $this->consultarLastId();
            $datos["folio"] = isset($lastId["folio"]) ? $lastId["folio"] + 1 : 1;
        }
        // Agregar al request
        $arrayPDOParam = array();
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
        $arrayPDOParam["tiempoEntrega"] = self::$type["tiempoEntrega"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["datoBancarioId"] = self::$type["datoBancarioId"];
        $arrayPDOParam["tipoRequisicion"] = self::$type["tipoRequisicion"];
        $arrayPDOParam["total"] = self::$type["total"];
        $arrayPDOParam["subtotal"] = self::$type["subtotal"];

        $datos["total"] =  str_replace(',', '', $datos["total"]);
        $datos["subtotal"] =  str_replace(',', '', $datos["subtotal"]) ;
        $datos["retencionIva"] = str_replace(',', '', $datos["retencionIva"]);
        $datos["retencionIsr"] = str_replace(',', '', $datos["retencionIsr"]);
        $datos["iva"] = str_replace(',', '', $datos["iva"]);
        $datos["descuento"] = str_replace(',', '', $datos["descuento"]);

        $campos = fCreaCamposInsert($arrayPDOParam);

        $ordenId = 0;


        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $ordenId);

        if ( $respuesta ) {

            $this->id = $ordenId;
            $tipoRequisicion = $datos["tipoRequisicion"];

            $detallesArray = json_decode($datos["detalles"], true);

            // ACTUALIZAMOS REQUISICIONES Y LAS AGREGAMOS A ORDENCOMPRACLIENTE_REQUISICION
            if (!empty($detallesArray['requisiciones']) && is_array($detallesArray['requisiciones'])) {
                $requisiciones = $detallesArray["requisiciones"];
                // Mandar solo las partidas a insertarDetalles
                if (!empty($requisiciones)) {
                    $respuesta = $this->insertarRequisiciones($requisiciones);
                    $respuesta = $this->actualizarTipoRequisicion($requisiciones,$tipoRequisicion);

                } else {
                    echo "No hay requisiciones que insertar.";
                }
            }

            // AGREGAMOS PARTIDAS A DETALLES DE ORDENES DE COMPRA
            if ($detallesArray && isset($detallesArray["partidas"])) {
                $partidas = $detallesArray["partidas"];
                // Mandar solo las partidas a insertarDetalles
                if (!empty($partidas)) {
                    $respuesta = $this->insertarDetalles($partidas);
                } else {
                    echo "No hay partidas que insertar.";
                }

            } else {
                echo "JSON inválido o sin partidas.";
            }

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
        $datos["fechaRequerida"] = fFechaSQL($datos["fechaRequerida"]);

        $datos["reposicion_gastos"] = (isset($datos["reposicion_gastos"]) && $datos["reposicion_gastos"] === 'on') ? 1 : 0;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        if ( isset($datos["estatusId"]) ){ 
            if ($datos["estatusId"] == 18 && $datos["estatusId"] !== $_POST["actualEstatusId"]) {
                $datos["usuarioIdAprobacion"] = usuarioAutenticado()["id"];
                $arrayPDOParam["usuarioIdAprobacion"] = self::$type["usuarioIdAprobacion"];
            } else if ($datos["estatusId"] == 10 && $datos["estatusId"] !== $_POST["actualEstatusId"]) {
                $datos["usuarioIdAutorizacion"] = usuarioAutenticado()["id"];
                $arrayPDOParam["usuarioIdAutorizacion"] = self::$type["usuarioIdAutorizacion"];
            }
        }

        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["condicionPagoId"] = self::$type["condicionPagoId"];
        $arrayPDOParam["monedaId"] = self::$type["monedaId"];
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
        $arrayPDOParam["datoBancarioId"] = self::$type["datoBancarioId"];
        $arrayPDOParam["tiempoEntrega"] = self::$type["tiempoEntrega"];
        $arrayPDOParam["tipoRequisicion"] = self::$type["tipoRequisicion"];
        $arrayPDOParam["total"] = self::$type["total"];
        $arrayPDOParam["subtotal"] = self::$type["subtotal"];

        $datos["descuento"] = str_replace(',', '', $datos["descuento"]);
        $datos["retencionIsr"] = str_replace(',', '', $datos["retencionIsr"]);
        $datos["retencionIva"] = str_replace(',', '', $datos["retencionIva"]);
        $datos["iva"] = str_replace(',', '', $datos["iva"]);
        $datos["total"] = str_replace(',', '', $datos["total"]);
        $datos["subtotal"] = str_replace(',', '', $datos["subtotal"]);
        
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

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO ordenesglobales_observaciones" . $campos, $insertar, $insertarPDOParam, $error);
                
            }

        $campos = fCreaCamposUpdate($arrayPDOParam);
        // Actualizar la requisicion
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
        
        if ( $respuesta ) {

            $this->requisicionId = $datos["requisicionIds"]; // "1194,1195,1193"
            $this->proveedorId = $datos["proveedorId"];
            $comprobantes = isset($datos['comprobanteArchivos']) ? $datos['comprobanteArchivos'] : null;

            if ($comprobantes) {

                // Inserta archivos una vez
                $respuesta = $this->insertarArchivos($comprobantes);

                if ($respuesta) {
                    $requisicionIds = explode(',', $this->requisicionId); // Convierte string a array
                    $usuarioId = usuarioAutenticado()["id"];
                    $observacion = isset($datos["observacion"]) && $datos["observacion"] !== "" ? $datos["observacion"] : "PAGADO";

                    foreach ($requisicionIds as $idReq) {
                        $idReq = trim($idReq); // por si vienen con espacios

                        // Actualiza estatus en requisiciones
                        $datosREQ = [
                            "estatusId" => 4,
                            "usuarioIdActualizacion" => $usuarioId,
                            "id" => $idReq
                        ];

                        $arrayPDOParamREQ = [
                            "estatusId" => self::$type["estatusId"],
                            "usuarioIdActualizacion" => self::$type["usuarioIdActualizacion"],
                            "id" => self::$type["id"]
                        ];

                        $campos = fCreaCamposUpdate($arrayPDOParamREQ);
                        Conexion::queryExecute($this->bdName, "UPDATE requisiciones SET $campos WHERE id = :id", $datosREQ, $arrayPDOParamREQ, $error);

                        // Inserta observación
                        $insertar = [
                            "requisicionId" => $idReq,
                            "estatusId" => 4,
                            "observacion" => $observacion,
                            "usuarioIdCreacion" => $usuarioId
                        ];

                        $insertarPDOParam = [
                            "requisicionId" => self::$type["id"],
                            "estatusId" => self::$type["estatusId"],
                            "observacion" => self::$type["observacion"],
                            "usuarioIdCreacion" => self::$type["usuarioIdCreacion"]
                        ];

                        $campos = fCreaCamposInsert($insertarPDOParam);
                        Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_observaciones $campos", $insertar, $insertarPDOParam, $error);
                    }
                }
            }
        }

        return $respuesta;
    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos["id"] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = "integer";

        return Conexion::queryExecute($this->bdName, "DELETE FROM ordencompraglobal WHERE id = :id", $datos, $arrayPDOParam, $error);

    } 

    function insertarDetalles(array $arrayDetalles = null) {
        $respuesta = false;

        if ($arrayDetalles) {
            $insertarPDOParam = array();
            $insertarPDOParam["ordenId"] = self::$type[$this->keyName];
            $insertarPDOParam["partidaId"] = "integer";
            $insertarPDOParam["cantidad"] = "decimal";
            $insertarPDOParam["importeUnitario"] = "decimal";

            foreach ($arrayDetalles as $detalle) {
                $partidaId = intval($detalle["partidaId"]);
                $cantidad = floatval(str_replace(',', '', $detalle["cantidad"]));
                $importe = floatval(str_replace(',', '', $detalle["valorUnitario"]));
                // Aquí puedes armar $datos e insertar:
                $datos = [
                    "ordenId" => intval($this->id), // asegúrate de tener este valor
                    "partidaId" => $partidaId,
                    "cantidad" => $cantidad,
                    "importeUnitario" => $importe,
                ];

                $respuesta= Conexion::queryExecute(
                    $this->bdName,
                    "INSERT INTO ordencompraglobal_detalles (ordenId, partidaId, cantidad, importeUnitario)
                    VALUES (:ordenId, :partidaId, :cantidad, :importeUnitario)",
                    $datos,
                    $insertarPDOParam,
                    $error
                );
            }

        }

        return $respuesta;
    }

    function insertarRequisiciones(array $arrayDetalles = null) {

        if ($arrayDetalles) {

            $insertarPDOParam = array();
            $insertarPDOParam["idOrdenCompra"] = self::$type[$this->keyName];
            $insertarPDOParam["idRequisicion"] = "integer";

            foreach ($arrayDetalles as $detalle) {
                $idRequisicion = isset($detalle["id"]) ? intval($detalle["id"]) : 0;

                // Aquí puedes armar $datos e insertar:
                $datos = [
                    "idOrdenCompra" => intval($this->id), // asegúrate de tener este valor
                    "idRequisicion" => $idRequisicion,
                ];

                Conexion::queryExecute(
                    $this->bdName,
                    "INSERT INTO ordencompraglobal_requisicion (idOrdenCompra, idRequisicion)
                    VALUES (:idOrdenCompra, :idRequisicion)",
                    $datos,
                    $insertarPDOParam,
                    $error
                );
            }

            $respuesta = true;
        }

        return $respuesta;
    }

    public function actualizarTipoRequisicion($requisicion,$tipoRequisicion) {

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["tipoRequisicion"] = "integer";

        foreach ($requisicion as $value) {

            $datos = [
                "id" => $value["id"], // asegúrate de tener este valor
                "tipoRequisicion" => $tipoRequisicion,
            ];

            $campos = fCreaCamposUpdate($arrayPDOParam);
            // Actualizar la requisicion
            $respuesta = Conexion::queryExecute($this->bdName, "UPDATE requisiciones SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        }

        return $respuesta;

    }

    public function consultarRequisicionesPorOrdenCompraDetalles($ordenCompraId) {
            
        $fechaActual = date('Y-m-d', strtotime('+1 days'));
        // Calcular la fecha de dos meses
        $fechaInicio = date('Y-m-d', strtotime('-2 months'));

        return Conexion::queryAll($this->bdName, 
            "SELECT 
                R.*, 
                E.nombreCorto AS 'empresas.nombreCorto', 
                O.empresaId AS 'empresaId', 
                O.descripcion AS 'obra', 
                O.prefijo AS 'prefijo', 
                US.nombre AS 'usuarios.nombre', 
                US.apellidoPaterno AS 'usuarios.apellidoPaterno', 
                US.apellidoMaterno AS 'usuarios.apellidoMaterno', 
                S.descripcion AS 'estatus.descripcion', 
                S.colorTexto AS 'estatus.colorTexto', 
                S.colorFondo AS 'estatus.colorFondo' 
            FROM requisiciones R
            INNER JOIN ordencompraglobal_requisicion OCG ON OCG.idRequisicion = R.id
            INNER JOIN obras O ON O.id = R.fk_idObra
            INNER JOIN empresas E ON O.empresaId = E.id
            INNER JOIN usuarios US ON R.usuarioIdCreacion = US.id
            INNER JOIN estatus S ON R.estatusId = S.id
            WHERE 
            R.estatusId <> 3
            AND OCG.idOrdenCompra = $ordenCompraId
            AND R.fechaCreacion 
            BETWEEN '$fechaInicio' AND '$fechaActual'
            GROUP BY R.id
            ORDER BY R.fechaCreacion DESC, E.id, R.folio
            ", $error);

    }

    public function consultarPartidaPorOrdenCompra($ordenCompraId = null)
    {
         return  $respuesta = Conexion::queryAll($this->bdName, "SELECT 
                                                                    OCD.*, P.concepto,
                                                                    COALESCE(D.descripcion, I.descripcion) AS 'descripcion'
                                                                FROM 
                                                                    ordencompraglobal_detalles OCD
                                                                INNER JOIN 
                                                                    partidas P ON OCD.partidaId = P.id
                                                                INNER JOIN 
                                                                    unidades U ON P.unidadId = U.id
                                                                INNER JOIN 
                                                                    obra_detalles OD ON OD.id = P.obraDetalleId
                                                                LEFT JOIN 
                                                                    insumos D ON D.id = OD.insumoId
                                                                LEFT JOIN 
                                                                    indirectos I ON I.id = OD.indirectoId
                                                                WHERE 
                                                                    OCD.ordenId =  $ordenCompraId", $error);
    }

    public function insertarArchivos($archivos, $dir = ""){

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
            $arrayPDOParam["requisicionId"] = "string";
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

    public function consultarComprobantes() {

        $sql = "
            SELECT RA.*
            FROM requisicion_archivos RA
            WHERE '$this->requisicionId' = RA.requisicionId
            AND RA.tipo = 1
            AND RA.eliminado = 1
            ORDER BY RA.id
        ";
        $resultado = Conexion::queryAll($this->bdName, $sql, $error);

        $this->comprobantesPago = $resultado;
    }

    public function consultarOrdenes() {

        $sql = "
            SELECT RA.*
            FROM requisicion_archivos RA
            WHERE '$this->requisicionId' = RA.requisicionId
            AND RA.tipo = 2
            AND RA.eliminado = 1
            ORDER BY RA.id
        ";
        $resultado = Conexion::queryAll($this->bdName, $sql, $error);

        $this->ordenesCompra = $resultado;
    }
    
    public function consultarFacturas() {

        $sql = "
            SELECT RA.*
            FROM requisicion_archivos RA
            WHERE '$this->requisicionId' = RA.requisicionId
            AND RA.tipo = 3
            AND RA.eliminado = 1
            ORDER BY RA.id
        ";
        $resultado = Conexion::queryAll($this->bdName, $sql, $error);

        $this->facturas = $resultado;
    }

    public function consultarCotizacionesOrdenesGlobales() {

        $sql = "
            SELECT RA.*
            FROM requisicion_archivos RA
            WHERE '$this->requisicionId' = RA.requisicionId
            AND RA.tipo = 4
            AND RA.eliminado = 1
            ORDER BY RA.id
        ";
        $resultado = Conexion::queryAll($this->bdName, $sql, $error);

        $this->cotizaciones = $resultado;
    }

    public function consultarSoporte(){

        $sql = "
            SELECT RA.*
            FROM requisicion_archivos RA
            WHERE '$this->requisicionId' = RA.requisicionId
            AND RA.tipo = 7
            AND RA.eliminado = 1
            ORDER BY RA.id
        ";
        $resultado = Conexion::queryAll($this->bdName, $sql, $error);

        $this->soportes = $resultado;
    }

    // CONSULTAR ORDEN DE COMPRA GLOBLA
    public function consultarDatosOrdenCompra($ordenCompraId) {    


        // Consulta las órdenes de compra globales para múltiples requisiciones
        $datosOrdenCompra = Conexion::queryAll($this->bdName, 
        "SELECT 
            OCG.*, 
            E.descripcion as 'estatus.descripcion' , 
            P.razonSocial as 'proveedor.razonSocial',
            GROUP_CONCAT(DISTINCT R.folio ORDER BY R.folio) AS REQUISICIONES_FOLIO,
            GROUP_CONCAT(DISTINCT R.id ORDER BY R.id) AS REQUISICIONES_ID,
            GROUP_CONCAT(DISTINCT O.almacen ORDER BY O.almacen) AS OBRA_ALMACEN,
            GROUP_CONCAT(DISTINCT O.descripcion ORDER BY O.descripcion) AS OBRA_DESCRIPCION,
            GROUP_CONCAT(DISTINCT EM.id ORDER BY EM.id) AS EMPRESA_ID
        FROM ordencompraglobal OCG
        INNER JOIN estatus E ON E.id = OCG.estatusId 
        INNER JOIN ordencompraglobal_requisicion OCGR ON OCG.id = OCGR.idOrdenCompra
        INNER JOIN requisiciones R ON R.id = OCGR.idRequisicion
        LEFT JOIN 
            obras O ON R.fk_idObra = O.id
        LEFT JOIN 
            empresas EM ON O.empresaId = EM.id
        INNER JOIN proveedores P ON P.id = OCG.proveedorId 
        WHERE OCG.id = $ordenCompraId", $error);
    
        $ordenes_limpios = [];
    
        foreach ($datosOrdenCompra as $orden) {
            // Limpiar las órdenes de compra
            $ordenes_limpios[] = [
                "id" => $orden["id"],
                "folio" => $orden["folio"],
                "REQUISICIONES_FOLIO" => $orden["REQUISICIONES_FOLIO"],
                "proveedorId" => $orden["proveedorId"],
                "monedaId" => $orden["monedaId"],
                "estatusId" => $orden["estatusId"],
                "condicionPagoId" => $orden["condicionPagoId"],
                "retencionIva" => $orden["retencionIva"],
                "retencionIsr" => $orden["retencionIsr"],
                "descuento" => $orden["descuento"],
                "iva" => $orden["iva"],
                "direccion" => $orden["direccion"],
                "especificaciones" => $orden["especificaciones"],
                "usuarioIdCreacion" => $orden["usuarioIdCreacion"],
                "usuarioIdActualizacion" => $orden["usuarioIdActualizacion"],
                "usuarioIdAutorizacion" => $orden["usuarioIdAutorizacion"],
                "usuarioIdAprobacion" => $orden["usuarioIdAprobacion"],
                "fechaCreacion" => $orden["fechaCreacion"],
                "fechaActualizacion" => $orden["fechaActualizacion"],
                "fechaRequerida" => $orden["fechaRequerida"],
                "datoBancarioId" => $orden["datoBancarioId"],
                "categoriaId" => $orden["categoriaId"],
                "estatus" => $orden["estatus.descripcion"],
                "proveedor" => $orden["proveedor.razonSocial"],
                "tiempoEntrega" => $orden["tiempoEntrega"],
                "reposicion_gastos" => $orden["reposicion_gastos"],
                "subtotal" => $orden["subtotal"],
                "total" => $orden["total"],
                "REQUISICIONES_ID" => $orden["REQUISICIONES_ID"],
                "OBRA_ALMACEN" => $orden["OBRA_ALMACEN"],
                "OBRA_DESCRIPCION" => $orden["OBRA_DESCRIPCION"],
                "EMPRESA_ID" => $orden["EMPRESA_ID"]
            ];
        }
    
        foreach ($ordenes_limpios as $key => $value) {
            // Inicializar el arreglo "partidas" vacío
            $ordenes_limpios[$key]['partidas'] = [];
    
            // Consultar las partidas de cada orden
            $partidas = $this->consultarPartidasOrdenesDeCompra($value["id"]);

            // Limpiar las partidas y agregarlas a la orden
            foreach ($partidas as $partida) {
                $ordenes_limpios[$key]['partidas'][] = [
                    "id" => $partida["id"],
                    "partidaId" => $partida["partidaId"],
                    "cantidad" => $partida["cantidad"],
                    "ordenId" => $partida["ordenId"],
                    "importeUnitario" => $partida["importeUnitario"],
                    "concepto" => $partida["concepto"],
                    "descripcion" => $partida["descripcion"],
                    "unidad" => $partida["unidad"],
                    "codigo" => $partida["codigo"],
                ];
            }
        }
    
        // Retornar como JSON
        return $ordenes_limpios;
    }

        public function consultarPartidasOrdenesDeCompra($id) {

        $resultado = Conexion::queryAll($this->bdName, "SELECT OCD.*, P.concepto,
                                                                COALESCE(D.descripcion, I.descripcion) AS 'descripcion',
                                                                COALESCE(U.descripcion, UI.descripcion) AS 'unidad',
                                                                COALESCE(D.codigo, I.numero) AS 'codigo'
                                                        FROM ordencompraglobal_detalles OCD
                                                        INNER JOIN partidas P ON P.id = OCD.partidaId
                                                        INNER JOIN obra_detalles OD ON OD.id = P.obraDetalleId
                                                        LEFT JOIN insumos D ON D.id = OD.insumoId
                                                        LEFT JOIN indirectos I ON I.id = OD.indirectoId
                                                        LEFT JOIN unidades U ON U.id = D.unidadId
                                                        LEFT JOIN unidades UI ON UI.id = I.unidadId
                                                        WHERE OCD.ordenId = $id", $error);
        return $resultado;
    }


}
