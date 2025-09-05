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
        'id', 'folio','actualEstatusId', 'estatusId', 'detalles', 'proveedorId', 'requisicionId', 'condicionPagoId', 'monedaId', 'fechaRequerida', 'retencionIva', 'retencionIsr', 'descuento', 'iva', 'direccion', 'especificaciones','observacion','justificacion', 'datoBancarioId', 'total', 'subtotal','tipoRequisicion','comprobanteArchivos'
    ];

    static protected $type = [
        'id' => 'integer',
        'folio' => 'integer',
        'estatusId' => 'integer',
        'servicioEstatusId' => 'integer',
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
        'datoBancarioId' => 'integer',
        'total' => 'decimal',
        'subtotal' => 'decimal',
        'observacion' => 'string',
        'direccion' => 'string',
        'especificaciones' => 'string',
        'justificacion' => 'string',
        'tipoRequisicion' =>'integer'

    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "ordencompra";

    protected $keyName = "id";

    public $id = null;    
    public $ordenCompraId = null;
    public $obraDetalleId;
    public $periodo;
    public $folio;
    public $estatus;
    public $idObra;
    public $justificacion;
    public $estatusId;
    public $detalles;
    public $usuarioIdAutorizacion;
    public $usuarioIdAprobacion;
    public $fechaCreacion;
    public $requisicionId;
    public $proveedorId;
    public $condicionPagoId;
    public $monedaId;
    public $usuarioIdCreacion;
    public $usuarioIdActualizacion;
    public $fechaActualizacion;

    public $fechaRequerida;
    public $retencionIva;
    public $retencionIsr;
    public $iva;
    public $direccion;
    public $especificaciones;
    public $total;
    public $subtotal;
    public $datoBancarioId;
    public $observaciones;
    public $descuento;


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
                OC.*, 
                R.folio AS 'requisicion.folio', 
                CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'creo', 
                S.descripcion AS 'estatus.descripcion',
                S.colorTexto AS 'estatus.colorTexto', 
                S.colorFondo AS 'estatus.colorFondo',
                SER.folio AS 'servicio.folio'
            FROM 
                $this->tableName OC
            INNER JOIN 
                requisiciones R ON OC.requisicionId = R.id 
            INNER JOIN 
                servicios SER ON R.servicioId = SER.id
            INNER JOIN 
                usuarios US ON OC.usuarioIdCreacion = US.id 
            INNER JOIN 
                estatus_orden_compra S ON OC.estatusId = S.id 
            WHERE 
                OC.estatusId <> 3 
            ORDER BY 
                OC.fechaCreacion DESC
            ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName,"SELECT 
                                                                    OC.* 
                                                                FROM $this->tableName OC 
                                                                INNER JOIN requisiciones R ON OC.requisicionId = R.id
                                                                WHERE OC.$this->keyName = $valor
                                                                ", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName,"SELECT 
                                                                    OC.* 
                                                                FROM $this->tableName OC
                                                                INNER JOIN requisiciones R ON OC.requisicionId = R.id
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
                $this->total = $respuesta["total"];
                $this->subtotal = $respuesta["subtotal"];
                $this->datoBancarioId = $respuesta["datoBancarioId"];

                require_once "app/Models/EstatusOrdenCompra.php";
                $estatus = New EstatusOrdenCompra;
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
        $query = "SELECT OC.*, O.nombreCorto as 'obra.nombreCorto', 
            -- O.prefijo as 'prefijo',
             R.folio as 'requisicion.folio',
            concat(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL( US.apellidoMaterno, '')) as 'creo',
            S.descripcion AS 'estatus.descripcion',
            -- S.colorTexto AS 'estatus.colorTexto', 
            -- S.colorFondo AS 'estatus.colorFondo'
            FROM        $this->tableName OC
            INNER JOIN requisiciones R ON OC.requisicionId = R.id
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN  usuarios US ON OC.usuarioIdCreacion = US.id
            INNER JOIN  estatus_orden_compra S ON OC.estatusId = S.id";

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
                FROM        ordenes_observaciones OO
                INNER JOIN  estatus_orden_compra SE ON OO.estatusId = SE.id
                INNER JOIN  usuarios US ON OO.usuarioIdCreacion = US.id
                WHERE       OO.ordenCompraId = {$this->id}
                ORDER BY    OO.id DESC";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $this->observaciones = $resultado;
    }

    public function consultarOrdenCompraProveedor()
    {

        $resultado = Conexion::queryAll($this->bdName, "SELECT    OC.*, O.nombreCorto as 'obra.nombreCorto', 
                    -- O.prefijo as 'prefijo', 
                    R.folio as 'requisicion.folio',
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

    // CONSULTAR DETALLES DE LAS ORDENES DE COMPRA
    public function consultarDetalles()
    {
        
        $resultado = Conexion::queryAll($this->bdName, 
                "SELECT 
                    OCD.*,
                    RD.concepto, 
                    RD.numeroParte, 
                    RD.unidad 
                FROM ordencompra_detalles OCD 
                INNER JOIN requisicion_detalles RD on RD.id = OCD.partidaId
                WHERE OCD.ordenId = $this->id", $error);

        $this->detalles = $resultado;
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
        $datos["descuento"] = str_replace(',', '', $datos["descuento"]);
        $datos["retencionIsr"] = str_replace(',', '', $datos["retencionIsr"]);
        $datos["iva"] = str_replace(',', '', $datos["iva"]);
        $datos["subtotal"] = str_replace(',', '', $datos["subtotal"]);
        $datos["total"] = str_replace(',', '', $datos["total"]);

        if (!isset($datos["folio"]) || $datos["folio"] == "" || $datos["folio"] == 0) {
            $lastId = $this->consultarLastId();
            $datos["folio"] = isset($lastId["folio"]) ? $lastId["folio"] + 1 : 1;
        }
        // Agregar al request
        $arrayPDOParam = array();
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["datoBancarioId"] = self::$type["datoBancarioId"];
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

    public function actualizar($datos,$usuarioCS) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = $usuarioCS;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        if ( isset($datos["estatusId"]) ){ 
            if ($datos["estatusId"] == 18) {
                $datos["usuarioIdAprobacion"] = $usuarioCS;
                $arrayPDOParam["usuarioIdAprobacion"] = self::$type["usuarioIdAprobacion"];
            } else if ($datos["estatusId"] == 19) {
                $datos["usuarioIdAutorizacion"] = $usuarioCS;
                $arrayPDOParam["usuarioIdAutorizacion"] = self::$type["usuarioIdAutorizacion"];
            }
        }

        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
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
        $respuestaActualizarOrdenCompra = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuestaActualizarOrdenCompra;
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
    
    //CONSULTAR SOLO ORDEN COMPRA POR ID
    public function consultarOrdenDeCompra() {

        // Obtener las órdenes de compra
        $ordenesDeCompraDatos = Conexion::queryAll($this->bdName,
        "SELECT OC.*, E.descripcion as 'estatus.descripcion' , P.razonSocial as 'proveedor.razonSocial'
        FROM ordencompra OC
        INNER JOIN servicio_estatus E ON E.id = OC.estatusId
        INNER JOIN proveedores P ON P.id = OC.proveedorId
        WHERE OC.id = $this->ordenCompraId", $error);

        $ordenes_limpios = [];

        foreach ($ordenesDeCompraDatos as $orden) {
            // Limpiar las órdenes de compra
            $ordenes_limpios[] = [
                "id" => $orden["id"],
                "folio" => $orden["folio"],
                "requisicionId" => $orden["requisicionId"],
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
                "justificacion" => $orden["justificacion"],
                "usuarioIdCreacion" => $orden["usuarioIdCreacion"],
                "usuarioIdActualizacion" => $orden["usuarioIdActualizacion"],
                "usuarioIdAutorizacion" => $orden["usuarioIdAutorizacion"],
                "usuarioIdAprobacion" => $orden["usuarioIdAprobacion"],
                "fechaCreacion" => $orden["fechaCreacion"],
                "fechaActualizacion" => $orden["fechaActualizacion"],
                "fechaRequerida" => $orden["fechaRequerida"],
                "datoBancarioId" => $orden["datoBancarioId"],
                "estatus" => $orden["estatus.descripcion"],
                "proveedor" => $orden["proveedor.razonSocial"],
                "subtotal" => $orden["subtotal"],
                "total" => $orden["total"]
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
                    "unidad" => $partida["unidad"],
                    "codigo" => ''

                ];
            }
        }

        // Retornar como JSON
        return $ordenes_limpios;
    }

    public function consultarPartidasOrdenesDeCompra($id) {

        $resultado = Conexion::queryAll($this->bdName, "SELECT OCD.*, P.concepto, P.numeroParte, P.unidad
                                                        FROM ordencompra_detalles OCD
                                                        INNER JOIN requisicion_detalles P ON P.id = OCD.partidaId
                                                        WHERE OCD.ordenId = $id", $error);
        return $resultado;
    }

    public function listaOrdenesDeCompraPorRequisicion(){

        $listaOrdenesCompra = Conexion::queryAll($this->bdName,
                                                            "SELECT 
                                                                OC.folio,
                                                                E.descripcion
                                                            FROM ordencompra OC 
                                                            INNER JOIN servicio_estatus E ON E.id = OC.estatusId
                                                            WHERE OC.requisicionId = $this->requisicionId", $error);

        return $listaOrdenesCompra;
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

    // FUNCION PARA ACTUALIZAR EL TIPO DE REQUISICION 
    public function actualizarTipoRequisicion($datos){

        $datosRequisicion = [];
        $datosRequisicion[$this->keyName] = $datos["requisicionId"] ;  
        $datosRequisicion["tipoRequisicion"] = $datos["tipoRequisicion"] ;        

        $arrayPDOParamRequisicion[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParamRequisicion["tipoRequisicion"] = self::$type["tipoRequisicion"];

        $camposRequisicion = fCreaCamposUpdate($arrayPDOParamRequisicion);

         Conexion::queryExecute($this->bdName, "UPDATE requisiciones SET " . $camposRequisicion . " WHERE id = :id", $datosRequisicion, $arrayPDOParamRequisicion, $error);
    }

    //FUNCION PARA MARCAR PAGADA REQUISICION
    public function pagadoRequisicion(){

        $datosRequisicion = array();
        $datosRequisicion["servicioEstatusId"] = 5;
        $datosRequisicion["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
        $datosRequisicion["id"] = $this->requisicionId;

        $arrayPDOParamRequisicion = array();
        $arrayPDOParamRequisicion["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParamRequisicion["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParamRequisicion["id"] = self::$type["id"];

        $camposRequisicion = fCreaCamposUpdate($arrayPDOParamRequisicion);

        return Conexion::queryExecute($this->bdName, "UPDATE requisiciones SET " . $camposRequisicion . " WHERE id = :id", $datosRequisicion, $arrayPDOParamRequisicion, $error);
    }

    // AGREGAR OBSERVACION A LA REQUISICION DE PAGADO
    public function observacionPagadoRequisicion(){

        $datosObservaciones = array();
        $datosObservaciones["requisicionId"] = $this->requisicionId;
        $datosObservaciones["servicioEstatusId"] = 5;
        $datosObservaciones["observacion"] = "PAGADO";
        $datosObservaciones["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $observacionesPDOParam = array();
        $observacionesPDOParam["requisicionId"] = self::$type["id"];
        $observacionesPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $observacionesPDOParam["observacion"] = self::$type["observacion"];
        $observacionesPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $camposObservaciones = fCreaCamposInsert($observacionesPDOParam);

        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_observaciones " . $camposObservaciones, $datosObservaciones, $observacionesPDOParam, $error);
    }

}
