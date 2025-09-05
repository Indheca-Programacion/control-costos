<?php

namespace App\Models;

if ( file_exists ( "app/Policies/RequisicionPolicy.php" ) ) {
    require_once "app/Policies/RequisicionPolicy.php";
} else {
    require_once "../Policies/RequisicionPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\RequisicionPolicy;

class Requisicion extends RequisicionPolicy
{
    static protected $fillable = [
        'servicioId', 'numero', 'folio', 'servicioEstatusId', 'detalles', 'detalle_imagenes', 'comprobanteArchivos', 'ordenesArchivos', 'observacion', 'facturaArchivos', 'cotizacionArchivos', 'valeArchivos', 'partidasEliminadas', 'actualServicioEstatusId', 'servicio','tipoRequisicion','fechaRequerida'
    ];

    static protected $type = [
        'id' => 'integer',
        'servicioId' => 'integer',
        'numero' => 'integer',
        'folio' => 'string',
        'servicioEstatusId' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'usuarioIdResponsable' => 'integer',
        'usuarioIdAlmacen' => 'integer',
        'servicio' => 'integer',
        'observacion' => 'string' ,
        'tipoRequisicion' => 'integer',
        'fechaRequerida' => 'datetime'
    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "requisiciones";

    protected $keyName = "id";

    public $id = null;
    public $servicioId;
    public $numero;
    public $folio;
    public $servicioEstatusId;
    public $usuarioIdCreacion;
    public $usuarioIdActualizacion;
    public $usuarioIdResponsable;
    public $usuarioIdAlmacen;
    public $fechaCreacion;
    public $tipoRequisicion;
    public $fechaRequerida;

    public $servicio;
    public $maquinaria;
    public $ubicacion;
    public $estatus;

    public $comprobantesPago;
    public $ordenesCompra;
    public $facturas;
    public $cotizaciones;
    public $valesAlmacen;
    public $valesAlmacenDigital;
    public $ordenes_compra;

    public $detalles;
    public $observaciones;


    

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    CONSULTAR ULTIMO VALOR DEL CAMPO numero
    =============================================*/
    public function consultarLastId($servicioId) {

        $query = "SELECT    S.folio AS 'servicios.folio', MAX(R.numero) AS 'numero'
                FROM        {$this->tableName} R
                INNER JOIN  servicios S ON R.servicioId = S.id
                WHERE       R.servicioId = {$servicioId}";

        $respuesta = Conexion::queryUnique($this->bdName, $query, $error);

        return $respuesta;

    }

    public function consultarFolio($servicioId){
        $query = "SELECT    S.folio AS 'servicios.folio'
                FROM        servicios S
                WHERE       S.id = {$servicioId}";

        $respuesta = Conexion::queryUnique($this->bdName, $query, $error);

        return $respuesta;
    }
    /*=============================================
    CONSULTAR USUARIO QUE CREO LA REQUISICIÓN

    FUNCION ENCARGADA DE CONSULTAR EL USUARIO
    QUE CREO LA REQUISICIÓN MANDANDO COMO 
    PARAMETRO EL ID DEL LA REQUISICIÓN.
    =============================================*/
    public function userCreateRequisition($id) {

        return Conexion::queryAll($this->bdName, "SELECT 
                                                    R.usuarioIdCreacion AS id,
                                                    U.correo
                                                FROM `requisiciones` R
                                                INNER JOIN usuarios U ON R.usuarioIdCreacion = U.id
                                                WHERE R.id = $id", $error);
    }

    /*=============================================
    MOSTRAR REQUISICIONES CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT    distinct R.*, E.nombreCorto AS 'empresas.nombreCorto', E.id as 'empresas.id', SC.id AS 'servicio_centros.id', S.numero AS 'servicios.numero',
                            SC.descripcion AS 'servicio_centros.descripcion',
                            M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie',
                            U.descripcion AS 'ubicaciones.descripcion',
                            O.descripcion AS 'obras.descripcion',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno',
                            SE.descripcion AS 'servicio_estatus.descripcion', SE.colorTexto AS 'servicio_estatus.colorTexto', SE.colorFondo AS 'servicio_estatus.colorFondo'
                FROM        {$this->tableName} R
                INNER JOIN  servicios S ON R.servicioId = S.id
                INNER JOIN  empresas E ON S.empresaId = E.id
                INNER JOIN requisicion_detalles rd ON rd.requisicionId = R.id 
                INNER JOIN  servicio_centros SC ON S.servicioCentroId = SC.id
                INNER JOIN  maquinarias M ON S.maquinariaId = M.id
                INNER JOIN  ubicaciones U ON S.ubicacionId = U.id
                INNER JOIN  usuarios US ON R.usuarioIdCreacion = US.id
                INNER JOIN  servicio_estatus SE ON R.servicioEstatusId = SE.id
                left join   obras O ON O.id = S.obraId";

        if ( count($arrayFiltros) == 0 ) {
            $query .= " WHERE       R.servicioEstatusId <> 4";
        } else {
            $filtroEstatus = false;
            foreach ($arrayFiltros as $key => $value) {
                if ( $value['campo'] == 'R.servicioEstatusId' ) $filtroEstatus = true;

                if ( $key == 0 ) $query .= " WHERE";
                if ( $key > 0 ) $query .= " AND";
                // $query .= " {$value['campo']} = {$value['valor']}";
                $query .= " {$value['campo']} {$value['operador']} {$value['valor']}";
            }
            if ( !$filtroEstatus ) $query .= " AND R.servicioEstatusId <> 4";
        }

        $query .= " ORDER BY    R.fechaCreacion DESC, E.id, SC.id, S.numero DESC, R.numero DESC";
        // return $query;
        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR REQUISICIONES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {
            $fechaActual = date('Y-m-d', strtotime('+1 days'));
            // Calcular la fecha de dos meses
            $fechaInicio = date('Y-m-d', strtotime('-2 months'));
            $query = "SELECT    R.*, E.nombreCorto AS 'empresas.nombreCorto',
                                SC.descripcion AS 'servicio_centros.descripcion',
                                M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie',
                                U.descripcion AS 'ubicaciones.descripcion',
                                O.descripcion AS 'obras.descripcion',
                                US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno',
                                SE.descripcion AS 'servicio_estatus.descripcion', SE.colorTexto AS 'servicio_estatus.colorTexto', SE.colorFondo AS 'servicio_estatus.colorFondo'
                    FROM        {$this->tableName} R
                    INNER JOIN  servicios S ON R.servicioId = S.id
                    INNER JOIN  empresas E ON S.empresaId = E.id
                    INNER JOIN  servicio_centros SC ON S.servicioCentroId = SC.id
                    INNER JOIN  maquinarias M ON S.maquinariaId = M.id
                    INNER JOIN  ubicaciones U ON S.ubicacionId = U.id
                    INNER JOIN  usuarios US ON R.usuarioIdCreacion = US.id
                    INNER JOIN  servicio_estatus SE ON R.servicioEstatusId = SE.id
                    left join   obras O ON O.id = S.obraId
                    WHERE       R.servicioEstatusId <> 4 AND R.fechaCreacion BETWEEN '$fechaInicio' AND '$fechaActual'
                    ORDER BY    R.fechaCreacion DESC, E.id, SC.id, S.numero DESC, R.numero DESC 
                    ";
            return Conexion::queryAll($this->bdName, $query, $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
            }

            if ( $respuesta ) {

                $this->id = $respuesta["id"];
                $this->servicioId = $respuesta["servicioId"];
                $this->numero = $respuesta["numero"];
                $this->folio = $respuesta["folio"];
                $this->servicioEstatusId = $respuesta["servicioEstatusId"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->usuarioIdResponsable = $respuesta["usuarioIdResponsable"];
                $this->usuarioIdAlmacen = $respuesta["usuarioIdAlmacen"];
                $this->fechaCreacion = $respuesta["fechaCreacion"];
                $this->tipoRequisicion = $respuesta["tipoRequisicion"];
                $this->fechaRequerida = $respuesta["fechaRequerida"];


                //TODO poner para que si es ajax poner para requerir en una forma diferente
                if ( file_exists ( "app/Models/Servicio.php" ) ) {
                    require_once "app/Models/Servicio.php";
                } else {
                    require_once "../Models/Servicio.php";
                }
                
                $servicio = New Servicio;
                $this->servicio = $servicio->consultar(null, $this->servicioId);

                if ( file_exists ( "app/Models/Maquinaria.php" ) ) {
                    require_once "app/Models/Maquinaria.php";
                } else {
                    require_once "../Models/Maquinaria.php";
                }
                $maquinaria = New Maquinaria;
                $this->maquinaria = $maquinaria->consultar(null, $this->servicio['maquinariaId']);

                if ( file_exists ( "app/Models/Ubicacion.php" ) ) {
                    require_once "app/Models/Ubicacion.php";
                } else {
                    require_once "../control-mantenimiento/app/Models/Ubicacion.php";
                }
                $ubicacion = New Ubicacion;
                $this->ubicacion = $ubicacion->consultar(null, $this->servicio['ubicacionId']);

                require_once "app/Models/ServicioEstatus.php";

                $servicioEstatus = New ServicioEstatus;
                $this->estatus = $servicioEstatus->consultar(null, $this->servicioEstatusId);
            }

            return $respuesta;

        }
    }

    public function consultarUlimoFolioCC($obraId) {

        return Conexion::queryUniqueCC(CONST_BD_SECURITY_CC,
        "SELECT folio FROM $this->tableName
        WHERE fk_idObra = $obraId
        ORDER BY folio DESC");

        $respuesta = Conexion::queryUniqueCC(CONST_BD_SECURITY_CC, $query, $error);

        return $respuesta;

    }

    public function consultarObservaciones()
    {
        $query = "SELECT    RO.*, SE.descripcion AS 'servicio_estatus.descripcion',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        requisicion_observaciones RO
                INNER JOIN  servicio_estatus SE ON RO.servicioEstatusId = SE.id
                INNER JOIN  usuarios US ON RO.usuarioIdCreacion = US.id
                WHERE       RO.requisicionId = {$this->id}
                ORDER BY    RO.id DESC";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $this->observaciones = $resultado;
    }

    public function consultarDetalles()
    {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RD.*, ( SELECT COUNT(RDI.id) FROM requisicion_detalle_imagenes RDI WHERE RDI.requisicionDetalleId = RD.id ) AS cant_imagenes , IFNULL((SELECT SUM(cantidad) FROM inventario_partida WHERE partidaId = RD.id),0) AS 'cantidadEntrada' FROM requisicion_detalles RD WHERE RD.requisicionId = $this->id ORDER BY RD.id", $error);
        
        $this->detalles = $resultado;
    }

    //CONSULTAR TODAS ORDENES DE COMPRA
    public function consultarOrdenesCompra(){

        $respuesta = Conexion::queryAll($this->bdName,"SELECT OC.*, E.descripcion as 'estatus.descripcion' from ordencompra OC inner join estatus_orden_compra E on OC.estatusId = E.id
        WHERE OC.requisicionId =$this->id");

        $this->ordenes_compra = $respuesta;
    }

    //CONSULTAR SOLO ORDEN COMPRA POR ID
    public function consultarOrdenDeCompra() {

        // Obtener las órdenes de compra
        $ordenesDeCompraDatos = Conexion::queryAll($this->bdName,
        "SELECT OC.*, E.descripcion as 'estatus.descripcion' , P.razonSocial as 'proveedor.razonSocial'
        FROM ordencompra OC
        INNER JOIN estatus_orden_compra E ON E.id = OC.estatusId
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

    public function consultarImagenes($detalleId)
    {

        $query = "SELECT    RDI.*
                FROM        requisicion_detalle_imagenes RDI
                WHERE       RDI.requisicionDetalleId = {$detalleId}
                ORDER BY    RDI.id";

        return Conexion::queryAll($this->bdName, $query, $error);

    }

    public function consultarComprobantes() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 1 ORDER BY RA.id", $error);
        
        $this->comprobantesPago = $resultado;

    }

    public function consultarOrdenes() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 2 ORDER BY RA.id", $error);
        
        $this->ordenesCompra = $resultado;

    }

    public function consultarFacturas() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 3 ORDER BY RA.id", $error);
        
        $this->facturas = $resultado;

    }

    public function consultarCotizaciones() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 4 ORDER BY RA.id", $error);
        
        $this->cotizaciones = $resultado;

    }

    public function consultarVales() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 5 ORDER BY RA.id", $error);
        
        $this->valesAlmacen = $resultado;

    }

    public function consultarValesDigital() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT INV.* FROM inventarios INV WHERE INV.requisicionId = $this->id", $error);
        
        $this->valesAlmacenDigital = $resultado;

    }

    public function consultarOrdenesDeCompra() {
        // Obtener las órdenes de compra
        $ordenesDeCompraDatos = Conexion::queryAll($this->bdName,
        "SELECT OC.*, E.descripcion as 'estatus.descripcion' , P.razonSocial as 'proveedor.razonSocial'
        FROM ordencompra OC
        INNER JOIN estatus_orden_compra E ON E.id = OC.estatusId
        INNER JOIN proveedores P ON P.id = OC.proveedorId
        WHERE OC.requisicionId = $this->id", $error);

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

    public function crear($datos) {

        // Buscar el último folio según el campo servicioId
        $lastId = $this->consultarLastId($datos["servicioId"]);

        if ( $lastId === false || $lastId["servicios.folio"] == null || $lastId["numero"] == null ) {

            $lastId = $this->consultarFolio($datos["servicioId"]);
        }

        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Agregar al request para especificar numero y folio del Servicio
        $datos["numero"] = (int) ($lastId["numero"] ?? 0) + 1;
        $datos["folio"] = "{$lastId["servicios.folio"]}-r{$datos["numero"]}";
        $datos["fechaRequerida"] = fFechaSQL($datos["fechaRequerida"]);

        $arrayPDOParam = array();
        $arrayPDOParam["servicioId"] = self::$type["servicioId"];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $arrayPDOParam["tipoRequisicion"] = self::$type["tipoRequisicion"];
        $arrayPDOParam["fechaRequerida"] = self::$type["fechaRequerida"];

        if (isset($datos["servicio"])) {
            $arrayPDOParam["servicio"] = self::$type["servicio"];
        }
        
        $campos = fCreaCamposInsert($arrayPDOParam);

        $requisicionId = 0;
        $insertarPDOParam["requisicionId"] = self::$type[$this->keyName];
        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $requisicionId);

        if ( $respuesta ) {

            $this->id = $requisicionId;
            $this->folio = $datos["folio"];
            
            $arrayDetalles = isset($datos['detalles']) ? $datos['detalles'] : null;
            $arrayDetalleImagenes = isset($datos['detalle_imagenes']) ? $datos['detalle_imagenes'] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles, $arrayDetalleImagenes);

            if ( isset($datos['comprobanteArchivos']) && $datos['comprobanteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['comprobanteArchivos'], 1);
            
            if ( isset($datos['ordenesArchivos']) && $datos['ordenesArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['ordenesArchivos'], 2);

            if ( isset($datos['facturaArchivos']) && $datos['facturaArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['facturaArchivos'], 3);

            if ( isset($datos['cotizacionArchivos']) && $datos['cotizacionArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['cotizacionArchivos'], 4);

            if ( isset($datos['valeArchivos']) && $datos['valeArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['valeArchivos'], 5);

        }

        return $respuesta;

    }

    public function crearRQCostos($datos,$archivos){

        $arrayPDOParam = array();
        $arrayPDOParam["folio"] = 'integer';
        $arrayPDOParam["periodo"] = 'integer';
        $arrayPDOParam["fk_idObra"] = 'integer';
        $arrayPDOParam["usuarioIdCreacion"] = 'string';
        $arrayPDOParam["estatusId"] = 'integer';
        $arrayPDOParam["fechaRequerida"] = 'date';
        $arrayPDOParam["divisa"] = 'integer';
        $arrayPDOParam["tipoRequisicion"] = 'integer';
        $arrayPDOParam["categoriaId"] = 'string';

        $campos = fCreaCamposInsert($arrayPDOParam);

        $requisicionId = 0;
        $respuesta =  Conexion::queryExecuteCC(CONST_BD_SECURITY_CC, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $requisicionId);

        if ( $respuesta ) {

            $this->id = $requisicionId;
            $this->usuarioIdCreacion = $datos["usuarioIdCreacion"];
            
            $arrayDetalles = isset($datos['detalles']) ? $datos['detalles'] : null;
          
            if ( $arrayDetalles ) $respuesta = $this->insertarDetallesCC($arrayDetalles);

            $respuesta = $this->insertarArchivoCC($archivos);

        }


        return $respuesta;
    }

    public function obtenerUsuarioCreadaRequisicion($id){
        
            $resultado = Conexion::queryAll($this->bdName, "SELECT U.id, U.correo from requisiciones R 
            INNER JOIN usuarios U on R.usuarioIdCreacion = U.id
            WHERE R.id = '$id'", $error);

            $cleanArray = array_map(function($item) {
            return [
                "id" => $item["id"],
                "correo" => $item["correo"]
            ];
            }, $resultado);

            return $cleanArray;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // var_dump($datos);
        // var_dump($datos['detalles']);
        // var_dump($datos['detalle_imagenes']);
        // die();

        $arrayPDOParam = array();
        if ( isset($datos["servicioEstatusId"]) ) $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        
        //Si el estatus no cambia, no se hace nada
        if ( $datos['actualServicioEstatusId'] !== $datos['servicioEstatusId'] ){
            if ($datos["servicioEstatusId"] == 11) {
                $arrayPDOParam["usuarioIdResponsable"] = self::$type["usuarioIdResponsable"];
                $datos["usuarioIdResponsable"] = usuarioAutenticado()["id"];
            }
    
            if ($datos["servicioEstatusId"] == 10) {
                $arrayPDOParam["usuarioIdAlmacen"] = self::$type["usuarioIdAlmacen"];
                $datos["usuarioIdAlmacen"] = usuarioAutenticado()["id"];
            }
        }
        
        $arrayPDOParam["tipoRequisicion"] = self::$type["tipoRequisicion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            if ( isset($datos["servicioEstatusId"]) ) $this->servicioEstatusId = $datos["servicioEstatusId"]; 

            if ( isset($datos["observacion"]) ) {
                $insertar = array();
                $insertar["requisicionId"] = $this->id;
                $insertar["servicioEstatusId"] = $datos["servicioEstatusId"];
                $insertar["observacion"] = $datos["observacion"];
                $insertar["usuarioIdCreacion"] = $datos["usuarioIdActualizacion"];

                $insertarPDOParam = array();
                $insertarPDOParam["requisicionId"] = self::$type["id"];
                $insertarPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_observaciones " . $campos, $insertar, $insertarPDOParam, $error);
            }

            $arrayDetalles = isset($datos["detalles"]) ? $datos["detalles"] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles, $datos['detalle_imagenes']);

            if ( isset($datos["partidasEliminadas"]) ) $respuesta = $this->eliminarDetalles($datos["partidasEliminadas"]);

            if ( isset($datos['comprobanteArchivos']) && $datos['comprobanteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['comprobanteArchivos'], 1);
            
            if ( isset($datos['ordenesArchivos']) && $datos['ordenesArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['ordenesArchivos'], 2);

            if ( isset($datos['facturaArchivos']) && $datos['facturaArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['facturaArchivos'], 3);

            if ( isset($datos['cotizacionArchivos']) && $datos['cotizacionArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['cotizacionArchivos'], 4);

            if ( isset($datos['valeArchivos']) && $datos['valeArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['valeArchivos'], 5);

        }

        return $respuesta;

    }

    public function actualizarEstado() {
            
            $respuesta = false;
    
            $datos = array();
            $datos["servicioEstatusId"] = $this->servicioEstatusId;
            $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
    
            $arrayPDOParam = array();
            $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
            $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
            $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
    
            $campos = fCreaCamposUpdate($arrayPDOParam);

            $datos[$this->keyName] = $this->id;
    
            $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    
            if ($respuesta) {

                $insertar = array();
                $insertar["requisicionId"] = $this->id;
                $insertar["servicioEstatusId"] = $this->servicioEstatusId;
                $insertar["observacion"] = $this->servicioEstatusId == 7 ? 'Recibido' : 'Recibido Parcial';
                $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

                $insertarPDOParam = array();
                $insertarPDOParam["requisicionId"] = self::$type["id"];
                $insertarPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_observaciones " . $campos, $insertar, $insertarPDOParam, $error);
            }
            
            return $respuesta;
    }

    function insertarDetalles(array $arrayDetalles = null, $imagenes) {

        $respuesta = false;
    
        if ( $arrayDetalles ) {

            $insertarPDOParam = array();
            $insertarPDOParam["requisicionId"] = self::$type[$this->keyName];
            $insertarPDOParam["partida"] = "integer";
            $insertarPDOParam["cantidad"] = "decimal";
            $insertarPDOParam["unidad"] = "string";
            $insertarPDOParam["numeroParte"] = "string";
            $insertarPDOParam["concepto"] = "string";
            $insertarPDOParam["costo"] = "decimal";

            for ($i = 0; $i < count($arrayDetalles["cantidad"]); $i++) { 

                $insertar = array();
                $insertar["requisicionId"] = $this->id;
                // $insertar["partida"] = $arrayDetalles["partida"][$i];
                $insertar["partida"] = "";
                $insertar["cantidad"] = $arrayDetalles["cantidad"][$i];
                $insertar["unidad"] = $arrayDetalles["unidad"][$i];
                $insertar["numeroParte"] = $arrayDetalles["numeroParte"][$i];
                $insertar["concepto"] = $arrayDetalles["concepto"][$i];
                $insertar["costo"] = $arrayDetalles["costo"][$i] ?? 0;

                // Quitar las comas de los campos decimal
                $insertar["cantidad"] = str_replace(',', '', $insertar["cantidad"]);

                $requisicionDetalleId = 0;
                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_detalles (requisicionId, partida, cantidad, unidad, numeroParte, concepto, costo) VALUES (:requisicionId, :partida, :cantidad, :unidad, :numeroParte, :concepto, :costo)", $insertar, $insertarPDOParam, $error, $requisicionDetalleId);

                if ( $respuesta ) {

                    // $partida = $insertar["partida"];
                    $partida = $arrayDetalles["partida"][$i];
                    if ( isset($imagenes['name'][$partida][0]) && $imagenes['name'][$partida][0] != '' ) $respuesta = $this->insertarImagenes($imagenes, $partida, $requisicionDetalleId);

                }

            }
            
        }

        return $respuesta;

    }

    function insertarDetallesCC(array $arrayDetalles = null) {

        $respuesta = false;
    
        if ( $arrayDetalles ) {

            $insertarPDOParam = array();

            $insertarPDOParam["requisicionId"] = "integer";
            $insertarPDOParam["obraDetalleId"] = "integer";
            $insertarPDOParam["costo"] = "decimal";
            $insertarPDOParam["cantidad"] = "decimal";
            $insertarPDOParam["unidadId"] = "integer";
            $insertarPDOParam["periodo"] = "integer";
            $insertarPDOParam["concepto"] = "string";
            $insertarPDOParam["costo_unitario"] = "decimal";

            foreach ($arrayDetalles as $detalle) {
                $insertar = array();
                $insertar["requisicionId"] = $this->id;
                $insertar["obraDetalleId"] = $detalle["obraDetalleId"];
                $insertar["costo"] = $detalle["costo"];
                $insertar["cantidad"] = $detalle["cantidad"];
                $insertar["unidadId"] = $detalle["unidadId"];
                $insertar["periodo"] = $detalle["periodo"];
                $insertar["concepto"] = $detalle["concepto"];
                $insertar["costo_unitario"] = $detalle["costo_unitario"];

                // Insertamos el detalle
                $respuesta = Conexion::queryExecuteCC(
                    CONST_BD_SECURITY_CC,
                    "INSERT INTO partidas (obraDetalleId, requisicionId, cantidad, costo, periodo, concepto, unidadId, costo_unitario) VALUES (:obraDetalleId, :requisicionId, :cantidad, :costo, :periodo, :concepto, :unidadId, :costo_unitario)",
                    $insertar,
                    $insertarPDOParam,
                    $error
                );
            }
            
        }

        return $respuesta;

    }

    function insertarImagenes($archivos, $partida, $requisicionDetalleId) {

        for ($i = 0; $i < count($archivos['name'][$partida]); $i++) {

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$partida][$i] != "" ) {

                $archivo = $archivos["name"][$partida][$i];
                $tipo = $archivos["type"][$partida][$i];
                $tmp_name = $archivos["tmp_name"][$partida][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN                
                $directorio = "vistas/uploaded-files/requisiciones/detalle-imagenes/";

                do {
                    $ruta = fRandomNameImageFile($directorio, $tipo);
                } while ( file_exists($ruta) );

                // $aleatorio = mt_rand(10000000,99999999);
                // $extension = '';

                // if ( $archivos["type"][$partida][$i] == "image/jpeg" ) $extension = ".jpg";
                // elseif ( $archivos["type"][$partida][$i] == "image/png" ) $extension = ".png";
                // if ( $extension != '') {
                //     $ruta = $directorio.$aleatorio.$extension;
                // }

            }
            // Request con el nombre del archivo
            $insertar = array();
            $insertar["requisicionDetalleId"] = $requisicionDetalleId;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;

            $arrayPDOParam = array();        
            $arrayPDOParam["requisicionDetalleId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_detalle_imagenes " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                // move_uploaded_file($tmp_name, $ruta);
                fSaveImageFile($tmp_name, $tipo, $ruta);
            }

        }

        return $respuesta;

    }

    function eliminarDetalles(array $arrayDetalles = null)
    {
        $respuesta = false;

        if ( $arrayDetalles ) {

            $eliminarPDOParam = array();
            $eliminarPDOParam["id"] = "integer";
            $eliminarPDOParam["requisicionId"] = self::$type[$this->keyName];

            $eliminarImagenesPDOParam = array();
            $eliminarImagenesPDOParam["requisicionDetalleId"] = "integer";

            for ($i = 0; $i < count($arrayDetalles); $i++) {

                $detalleImagenes = $this->consultarImagenes($arrayDetalles[$i]);

                if ( $detalleImagenes ) {

                    // Agregar al request para eliminar el registro
                    $eliminarImagenes = array();
                    $eliminarImagenes['requisicionDetalleId'] = $arrayDetalles[$i];

                    $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM requisicion_detalle_imagenes WHERE requisicionDetalleId = :requisicionDetalleId", $eliminarImagenes, $eliminarImagenesPDOParam, $error);

                    if ( $respuesta ) {
                        foreach ($detalleImagenes as $key => $value) {
                            // Eliminar físicamente la imágen
                            fDeleteFile($value['ruta']);
                        }
                    }

                }

                $eliminar = array();
                $eliminar["id"] = $arrayDetalles[$i];
                $eliminar["requisicionId"] = $this->id;

                $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM requisicion_detalles WHERE id = :id AND requisicionId = :requisicionId", $eliminar, $eliminarPDOParam, $error);

            }

        }

        return $respuesta;
    }

    function insertarArchivos($archivos, $tipoArchivo) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                if ( $tipoArchivo == 1 ) $directorio = "vistas/uploaded-files/requisiciones/comprobantes-pago/";
                elseif ( $tipoArchivo == 2 ) $directorio = "vistas/uploaded-files/requisiciones/ordenes-compra/";
                elseif ( $tipoArchivo == 3 ) $directorio = "vistas/uploaded-files/requisiciones/facturas/";
                elseif ( $tipoArchivo == 4 ) $directorio = "vistas/uploaded-files/requisiciones/cotizaciones/";
                else $directorio = "vistas/uploaded-files/requisiciones/vales-almacen/";
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';

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
            $insertar["requisicionId"] = $this->id;
            $insertar["tipo"] = $tipoArchivo; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["requisicionId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        // $datos["empresaId"] = $this->empresaId;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];

        // return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id AND empresaId = :empresaId", $datos, $arrayPDOParam, $error);
        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
