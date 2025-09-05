<?php

namespace App\Models;

use App\Conexion;
use PDO;

class Servicio 
{
    static protected $fillable = [
        'empresaId', 'servicioCentroId', 'numero', 'folio', 'maquinariaId', 'ubicacionId', 'obraId', 'mantenimientoTipoId', 'servicioTipoId', 'servicioEstatusId', 'solicitudTipoId', 'horasProyectadas', 'fechaSolicitud', 'fechaProgramacion', 'fechaFinalizacion', 'descripcion', 'imagenes','horoOdometro'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresaId' => 'integer',
        'servicioCentroId' => 'integer',
        'numero' => 'integer',
        'folio' => 'string',
        'maquinariaId' => 'integer',
        'ubicacionId' => 'integer',
        'obraId' => 'integer',
        'horoOdometro' => 'decimal',
        'mantenimientoTipoId' => 'integer',
        'servicioTipoId' => 'integer',
        'servicioEstatusId' => 'integer',
        'solicitudTipoId' => 'integer',
        'horasProyectadas' => 'decimal',
        'fechaSolicitud' => 'date',
        'fechaProgramacion' => 'date',
        'fechaFinalizacion' => 'date',
        'descripcion' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "servicios";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $servicioCentroId;
    public $numero;
    public $folio;
    public $maquinariaId;
    public $ubicacionId;
    public $obraId;
    public $horoOdometro;
    public $mantenimientoTipoId;
    public $servicioTipoId;
    public $servicioEstatusId;
    public $solicitudTipoId;
    public $horasProyectadas;
    public $fechaSolicitud;
    public $fechaProgramacion;
    public $fechaFinalizacion;
    public $descripcion;
    public $horasReales;
    public $cant_imagenes;
    public $cant_archivos;
    public $usuarioIdCreacion;
    public $usuarioIdActualizacion;
    public $sumHorasTrabajadas;
    public $maquinaria;
    public $ubicacion;
    public $estatus;
    public $solicitudTipo;

    public $requisiciones;
    public $actividades;

    
    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    CONSULTAR ULTIMO VALOR DEL CAMPO numero
    =============================================*/
    public function consultarLastId($empresaId, $servicioCentroId) {

        $query = "SELECT    E.nomenclaturaOT AS 'empresas.nomenclaturaOT', SC.nomenclaturaOT AS 'servicio_centros.nomenclaturaOT', MAX(S.numero) AS 'numero'
                FROM        {$this->tableName} S
                INNER JOIN  empresas E ON S.empresaId = E.id
                INNER JOIN  servicio_centros SC ON S.servicioCentroId = SC.id
                WHERE       S.empresaId = {$empresaId} AND S.servicioCentroId = {$servicioCentroId}";

        $respuesta = Conexion::queryUnique($this->bdName, $query, $error);

        return $respuesta;

    }

    /*=============================================
    MOSTRAR SERVICIOS ABIERTOS
    =============================================*/
    public function consultarAbiertos($empresaId = null)
    {
        if ( $empresaId ) {
            // $respuesta = Conexion::queryAll($this->bdName, "SELECT S.*, M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie', SE.servicioAbierto AS 'servicio_estatus.servicioAbierto', SE.descripcion AS 'servicio_estatus.descripcion' FROM $this->tableName S INNER JOIN maquinarias M ON S.maquinariaId = M.id INNER JOIN servicio_estatus SE ON S.servicioEstatusId = SE.id WHERE S.empresaId = {$empresaId} AND SE.servicioAbierto = 1 ORDER BY S.folio, S.fechaSolicitud DESC, S.numero DESC", $error);

            $respuesta = Conexion::queryAll($this->bdName, "SELECT S.*, M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie', SE.servicioAbierto AS 'servicio_estatus.servicioAbierto', SE.descripcion AS 'servicio_estatus.descripcion' FROM $this->tableName S INNER JOIN maquinarias M ON S.maquinariaId = M.id INNER JOIN servicio_estatus SE ON S.servicioEstatusId = SE.id WHERE S.empresaId = {$empresaId} ORDER BY S.folio, S.fechaSolicitud DESC, S.numero DESC", $error);
        } else {
            // $respuesta = Conexion::queryAll($this->bdName, "SELECT S.*, M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie', SE.servicioAbierto AS 'servicio_estatus.servicioAbierto', SE.descripcion AS 'servicio_estatus.descripcion' FROM $this->tableName S INNER JOIN maquinarias M ON S.maquinariaId = M.id INNER JOIN servicio_estatus SE ON S.servicioEstatusId = SE.id WHERE SE.servicioAbierto = 1 ORDER BY S.folio, S.fechaSolicitud DESC, S.numero DESC", $error);

            $respuesta = Conexion::queryAll($this->bdName, "SELECT S.*, M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie', SE.servicioAbierto AS 'servicio_estatus.servicioAbierto', SE.descripcion AS 'servicio_estatus.descripcion' FROM $this->tableName S INNER JOIN maquinarias M ON S.maquinariaId = M.id INNER JOIN servicio_estatus SE ON S.servicioEstatusId = SE.id ORDER BY S.folio, S.fechaSolicitud DESC, S.numero DESC", $error);
        }
        return $respuesta;
    }

    /*=============================================
    MOSTRAR SERVICIOS CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT    S.*, E.nombreCorto AS 'empresas.nombreCorto', SC.descripcion AS 'servicio_centros.descripcion',
                            M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie',
                            MT.descripcion AS 'maquinaria_tipos.descripcion', MO.descripcion AS 'modelos.descripcion',
                            MA.descripcion AS 'marcas.descripcion', U.descripcion AS 'ubicaciones.descripcion',
                            MAT.descripcion AS 'mantenimiento_tipos.descripcion', ST.descripcion AS 'servicio_tipos.descripcion',
                            SE.descripcion AS 'servicio_estatus.descripcion', SE.colorTexto AS 'servicio_estatus.colorTexto', SE.colorFondo AS 'servicio_estatus.colorFondo',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        $this->tableName S
                INNER JOIN  empresas E ON S.empresaId = E.id
                INNER JOIN  servicio_centros SC ON S.servicioCentroId = SC.id
                INNER JOIN  maquinarias M ON S.maquinariaId = M.id
                INNER JOIN  maquinaria_tipos MT ON M.maquinariaTipoId = MT.id
                INNER JOIN  modelos MO ON M.modeloId = MO.id
                INNER JOIN  marcas MA ON MO.marcaId = MA.id
                INNER JOIN  ubicaciones U ON S.ubicacionId = U.id
                INNER JOIN  mantenimiento_tipos MAT ON S.mantenimientoTipoId = MAT.id
                INNER JOIN  servicio_tipos ST ON S.servicioTipoId = ST.id
                INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                INNER JOIN  usuarios US ON S.usuarioIdCreacion = US.id";

        if ( count($arrayFiltros) == 0 ) {
            $query .= " WHERE       S.servicioEstatusId <> 4";
        } else {
            $filtroEstatus = false;
            foreach ($arrayFiltros as $key => $value) {
                if ( $value['campo'] == 'S.servicioEstatusId' ) $filtroEstatus = true;

                if ( $key == 0 ) $query .= " WHERE";
                if ( $key > 0 ) $query .= " AND";
                // $query .= " {$value['campo']} = {$value['valor']}";
                $query .= " {$value['campo']} {$value['operador']} {$value['valor']}";
            }
            if ( !$filtroEstatus ) $query .= " AND S.servicioEstatusId <> 4";
        }

        $query .= " ORDER BY    S.fechaSolicitud DESC, E.id, SC.id, S.numero DESC, M.maquinariaTipoId, M.descripcion";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR SERVICIOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            $query = "SELECT S.*, E.nombreCorto AS 'empresas.nombreCorto', SC.descripcion AS 'servicio_centros.descripcion',
                    M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie',
                    MT.descripcion AS 'maquinaria_tipos.descripcion', MO.descripcion AS 'modelos.descripcion',
                    MA.descripcion AS 'marcas.descripcion', U.descripcion AS 'ubicaciones.descripcion',
                    MAT.descripcion AS 'mantenimiento_tipos.descripcion', ST.descripcion AS 'servicio_tipos.descripcion',
                    SE.descripcion AS 'servicio_estatus.descripcion', SE.colorTexto AS 'servicio_estatus.colorTexto', SE.colorFondo AS 'servicio_estatus.colorFondo',
                    ( SELECT COUNT(SI.id) FROM servicio_imagenes SI WHERE SI.servicioId = S.id ) AS cant_imagenes,
                    US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        {$this->tableName} S
                INNER JOIN  empresas E ON S.empresaId = E.id
                INNER JOIN  servicio_centros SC ON S.servicioCentroId = SC.id
                INNER JOIN  maquinarias M ON S.maquinariaId = M.id
                INNER JOIN  maquinaria_tipos MT ON M.maquinariaTipoId = MT.id
                INNER JOIN  modelos MO ON M.modeloId = MO.id
                INNER JOIN  marcas MA ON MO.marcaId = MA.id
                INNER JOIN  ubicaciones U ON S.ubicacionId = U.id
                INNER JOIN  mantenimiento_tipos MAT ON S.mantenimientoTipoId = MAT.id
                INNER JOIN  servicio_tipos ST ON S.servicioTipoId = ST.id
                INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                INNER JOIN  usuarios US ON S.usuarioIdCreacion = US.id
                WHERE       S.servicioEstatusId <> 4 AND S.servicioEstatusId <> 20 AND S.servicioEstatusId <> 21 AND S.fechaSolicitud >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                ORDER BY    S.fechaSolicitud DESC, E.id, SC.id, S.numero DESC, M.maquinariaTipoId, M.descripcion";

            return Conexion::queryAll($this->bdName, $query, $error);

        } else {

            if ( is_null($item) ) {

                // $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);
                $respuesta = Conexion::queryUnique($this->bdName, "SELECT S.*, ( SELECT SUM(AD.horas) FROM actividad_detalles AD WHERE AD.servicioId = S.id ) AS sumHorasTrabajadas, ( SELECT COUNT(SI.id) FROM servicio_imagenes SI WHERE SI.servicioId = S.id ) AS cant_imagenes, ( SELECT COUNT(SA.id) FROM servicio_archivos SA WHERE SA.servicioId = S.id ) AS cant_archivos FROM $this->tableName S WHERE S.$this->keyName = $valor", $error);

            } else {

                // $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                $respuesta = Conexion::queryUnique($this->bdName, "SELECT S.*, ( SELECT SUM(AD.horas) FROM actividad_detalles AD WHERE AD.servicioId = S.id ) AS sumHorasTrabajadas, ( SELECT COUNT(SI.id) FROM servicio_imagenes SI WHERE SI.servicioId = S.id ) AS cant_imagenes, ( SELECT COUNT(SA.id) FROM servicio_archivos SA WHERE SA.servicioId = S.id ) AS cant_archivos FROM $this->tableName S WHERE S.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresaId = $respuesta["empresaId"];
                $this->servicioCentroId = $respuesta["servicioCentroId"];
                $this->numero = $respuesta["numero"];
                $this->folio = $respuesta["folio"];
                $this->maquinariaId = $respuesta["maquinariaId"];
                $this->ubicacionId = $respuesta["ubicacionId"];
                $this->obraId = $respuesta["obraId"];
                $this->horoOdometro = $respuesta["horoOdometro"];
                $this->mantenimientoTipoId = $respuesta["mantenimientoTipoId"];
                $this->servicioTipoId = $respuesta["servicioTipoId"];
                $this->servicioEstatusId = $respuesta["servicioEstatusId"];
                $this->solicitudTipoId = $respuesta["solicitudTipoId"];
                $this->horasProyectadas = $respuesta["horasProyectadas"];
                $this->horasReales = $respuesta["horasReales"];
                $this->fechaSolicitud = $respuesta["fechaSolicitud"];
                $this->fechaProgramacion = $respuesta["fechaProgramacion"];
                $this->fechaFinalizacion = $respuesta["fechaFinalizacion"];
                $this->descripcion = $respuesta["descripcion"];
                $this->cant_imagenes = $respuesta["cant_imagenes"];
                $this->cant_archivos = $respuesta["cant_archivos"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->sumHorasTrabajadas = ( is_null($respuesta["sumHorasTrabajadas"]) ) ? 0 : $respuesta["sumHorasTrabajadas"];

                // require_once "app/Models/Maquinaria.php";
                if ( file_exists ( "app/Models/Maquinaria.php" ) ) {
                    require_once "app/Models/Maquinaria.php";
                } else {
                    require_once "../Models/Maquinaria.php";
                }
                $maquinaria = New Maquinaria;
                $this->maquinaria = $maquinaria->consultar(null, $this->maquinariaId);

                // require_once "app/Models/Ubicacion.php";
                if ( file_exists ( "app/Models/Ubicacion.php" ) ) {
                    require_once "app/Models/Ubicacion.php";
                } else {
                    require_once "../Models/Ubicacion.php";
                }
                $ubicacion = New Ubicacion;
                $this->ubicacion = $ubicacion->consultar(null, $this->ubicacionId);

                // require_once "app/Models/ServicioEstatus.php";
                if ( file_exists ( "app/Models/ServicioEstatus.php" ) ) {
                    require_once "app/Models/ServicioEstatus.php";
                } else {
                    require_once "../Models/ServicioEstatus.php";
                }
                $servicioEstatus = New ServicioEstatus;
                $this->estatus = $servicioEstatus->consultar(null, $this->servicioEstatusId);

                if ( file_exists ( "app/Models/SolicitudTipo.php" ) ) {
                    require_once "app/Models/SolicitudTipo.php";
                } else {
                    require_once "../Models/SolicitudTipo.php";
                }
                $solicitudTipo = New SolicitudTipo;
                $this->solicitudTipo = $solicitudTipo->consultar(null, $this->solicitudTipoId);
            }

            return $respuesta;

        }

    }


    public function consultarPorRequisicion($id) {

            $query = "   SELECT S.id  AS id_servicio ,R.id AS id_requisicion ,
                         S.servicioCentroId,S.mantenimientoTipoId FROM requisiciones R
                        INNER JOIN	servicios S ON R.servicioId = S.id 
                        WHERE R.id = $id;";

            return Conexion::queryAll($this->bdName, $query, $error);
    }


    public function consultarImagenes($servicioId)
    {

        $query = "SELECT    SI.*
                FROM        servicio_imagenes SI
                WHERE       SI.servicioId = {$servicioId}
                ORDER BY    SI.id";

        return Conexion::queryAll($this->bdName, $query, $error);

    }

    public function consultarArchivos($servicioId)
    {

        $query = "SELECT    SA.*
                FROM        servicio_archivos SA
                WHERE       SA.servicioId = {$servicioId}
                ORDER BY    SA.id";

        return Conexion::queryAll($this->bdName, $query, $error);

    }   

    public function consultarRequisiciones() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT R.*, E.nombreCorto AS 'empresas.nombreCorto', SC.descripcion AS 'servicio_centros.descripcion', M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie', SE.descripcion AS 'servicio_estatus.descripcion' FROM requisiciones R INNER JOIN servicios S ON R.servicioId = S.id INNER JOIN empresas E ON S.empresaId = E.id INNER JOIN servicio_centros SC ON S.servicioCentroId = SC.id INNER JOIN maquinarias M ON S.maquinariaId = M.id INNER JOIN servicio_estatus SE ON R.servicioEstatusId = SE.id WHERE R.servicioId = $this->id ORDER BY R.fechaCreacion DESC, E.id, SC.id, S.numero DESC, R.numero DESC", $error);
    
        $this->requisiciones = $resultado;

    }

    public function consultarActividades() {

        $query = "SELECT      AD.*
                  FROM        actividad_detalles AD
                  INNER JOIN  servicios S ON AD.servicioId = S.id
                  WHERE       AD.servicioId = {$this->id}
                  ORDER BY    AD.fecha DESC, AD.id DESC";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);
    
        $this->actividades = $resultado;

    }

    public function crear($datos) {

        // VALIDACION AL CREAR EL TRASLADO
        $datos["obraId"] = $datos["obraId"] ?? "";

        // Buscar el último folio según la Empresa y Centro de Servicio
        $lastId = $this->consultarLastId($datos["empresaId"], $datos["servicioCentroId"]);

        if ( $lastId === false || $lastId[0] === null ) {
            $lastId["numero"] = 0;
            $lastId["empresas.nomenclaturaOT"] = $datos['empresas.nomenclaturaOT'];
            $lastId["servicio_centros.nomenclaturaOT"] = $datos['servicio_centros.nomenclaturaOT'];
        }

        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        // Agregar al request para especificar numero y folio del Servicio
        $datos["numero"] = (int) $lastId["numero"] + 1;
        $datos["folio"] = "{$lastId["empresas.nomenclaturaOT"]}-{$lastId["servicio_centros.nomenclaturaOT"]}-{$datos["numero"]}";

        // Convertir los campos date (fechaLarga) a formato SQL
        // $datos["fechaSolicitud"] = fFechaSQL($datos["fechaSolicitud"]);
        if ( isset($datos["fechaSolicitud"]) ) $datos["fechaSolicitud"] = fFechaSQL($datos["fechaSolicitud"]);
        if ( $datos["fechaProgramacion"] != '' ) $datos["fechaProgramacion"] = fFechaSQL($datos["fechaProgramacion"]);
        // Quitar las comas de los campos decimal
        $datos["horasProyectadas"] = str_replace(',', '', $datos["horasProyectadas"]);

        $arrayPDOParam = array();
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["servicioCentroId"] = self::$type["servicioCentroId"];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["ubicacionId"] = self::$type["ubicacionId"];
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["mantenimientoTipoId"] = self::$type["mantenimientoTipoId"];
        $arrayPDOParam["servicioTipoId"] = self::$type["servicioTipoId"];
        $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParam["solicitudTipoId"] = self::$type["solicitudTipoId"];
        $arrayPDOParam["horasProyectadas"] = self::$type["horasProyectadas"];
        // $arrayPDOParam["fechaSolicitud"] = self::$type["fechaSolicitud"];
        if ( isset($datos["fechaSolicitud"]) ) $arrayPDOParam["fechaSolicitud"] = self::$type["fechaSolicitud"];
        if ( $datos["fechaProgramacion"] != '' ) $arrayPDOParam["fechaProgramacion"] = self::$type["fechaProgramacion"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {
            $this->id = $lastId;
        }
        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        // $datos["fechaSolicitud"] = fFechaSQL($datos["fechaSolicitud"]);
        if ( $datos["fechaProgramacion"] != '' ) $datos["fechaProgramacion"] = fFechaSQL($datos["fechaProgramacion"]);
        // if ( $datos["fechaFinalizacion"] != '' ) $datos["fechaFinalizacion"] = fFechaSQL($datos["fechaFinalizacion"]);
        if ( isset($datos["fechaFinalizacion"]) ) $datos["fechaFinalizacion"] = fFechaSQL($datos["fechaFinalizacion"]);
        // Quitar las comas de los campos decimal
        $datos["horasProyectadas"] = str_replace(',', '', $datos["horasProyectadas"]);

        $arrayPDOParam = array();
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        // $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["ubicacionId"] = self::$type["ubicacionId"];
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["mantenimientoTipoId"] = self::$type["mantenimientoTipoId"];
        $arrayPDOParam["servicioTipoId"] = self::$type["servicioTipoId"];
        if ( isset($datos["servicioEstatusId"]) ) $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParam["solicitudTipoId"] = self::$type["solicitudTipoId"];
        $arrayPDOParam["horasProyectadas"] = self::$type["horasProyectadas"];
        // $arrayPDOParam["fechaSolicitud"] = self::$type["fechaSolicitud"];
        if ( $datos["fechaProgramacion"] != '' ) $arrayPDOParam["fechaProgramacion"] = self::$type["fechaProgramacion"];
        // if ( $datos["fechaFinalizacion"] != '' ) $arrayPDOParam["fechaFinalizacion"] = self::$type["fechaFinalizacion"];
        if ( isset($datos["fechaFinalizacion"]) ) $arrayPDOParam["fechaFinalizacion"] = self::$type["fechaFinalizacion"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta && $datos['imagenes']["tmp_name"][0]) {

            $respuesta = $this->insertarImagenes($datos['imagenes']);

        }

        return $respuesta;

    }

    function insertarImagenes($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) {

            if ( $archivos["tmp_name"][$i] == "" ) continue;

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            // if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/servicios/imagenes/";

                do {
                    $ruta = fRandomNameImageFile($directorio, $tipo);
                } while ( file_exists($ruta) );

            // }
            // Request con el nombre del archivo
            $insertar = array();
            $insertar["servicioId"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();
            $arrayPDOParam["servicioId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO servicio_imagenes " . $campos, $insertar, $arrayPDOParam, $error);

            $respuesta = true;
            if ( $respuesta && $ruta != "" ) {
                fSaveImageFile($tmp_name, $tipo, $ruta);
            }

        }

        return $respuesta;

    }

    function insertarArchivos($archivos)
    {
        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/servicios/documentos/";
                $extension = '';

                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";

                if ( $extension != '') {
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    // } while ( file_exists($ruta) );
                    } while ( file_exists($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$ruta) ); // Ruta absoluta al ser llamado desde JS
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["servicioId"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["servicioId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO servicio_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                // move_uploaded_file($tmp_name, $ruta);
                move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$ruta); // Ruta absoluta al ser llamado desde JS
            }

        }

        return $respuesta;
    }

    public function finalizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fechaFinalizacion"] = fFechaSQL($datos["fechaFinalizacion"]);

        // Quitar las comas de los campos decimal
        if ( isset($datos["horoOdometro"]) ) $datos["horoOdometro"] = str_replace(',', '', $datos["horoOdometro"]);

        $arrayPDOParam = array();
        if ( isset($datos["horoOdometro"]) ) $arrayPDOParam["horoOdometro"] = self::$type["horoOdometro"];
        $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParam["fechaFinalizacion"] = self::$type["fechaFinalizacion"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
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
