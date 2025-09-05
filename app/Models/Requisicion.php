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
        'id', 'folio', 'fk_IdObra', 'periodo', 'usuarioIdCreacion','actualEstatusId', 'unidadId', 'estatusId', 'usuarioIdActualizacion','observacion','detalles', 'detalle_imagenes', 'comprobanteArchivos', 'ordenesArchivos', 'observacion', 'facturaArchivos','soporte','cotizacionArchivos', 'valeArchivos','resguardoArchivos','proveedor', 'fax', 'email', 'telefono', 'fax', 'fechaSol', 'fechaRequerida', 'justificacion', 'costo_final','divisa', 'proveedorId', 'tipoRequisicion', 'direccion', 'especificaciones', 'iva', 'retencionIva', 'retencionIsr', 'descuento', 'categoriaId', 'presupuesto'
    ];

    static protected $type = [
        'id' => 'integer',
        'folio' => 'float',
        'fk_IdObra' => 'integer',
        'periodo' => 'integer',
        'observacion' => 'string',
        'usuarioIdCreacion' => 'integer',
        'unidadId' => 'integer',
        'estatusId' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'usuarioIdAutorizacion' => 'integer',
        'justificacion' => 'string',
        'fechaRequerida'=> 'date',
        'costo_total' => 'float',
        'divisa' => 'integer',
        'proveedorId' => 'integer',
        'usuarioIdAlmacen' => 'integer',
        'tipoRequisicion' => 'integer',
        'direccion' => 'string',
        'especificaciones' => 'string',
        'iva' => 'decimal',
        'retencionIva' => 'decimal',
        'retencionIsr' => 'decimal',
        'descuento' => 'decimal',
        'categoriaId' => 'integer',
        'usuarioIdAutorizacionAdd' => 'integer',
        'presupuesto' => 'integer',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "requisiciones";
    protected $tablePartida = "partidas";

    protected $keyName = "id";

    public $id = null;
    public $obraDetalleId;
    public $periodo;
    public $folio;
    public $estatus;
    public $idObra;
    public $justificacion;
    public $estatusId;
    public $proveedor = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE INSUMOS
    =============================================*/
    public function consultar($item = null, $valor = null, $divisa = 1) {

        // $ubicacionId = ubicacionUsuario();

        // $idUsuario = usuarioAutenticado()["id"];

        if ( is_null($valor) ) {
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
                S.colorFondo AS 'estatus.colorFondo',
                CASE WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, ''))) WHEN P.personaFisica = 0 THEN P.razonSocial END AS 'nombreProveedor',
                (
                    SELECT
                        sum( costo )
                    from partidas
                    Where requisicionId = R.id
                ) as costo
            FROM requisiciones R
            INNER JOIN obras O ON O.id = R.fk_idObra
            INNER JOIN empresas E ON O.empresaId = E.id
            INNER JOIN usuarios US ON R.usuarioIdCreacion = US.id
            INNER JOIN estatus S ON R.estatusId = S.id
            LEFT JOIN proveedores P ON R.proveedorId = P.id
            WHERE
            R.estatusId <> 3
            AND R.fechaCreacion
            BETWEEN '$fechaInicio' AND '$fechaActual'
            GROUP BY R.id
            ORDER BY R.fechaCreacion DESC, E.id, R.folio
            ", $error);
        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT R.*,
                                                                         CASE WHEN personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', apellidoPaterno, ' ', IFNULL(apellidoMaterno, ''))) WHEN personaFisica = 0 THEN razonSocial END AS 'nombreProveedor',
                                                                        P.telefono AS telefonoProveedor
                                                                    FROM $this->tableName R
                                                                    LEFT JOIN proveedores P ON R.proveedorId = P.id
                                                                    LEFT JOIN obras O ON R.fk_idObra = O.id
                                                                    WHERE R.$this->keyName = $valor
                                                                    ", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT R.*
                                                                    FROM $this->tableName R
                                                                    LEFT JOIN obras O ON R.fk_idObra = O.id
                                                                    WHERE R.$item = '$valor'
                                                                    ", $error);
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->estatus = $respuesta["estatusId"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->usuarioIdAutorizacion = $respuesta["usuarioIdAutorizacion"];
                $this->usuarioIdAlmacen = $respuesta["usuarioIdAlmacen"];
                $this->fechaCreacion = $respuesta["fechaCreacion"];
                $this->folio = $respuesta["folio"];
                $this->idObra = $respuesta["fk_idObra"];
                $this->justificacion = $respuesta["justificacion"];
                $this->fechaSol = $respuesta["fechaCreacion"];
                $this->fechaReq = $respuesta["fechaRequerida"];
                $this->especificaciones = $respuesta["especificaciones"];
                $this->direccion = $respuesta["direccion"];
                $this->tipoRequisicion = $respuesta["tipoRequisicion"];
                $this->periodo = $respuesta["periodo"];
                $this->divisa = $respuesta["divisa"];
                $this->proveedorId = $respuesta["proveedorId"];
                $this->iva = $respuesta["iva"];
                $this->retencionIva = $respuesta["retencionIva"];
                $this->retencionIsr = $respuesta["retencionIsr"];
                $this->descuento = $respuesta["descuento"];
                $this->categoriaId = $respuesta["categoriaId"];
                $this->usuarioIdAutorizacionAdd = $respuesta["usuarioIdAutorizacionAdd"];
                $this->presupuesto = $respuesta["presupuesto"];
                $this->nombreProveedor = !empty($respuesta["nombreProveedor"]) ? mb_strtoupper(fString($respuesta["nombreProveedor"])) : "SIN PROVEEDOR ASIGNADO";

                $this->telefonoProveedor = !empty($respuesta["telefonoProveedor"]) ? mb_strtoupper(fString($respuesta["telefonoProveedor"])) : "SIN PROVEEDOR ASIGNADO";


                if ( file_exists ( "app/Models/Estatus.php" ) ) {
                    require_once "app/Models/Estatus.php";
                } else {
                    require_once "../Models/Estatus.php";
                }
                $estatus = New Estatus;
                $this->estatus = $estatus->consultar(null, $this->estatus);

                if ( file_exists ( "app/Models/Obra.php" ) ) {
                    require_once "app/Models/Obra.php";
                } else {
                    require_once "../Models/Obra.php";
                }
                $obras = new Obra;

                $this->obras = $obras->consultar(null,$respuesta["fk_idObra"]);

                $this->empresaId = $this->obras["empresaId"];

                // require_once "app/Models/Empresas.php";
                // $empresas = New Empresa;
                // $this->empresa = $empresas->consultar(null,$this->obras["empresaId"]);

            }

            return $respuesta;

        }

    }

    public function consultarPorObra(){
        $idUsuario = usuarioAutenticado()["id"];

        $fechaActual = date('Y-m-d', strtotime('+1 days'));
        // Calcular la fecha de dos meses
        $fechaInicio = date('Y-m-d', strtotime('-2 months'));
        $respuesta = Conexion::queryAll($this->bdName, "SELECT
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
                S.colorFondo AS 'estatus.colorFondo',
                CASE WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, ''))) WHEN P.personaFisica = 0 THEN P.razonSocial END AS 'nombreProveedor',
                (
                    SELECT
                        sum( costo )
                    from partidas
                    Where requisicionId = R.id
                ) as costo
            FROM requisiciones R
            INNER JOIN obras O ON O.id = R.fk_idObra
            INNER JOIN empresas E ON O.empresaId = E.id
            INNER JOIN usuarios US ON R.usuarioIdCreacion = US.id
            INNER JOIN estatus S ON R.estatusId = S.id
            LEFT JOIN proveedores P ON R.proveedorId = P.id
            WHERE R.fk_idObra IN (
                SELECT idObra
                FROM puesto_usuario
                WHERE idUsuario = $idUsuario
            )
            AND R.estatusId <> 3
            AND R.fechaCreacion
            BETWEEN '$fechaInicio' AND '$fechaActual'
            GROUP BY R.id
            ORDER BY R.fechaCreacion DESC, E.id, R.folio
            ", $error);

        return $respuesta;
    }

    public function consultarRoal(){
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
                S.colorFondo AS 'estatus.colorFondo',
                CASE WHEN P.personaFisica = 1 THEN TRIM(CONCAT(P.nombre, ' ', P.apellidoPaterno, ' ', IFNULL(P.apellidoMaterno, ''))) WHEN P.personaFisica = 0 THEN P.razonSocial END AS 'nombreProveedor',
                (
                    SELECT
                        sum( costo )
                    from partidas
                    Where requisicionId = R.id
                ) as costo
            FROM requisiciones R
            INNER JOIN obras O ON O.id = R.fk_idObra
            INNER JOIN empresas E ON O.empresaId = E.id
            INNER JOIN usuarios US ON R.usuarioIdCreacion = US.id
            INNER JOIN estatus S ON R.estatusId = S.id
            LEFT JOIN proveedores P ON R.proveedorId = P.id
            WHERE
            R.estatusId <> 3 and O.id = 109
            GROUP BY R.id
            ORDER BY R.fechaCreacion DESC, E.id, R.folio
            ", $error);
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
   
        $proveedorId = $this->proveedor !== null ? "'{$this->proveedor}'" : "NULL";


        $query = "SELECT 
            distinct 
            R.*, 
            OC.proveedorId AS 'oc.proveedorId' ,
            OCG.proveedorId AS 'ocg.proveedorId' ,
            O.prefijo AS 'prefijo', 
            E.nombreCorto AS 'empresas.nombreCorto', 
            O.empresaId AS 'empresaId', 
            O.descripcion as 'obra',
            US.nombre AS 'usuarios.nombre', 
            US.apellidoPaterno AS 'usuarios.apellidoPaterno',
            US.apellidoMaterno AS 'usuarios.apellidoMaterno',
            S.descripcion AS 'estatus.descripcion', 
            S.colorTexto AS 'estatus.colorTexto', 
            S.colorFondo AS 'estatus.colorFondo',
            CASE WHEN Pro.personaFisica = 1 THEN TRIM(CONCAT(Pro.nombre, ' ', Pro.apellidoPaterno, ' ', IFNULL(Pro.apellidoMaterno, ''))) WHEN Pro.personaFisica = 0 THEN Pro.razonSocial END AS 'nombreProveedor',
                (
                    SELECT
                        sum( costo )
                    from partidas
                    Where requisicionId = R.id
                ) as costo
            FROM        $this->tableName R
            LEFT JOIN ordencompra OC ON OC.requisicionId = R.id AND OC.proveedorId = $proveedorId
            LEFT JOIN ordencompraglobal_requisicion OCGR ON OCGR.idRequisicion = R.id 
            LEFT JOIN ordencompraglobal OCG ON OCG.id = OCGR.idOrdenCompra AND OCG.proveedorId = $proveedorId
            INNER JOIN  obras O ON R.fk_idObra = O.id
            INNER JOIN  partidas P ON P.requisicionId = R.id
            LEFT JOIN proveedores Pro ON R.proveedorId = Pro.id
            INNER JOIN  empresas E ON O.empresaId = E.id
            INNER JOIN  usuarios US ON R.usuarioIdCreacion = US.id
            INNER JOIN  estatus S ON R.estatusId = S.id";

        if ( count($arrayFiltros) == 0 ) {
            $query .= " WHERE       R.estatusId <> 3";
        } else {
            $filtroEstatus = false;
            foreach ($arrayFiltros as $key => $value) {
                if ( $value['campo'] == 'R.estatusId' ) $filtroEstatus = true;

                if ( $key == 0 ) $query .= " WHERE";
                if ( $key > 0 ) $query .= " AND";
                // $query .= " {$value['campo']} = {$value['valor']}";
                $query .= " {$value['campo']} {$value['operador']} {$value['valor']}";
            }
            if ( !$filtroEstatus ) $query .= " AND R.estatusId <> 3";
        }

        $whereProveedor = '';

        if ($this->proveedor) {
            $whereProveedor = " AND (
                OC.id IS NOT NULL
                OR (
                    OC.id IS NULL
                    AND OCG.id IS NOT NULL
                )
            )";
        }
        
        $query .= $whereProveedor;
        $query .= " ORDER BY R.fechaCreacion DESC";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    public function actualizarCostoFinal($datos){

        $arrayPDOParam["costo_total"] = self::$type["costo_total"];
        $arrayPDOParam["id"] = self::$type["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);
        return $respuesta = Conexion::queryExecute($this->bdName,"UPDATE $this->tablePartida SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam);
    }

    public function consultarObservaciones()
    {
        $query = "SELECT    RO.*, SE.descripcion AS 'servicio_estatus.descripcion',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        requisicion_observaciones RO
                INNER JOIN  estatus SE ON RO.estatusId = SE.id
                INNER JOIN  usuarios US ON RO.usuarioIdCreacion = US.id
                WHERE       RO.requisicionId = {$this->id}
                ORDER BY    RO.id DESC";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $this->observaciones = $resultado;
    }

    public function consultarDetalles()
    {
        $resultado = Conexion::queryAll($this->bdName, "SELECT P.*, ( SELECT COUNT(RDI.id) FROM requisicion_detalle_imagenes RDI WHERE RDI.requisicionDetalleId = P.id ) AS cant_imagenes, U.descripcion as unidad,
        I.descripcion as descripcionI, D.descripcion, D.id as 'insumo.id', I.id as 'indirecto.id', ifnull(D.codigo, I.numero) as codigo,
        IFNULL((SELECT SUM(cantidad) FROM inventario_detalles WHERE partida = P.id),0) AS 'cantidadEntrada'
        FROM partidas P
        LEFT JOIN obra_detalles OD ON OD.id = P.obraDetalleId
        LEFT JOIN indirectos I ON I.id = OD.indirectoId
        LEFT JOIN insumos D ON D.id = OD.insumoId
        INNER JOIN unidades U ON U.id = P.unidadId
        WHERE P.requisicionId = $this->id ORDER BY P.id", $error);

        foreach($resultado as $key => $fila){
            if($fila["descripcionI"] !== null){
                $resultado[$key]["descripcion"] = $fila["descripcionI"];
            }
        }
        $this->detalles = $resultado;
    }

    // CONSULTAR DETALLES DE LA REQUISICION
    public function consultarDetallesPartidasRequisicion()
    {
        $resultado = Conexion::queryAll($this->bdName, "SELECT P.id,P.concepto,
        P.cantidad AS 'cantidadPartida',
                                                        U.descripcion as unidad,
                                                        I.descripcion as descripcionI,
                                                        D.descripcion, D.id as 'insumo.id',
                                                        I.id as 'indirecto.id',
                                                        IFNULL((SELECT SUM(cantidad) FROM inventario_detalles WHERE partida = P.id),0) AS 'cantidadInventario'
                                                        FROM partidas P
                                                        LEFT JOIN obra_detalles OD ON OD.id = P.obraDetalleId
                                                        LEFT JOIN indirectos I ON I.id = OD.indirectoId
                                                        LEFT JOIN insumos D ON D.id = OD.insumoId
                                                        INNER JOIN unidades U ON U.id = P.unidadId
                                                        WHERE P.requisicionId = $this->id ORDER BY P.id", $error);

        foreach($resultado as $key => $fila){
            if($fila["descripcionI"] !== null){
                $resultado[$key]["descripcion"] = $fila["descripcionI"];
            }
        }
        $this->detalles = $resultado;
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

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 1 AND RA.eliminado = 1 ORDER BY RA.id", $error);

        $this->comprobantesPago = $resultado;

    }

    public function consultarOrdenesDeCompra() {
        // Obtener las órdenes de compra
        $ordenesDeCompraDatos = Conexion::queryAll($this->bdName,
        "SELECT OC.*, E.descripcion as 'estatus.descripcion' , P.razonSocial as 'proveedor.razonSocial'
        FROM ordencompra OC
        INNER JOIN estatus E ON E.id = OC.estatusId
        INNER JOIN proveedores P ON P.id = OC.proveedorId
        WHERE OC.requisicionId = $this->id AND OC.estatusId <> 3", $error);

        $ordenes_limpios = [];

        foreach ($ordenesDeCompraDatos as $orden) {
            // Limpiar las órdenes de compra
            $ordenes_limpios[] = [
                "id" => $orden["id"],
                "folio" => $orden["folio"],
                "requisiciones" => $orden["requisiciones"],
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
                "categoriaId" => $orden["categoriaId"],
                "estatus" => $orden["estatus.descripcion"],
                "proveedor" => $orden["proveedor.razonSocial"],
                "tiempoEntrega" => $orden["tiempoEntrega"],
                "reposicion_gastos" => $orden["reposicion_gastos"],
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
                    "descripcion" => $partida["descripcion"],
                    "unidad" => $partida["unidad"],
                    "codigo" => $partida["codigo"],
                    "impuesto" => $partida["impuesto"],



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
                                                        FROM ordencompra_detalles OCD
                                                        INNER JOIN partidas P ON P.id = OCD.partidaId
                                                        INNER JOIN obra_detalles OD ON OD.id = P.obraDetalleId
                                                        LEFT JOIN insumos D ON D.id = OD.insumoId
                                                        LEFT JOIN indirectos I ON I.id = OD.indirectoId
                                                        LEFT JOIN unidades U ON U.id = D.unidadId
                                                        LEFT JOIN unidades UI ON UI.id = I.unidadId
                                                        WHERE OCD.ordenId = $id", $error);
        return $resultado;
    }

    public function consultarOrdenes() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 2 AND RA.eliminado = 1 ORDER BY RA.id", $error);

        $this->ordenesCompra = $resultado;

    }

    public function consultarFacturas() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 3 AND RA.eliminado = 1 ORDER BY RA.id", $error);

        $this->facturas = $resultado;

    }

    public function consultarCotizaciones() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 4 AND RA.eliminado = 1 ORDER BY RA.id", $error);

        $this->cotizaciones = $resultado;

    }

    public function consultarCotizacionesProveedor($id) {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 4 AND RA.eliminado = 1 AND RA.proveedorId = $id ORDER BY RA.id", $error);

        $this->cotizacionesProveedor = $resultado;

    }

    public function consultarVales() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 5 AND RA.eliminado = 1 ORDER BY RA.id", $error);

        $this->valesAlmacen = $resultado;

    }

    public function consultarResguardos(){

        $resultado = Conexion::queryAll($this->bdName,"SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 6 AND RA.eliminado = 1 ORDER BY RA.id",$error);

        $this->resguardos = $resultado;
    }

    public function consultarSoporte(){

        $resultado = Conexion::queryAll($this->bdName,"SELECT RA.* FROM requisicion_archivos RA WHERE RA.requisicionId = $this->id AND RA.tipo = 7 AND RA.eliminado = 1 ORDER BY RA.id",$error);

        $this->soportes = $resultado;
    }

    public function consultarPartidas($obraId, $divisa = 1, $presupuesto = 1) {
        $general = "";
        if($presupuesto > 1){
            $general = "and R.presupuesto = $presupuesto";
        }
        return Conexion::queryAll($this->bdName,
        "SELECT R.folio, par.requisicionId,IF($divisa = 1,od.presupuesto, od.presupuesto_dolares) as presupuesto, IF(par.costo_total is not null, par.costo_total,par.costo ) as costo, par.cantidad, par.periodo, od.insumoId, od.indirectoId, od.id as obraDetalleId, COALESCE(IT.id, DT.id) AS tipo, COALESCE(IT.descripcion, DT.descripcion) AS descripcion
        FROM $this->tablePartida par
        INNER JOIN obra_detalles od ON od.id = par.obraDetalleId
        INNER JOIN obras o ON o.id = od.obraId
        INNER JOIN requisiciones R ON R.id = par.requisicionId

        LEFT JOIN indirectos I ON I.id = od.indirectoId
        LEFT JOIN indirecto_tipos IT ON IT.id = I.indirectoTipoId

        LEFT JOIN insumos D ON D.id = od.insumoId
        LEFT JOIN insumo_tipos DT ON DT.id = D.insumoTipoId
        WHERE o.id  = $obraId AND R.divisa = $divisa $general", $error);
    }

    public function consultarId($obraId){
        return Conexion::queryAll($this->bdName,
        "SELECT folio FROM $this->tableName
        WHERE fk_idObra = $obraId
        ORDER BY folio DESC
        LIMIT 1");
    }

    public function consultarProveedor($requisicionId){
        return Conexion::queryAll($this->bdName,"SELECT * from proveedor_requisicion
        WHERE requisicionId = $requisicionId");
    }

    public function consultarOrdenesCompra(){

        $respuesta = Conexion::queryAll($this->bdName,"SELECT
                                                        OC.id,
                                                        OC.folio,
                                                        OC.fechaCreacion,
                                                        E.descripcion AS 'estatus.descripcion',
                                                        CASE
                                                            WHEN personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', apellidoPaterno, ' ', IFNULL(apellidoMaterno, '')))
                                                            WHEN personaFisica = 0 THEN razonSocial
                                                        END AS proveedor,
                                                        'ordencompra' AS tipo_orden
                                                        FROM requisiciones R
                                                        INNER JOIN ordencompra OC ON OC.requisicionId = R.id
                                                        INNER JOIN estatus E ON OC.estatusId = E.id
                                                        INNER JOIN proveedores P ON OC.proveedorId = P.id
                                                        WHERE R.id =  $this->id AND OC.estatusId <> 3

                                                        UNION

                                                        SELECT
                                                        OCG.id,
                                                        OCG.folio,
                                                        OCG.fechaCreacion,
                                                        E.descripcion AS 'estatus.descripcion',
                                                        CASE
                                                            WHEN personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', apellidoPaterno, ' ', IFNULL(apellidoMaterno, '')))
                                                            WHEN personaFisica = 0 THEN razonSocial
                                                        END AS proveedor,
                                                        'ordencompraglobal' AS tipo_orden
                                                        FROM ordencompraglobal OCG
                                                        INNER JOIN ordencompraglobal_requisicion OCGR ON OCGR.idOrdenCompra = OCG.id
                                                        INNER JOIN estatus E ON OCG.estatusId = E.id
                                                        INNER JOIN proveedores P ON OCG.proveedorId = P.id
                                                        WHERE OCGR.idRequisicion = $this->id AND OCG.estatusId <> 3");


        $this->ordenes_compra = $respuesta;
    }

    public function consultarCategorias()
    {
        $respuesta = Conexion::queryAll($this->bdName,
            "SELECT distinct ifnull(IT.descripcion, DT.descripcion) as categoria
            FROM partidas P
            INNER JOIN obra_detalles OD ON OD.id = P.obraDetalleId
            LEFT join indirectos I on I.id = OD.indirectoId
            LEFT join insumos D on D.id = OD.insumoId
            LEFT JOIN indirecto_tipos IT ON IT.id = I.indirectoTipoId
            LEFT JOIN insumo_tipos DT ON DT.id = D.insumoTipoId
            WHERE P.requisicionId = $this->id"
            , $error);
        $this->categorias = $respuesta;
    }

    /*=============================================
    CONSULTAR PARTIDAS DE LA REQUISICIONES

    FUNCION QUE MANDA EL ID DE LA REQUISICION
    PARA OBTENER LAS PARTIDAS DE ELLA
    =============================================*/
    public function obtenerPartidasRequisicionId($requisicionId){

        $respuesta = Conexion::queryAll($this->bdName, "SELECT
                                                                P.id,
                                                                P.cantidad,
                                                                P.concepto,
                                                                P.costo_unitario,
                                                                U.nombreCorto AS nombreUnidad
                                                                FROM partidas P
                                                                LEFT JOIN unidades U ON U.id = P.unidadId
                                                                WHERE P.requisicionId  = $requisicionId
                                                            ", $error);
        return $respuesta;
    }

    /*=============================================
    CONSULTAR ORDENES DE COMPRA DE LA REQUISICIONES

    FUNCION QUE MANDA EL ID DE LA REQUISICION
    PARA OBTENER LAS ORDENES DE COMPRA DE ELLA
    =============================================*/
    public function obtenerOrdenCompraRequisicion($requisicionId){

        $respuesta = Conexion::queryAll($this->bdName, "SELECT
                                                            OC.folio,
                                                            CASE
                                                                WHEN OC.tiempoEntrega IS NULL OR OC.tiempoEntrega = '' THEN 'no especificado'
                                                                ELSE OC.tiempoEntrega
                                                            END AS tiempoEntrega,
                                                            E.descripcion as nombreEstatus
                                                        FROM ordencompra OC
                                                        LEFT JOIN estatus E ON E.id = OC.estatusId
                                                        WHERE OC.requisicionId  = $requisicionId
                                                            ", $error);
        return $respuesta;
    }

    public function crear($datos) {

        $arrayPDOParam = array();
        // TODO: Cuando esten las requisiciones se debe de guardar
        $arrayPDOParam["fk_IdObra"] = self::$type["fk_IdObra"];
        $arrayPDOParam["periodo"] = self::$type["periodo"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["divisa"] = self::$type["divisa"];
        $arrayPDOParam["tipoRequisicion"] = self::$type["tipoRequisicion"];
        $arrayPDOParam["fechaRequerida"] = self::$type["fechaRequerida"];
        $arrayPDOParam["direccion"] = self::$type["direccion"];
        $arrayPDOParam["especificaciones"] = self::$type["especificaciones"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["justificacion"] = self::$type["justificacion"];
        $campos = fCreaCamposInsert($arrayPDOParam);

        $requisicionId = 0;
        $respuesta = Conexion::queryExecute($this->bdName,"INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $requisicionId);

        if ( $respuesta ){
            $this->id = $requisicionId;
            $this->folio = $datos["folio"];

            if ( isset($datos['cotizacionArchivos']) && $datos['cotizacionArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['cotizacionArchivos'], 4, '../../');
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        if ( isset($datos["estatusId"]) ){
            if ( $datos["estatusId"] !== $_POST["actualEstatusId"] ) {
                $arrayPDOParam["estatusId"] = self::$type["estatusId"];
                if ($datos["estatusId"] == 9) {
                    $datos["usuarioIdAlmacen"] = usuarioAutenticado()["id"];
                    $arrayPDOParam["usuarioIdAlmacen"] = self::$type["usuarioIdAlmacen"];
                } else if ($datos["estatusId"] == 10) {
                    $datos["usuarioIdAutorizacion"] = usuarioAutenticado()["id"];
                    $arrayPDOParam["usuarioIdAutorizacion"] = self::$type["usuarioIdAutorizacion"];
                }
            }
        }
        if ( isset($datos["iva"]) ) $arrayPDOParam["iva"] = self::$type["iva"];
        if ( isset($datos["retencionIva"]) ) $arrayPDOParam["retencionIva"] = self::$type["retencionIva"];
        if ( isset($datos["retencionIsr"]) ) {
            $arrayPDOParam["retencionIsr"] = self::$type["retencionIsr"];
            $datos["retencionIsr"] = str_replace(',', '', $datos["retencionIsr"]);
        }
        if ( isset($datos["descuento"]) ) {
            $arrayPDOParam["descuento"] = self::$type["descuento"];
            $datos["descuento"] = str_replace(',', '', $datos["descuento"]);
        }
        $arrayPDOParam["justificacion"] = self::$type["justificacion"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["direccion"] = self::$type["direccion"];
        $arrayPDOParam["especificaciones"] = self::$type["especificaciones"];
        $arrayPDOParam["fechaRequerida"] = self::$type["fechaRequerida"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["categoriaId"] = self::$type["categoriaId"];
        $arrayPDOParam["tipoRequisicion"] = self::$type["tipoRequisicion"];
        $arrayPDOParam["divisa"] = self::$type["divisa"];
        $arrayPDOParam["presupuesto"] = self::$type["presupuesto"];

        $datos["fechaRequerida"] = fFechaSQL($datos["fechaRequerida"]);

        $campos = fCreaCamposUpdate($arrayPDOParam);
        // Actualizar la requisicion
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            if ( isset($datos["estatusId"]) ) $this->estatusId = $datos["estatusId"];
            //Ingresa las nuevas observaciones
            if ( isset($datos["observacion"]) ) {
                $insertar = array();
                $insertar["requisicionId"] = $this->id;
                $insertar["estatusId"] = $datos["estatusId"];
                $insertar["observacion"] = $datos["observacion"];
                $insertar["usuarioIdCreacion"] = $datos["usuarioIdActualizacion"];

                $insertarPDOParam = array();
                $insertarPDOParam["requisicionId"] = self::$type["id"];
                $insertarPDOParam["estatusId"] = self::$type["estatusId"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_observaciones " . $campos, $insertar, $insertarPDOParam, $error);

            }

            if ( isset($datos['comprobanteArchivos']) && $datos['comprobanteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['comprobanteArchivos'], 1);

            if ( isset($datos['ordenesArchivos']) && $datos['ordenesArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['ordenesArchivos'], 2);

            if ( isset($datos['facturaArchivos']) && $datos['facturaArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['facturaArchivos'], 3);

            if ( isset($datos['cotizacionArchivos']) && $datos['cotizacionArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['cotizacionArchivos'], 4);

            if ( isset($datos['valeArchivos']) && $datos['valeArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['valeArchivos'], 5);

            if ( isset($datos['resguardoArchivos']) && $datos['resguardoArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['resguardoArchivos'], 6);

            if ( isset($datos['soporte']) && $datos['soporte']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['soporte'], 7);
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

        for ($i = 0; $i < count($archivos['name']); $i++) {

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                if ( $tipoArchivo == 1 ) $directorio =  "vistas/uploaded-files/requisiciones/comprobantes-pago/";
                elseif ( $tipoArchivo == 2 ) $directorio = "vistas/uploaded-files/requisiciones/ordenes-compra/";
                elseif ( $tipoArchivo == 3 ) $directorio = "vistas/uploaded-files/requisiciones/facturas/";
                elseif ( $tipoArchivo == 4 ) $directorio = "vistas/uploaded-files/requisiciones/cotizaciones/";
                elseif ( $tipoArchivo == 6 ) $directorio = "vistas/uploaded-files/requisiciones/resguardos/";
                elseif ( $tipoArchivo == 7 ) $directorio = "vistas/uploaded-files/requisiciones/soporte/";

                else $directorio = "vistas/uploaded-files/requisiciones/vales-almacen/";
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
                move_uploaded_file($tmp_name, $dir.$ruta);
            }

        }

        return $respuesta;

    }

    // ACTUALIZA EL ESTATUS DE LA REQUISICION DEPENDE LA TIPO DE ENTREGO

    public function actualizarEstatusPorEntrada($verificacionEntrada){

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];


        $datos = array();
        $datos[$this->keyName] = $this->id;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        if(!$verificacionEntrada){

            $datos["estatusId"] = 5;
            return  Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
        }

        $datos["estatusId"] = 6;
        return  Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function totalCantidadPartidas(){

        $resultado = Conexion::queryAll($this->bdName,
        "SELECT
        SUM(ID.cantidad) AS totalInventario,
        (SELECT SUM(P.cantidad) FROM partidas P WHERE P.requisicionId = I.requisicionId) AS totalPartidas,
        SUM(ID.cantidad) - (SELECT SUM(P.cantidad) FROM partidas P WHERE P.requisicionId = I.requisicionId) AS diferencia
        FROM inventarios I
        INNER JOIN inventario_detalles ID ON I.id = ID.inventario
        WHERE I.requisicionId =$this->id", $error);

            return  abs($resultado[0]["diferencia"]);
    }

    public function autorizar(){
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["usuarioIdAutorizacionAdd"] = self::$type["usuarioIdAutorizacionAdd"];

        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["usuarioIdAutorizacionAdd"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    // FUNCION GENERAL PARA CAMBIAR DE OBRA LA REQUISICIÓN
    public function cambiarObra(){

        // CAMBIA LA OBRA EN REQUISICIONES
        $respuesta = $this->cambiarObraDeRequisicion();

        if($respuesta){
            // OBTENER LAS PARTIDAS
            foreach($this->obraDetalleIdsPartidas() as $value){
                // VALIDACION SI YA ESTA LA OBRA ASIGNADA
                if($this->obraId != $value["obraId"]){
                    // VALIDACION SI EXISTE O NO LA PARTIDA
                    $this->validacionObraDetalle($value);
                }
            }
        }

        return $respuesta;
    }

    // OBTENER IDS DE LAS OBRAS DETALLE DE LAS PARTIDAS
    public function obraDetalleIdsPartidas(){

        $query = "SELECT
                    P.id AS partidaId,
                    P.requisicionId,
                    P.obraDetalleId,
                    OD.*
                FROM
                    partidas P
                INNER JOIN obra_detalles OD ON OD.id = P.obraDetalleId
                WHERE
                    P.requisicionId = $this->id
                LIMIT 0, 25";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    // CAMBIAR LA OBRA DE LA TABLA OBRA DETALLE
    public function validacionObraDetalle($valuePartida){

        // VARIFICAR SI HAY OBRAS DETALLES DE LA OBRA SELECCIONADA
        $existenciaObraDetalleId = $this->existenciaObraDetalleId($valuePartida);

        if($existenciaObraDetalleId){
            // ACTUALIZAR
            foreach ($existenciaObraDetalleId as $value) {
                // ACTUALIZAR PARTIDAS CON LA OBRA DETALLE ID
                $this->actualizarObraDetalle($value,$valuePartida);
            }
        }else{
            // CREAR ORDEN DETALLE Y ASIGNAR
            $this->crearObraDetalle($valuePartida);
        }
    }

    //CAMBIAR OBRA DE LA REQUISICION
    public function cambiarObraDeRequisicion(){

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["fk_IdObra"] = self::$type["fk_IdObra"];

        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["fk_IdObra"] = $this->obraId;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    // BUSCAR OBRAS DETALLES EXISTENTES
    public function existenciaObraDetalleId($value){

        $obraId = $this->obraId;
        $insumoId = $value["insumoId"];
        $indirectoId = $value["indirectoId"];

        $query = "SELECT
                    OD.*
                FROM
                    obra_detalles OD
                WHERE
                    OD.obraId = $obraId";

        // Condicional para decidir si usar insumoId o indirectoId
        if (!empty($insumoId)) {
            $query .= " AND OD.insumoId = $insumoId";
        } elseif (!empty($indirectoId)) {
            $query .= " AND OD.indirectoId = $indirectoId";
        }

        $query .= " LIMIT 0, 25";

        return  Conexion::queryAll($this->bdName, $query, $error);
    }

    //CREAR OBRA DETALLE Y ASIGNAR A LAS PARTIDAS
    public function crearObraDetalle($value){

        $arrayPDOParam = array();
        $arrayPDOParam["obraId"] = 'integer';
        $arrayPDOParam["insumoId"] = 'integer';
        $arrayPDOParam["indirectoId"] = 'integer';
        $arrayPDOParam["cantidad"] = 'decimal';
        $arrayPDOParam["presupuesto"] = 'decimal';
        $arrayPDOParam["presupuesto_dolares"] = 'decimal';
        $campos = fCreaCamposInsert($arrayPDOParam);

        $datos = array();
        $datos["obraId"] = $this->obraId;
        $datos["insumoId"] = $value["insumoId"];
        $datos["indirectoId"] = $value["indirectoId"];
        $datos["cantidad"] = 1.00;
        $datos["presupuesto"] = 1.00;
        $datos["presupuesto_dolares"] = 1.00;

        $lastId = 0;

        $respuesta = Conexion::queryExecute($this->bdName,"INSERT INTO obra_detalles ".$campos, $datos,$arrayPDOParam,
        $error,$lastId);

        if($respuesta){

            $arrayPartidaPDOParam = array();
            $arrayPartidaPDOParam[$this->keyName] = self::$type[$this->keyName];
            $arrayPartidaPDOParam["obraDetalleId"] = 'integer';

            $datosPartida = array();
            $datosPartida[$this->keyName] = $value["partidaId"];
            $datosPartida["obraDetalleId"] = $lastId;

            $camposPartida = fCreaCamposUpdate($arrayPartidaPDOParam);

            return Conexion::queryExecute($this->bdName, "UPDATE partidas SET " . $camposPartida . " WHERE id = :id", $datosPartida, $arrayPartidaPDOParam, $error);
        }

        return $respuesta;
    }

    public function actualizarObraDetalle($value,$valuePartida){

        $arrayPartidaPDOParam = array();
        $arrayPartidaPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPartidaPDOParam["obraDetalleId"] = 'integer';

        $datosPartida = array();
        $datosPartida[$this->keyName] = $valuePartida["partidaId"];
        $datosPartida["obraDetalleId"] = $value["id"];

        $camposPartida = fCreaCamposUpdate($arrayPartidaPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE partidas SET " . $camposPartida . " WHERE id = :id", $datosPartida, $arrayPartidaPDOParam, $error);

    }

    public function consultarPorInsumo($insumoId, $obraId, $anio, $mes) {
        $query = "SELECT R.folio, R.id, R.justificacion, R.fechaCreacion, P.costo
                  FROM requisiciones R
                  INNER JOIN partidas P ON P.requisicionId = R.id
                  INNER JOIN obra_detalles OD ON OD.id = P.obraDetalleId
                  LEFT JOIN insumos I ON I.id = OD.insumoId
                  WHERE I.id = $insumoId and R.fk_idObra = $obraId and YEAR(R.fechaRequerida) = $anio and MONTH(R.fechaRequerida) = $mes";
        return Conexion::queryAll($this->bdName, $query);
    }

    public function eliminarDetalle()
    {
        $query = "DELETE FROM partidas WHERE id = :id";
        $datos = ["id" => $this->detalleId];
        $arrayPDOParam = ["id" => "integer"];
        return Conexion::queryExecute($this->bdName, $query, $datos, $arrayPDOParam, $error);
    }

}