<?php

namespace App\Models;


use App\Conexion;
use PDO;

class EstatusOrdenCompra 
{
    static protected $fillable = [
        'descripcion', 'nombreCorto', 'colorTexto', 'colorFondo', 'servicioAbierto', 'servicioCerrado', 'requisicionAbierta', 'requisicionCerrada', 'requisicionOrden', 'requisicionAgregarPartidas', 'requisicionUsuarioCreacion', 'ordenCompraAbierta', 'ordenCompraCerrada'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'colorTexto' => 'string',
        'colorFondo' => 'string',
        'servicioAbierto' => 'integer',
        'servicioCerrado' => 'integer',
        'requisicionAbierta' => 'integer',
        'requisicionCerrada' => 'integer',
        'requisicionOrden' => 'integer',
        'requisicionAgregarPartidas' => 'integer',
        'requisicionUsuarioCreacion' => 'integer',
        'ordenCompraAbierta' => 'integer',
        'ordenCompraCerrada' => 'integer'
    ];

    protected $bdName = "atiberna_tibernal";
    protected $tableName = "estatus_orden_compra";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;
    public $colorTexto;
    public $colorFondo;
    public $servicioAbierto;
    public $servicioCerrado;
    public $requisicionAbierta;
    public $requisicionCerrada;
    public $requisicionOrden;
    public $requisicionAgregarPartidas;
    public $requisicionUsuarioCreacion;
    public $ordenCompraAbierta;
    public $ordenCompraCerrada;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR ESTATUS DE SERVICIO
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT SE.* FROM $this->tableName SE ORDER BY SE.descripcion", $error);

        } else {

            if ( is_null($item) ) {
                
                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
            }            
            if ( $respuesta ) {
                $this->id = $respuesta["id"] ?? null;
                $this->descripcion = $respuesta["descripcion"] ?? "";
                $this->nombreCorto = $respuesta["nombreCorto"] ?? "";
                $this->colorTexto = $respuesta["colorTexto"] ?? "";
                $this->colorFondo = $respuesta["colorFondo"] ?? "";
                $this->servicioAbierto = $respuesta["servicioAbierto"] ?? 0;
                $this->servicioCerrado = $respuesta["servicioCerrado"] ?? 0;
                $this->requisicionAbierta = $respuesta["requisicionAbierta"] ?? 0;
                $this->requisicionCerrada = $respuesta["requisicionCerrada"] ?? 0;
                $this->requisicionOrden = $respuesta["requisicionOrden"] ?? 0;
                $this->requisicionAgregarPartidas = $respuesta["requisicionAgregarPartidas"] ?? 0;
                $this->requisicionUsuarioCreacion = $respuesta["requisicionUsuarioCreacion"] ?? "";
                $this->ordenCompraAbierta = $respuesta["ordenCompraAbierta"] ?? 0;
                $this->ordenCompraCerrada = $respuesta["ordenCompraCerrada"] ?? 0;
            }

            return $respuesta;
        }

    }

    public function crear($datos) {

        // Modificar el contenido de los checkboxes
        $datos["servicioAbierto"] = ( isset($datos["servicioAbierto"]) && mb_strtolower($datos["servicioAbierto"]) == "on" ) ? "1" : "0";
        $datos["servicioCerrado"] = ( isset($datos["servicioCerrado"]) && mb_strtolower($datos["servicioCerrado"]) == "on" ) ? "1" : "0";
        $datos["requisicionAbierta"] = ( isset($datos["requisicionAbierta"]) && mb_strtolower($datos["requisicionAbierta"]) == "on" ) ? "1" : "0";
        $datos["requisicionCerrada"] = ( isset($datos["requisicionCerrada"]) && mb_strtolower($datos["requisicionCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAgregarPartidas"] = ( isset($datos["requisicionAgregarPartidas"]) && mb_strtolower($datos["requisicionAgregarPartidas"]) == "on" ) ? "1" : "0";
        $datos["requisicionUsuarioCreacion"] = ( isset($datos["requisicionUsuarioCreacion"]) && mb_strtolower($datos["requisicionUsuarioCreacion"]) == "on" ) ? "1" : "0";
        $datos["ordenCompraAbierta"] = ( isset($datos["ordenCompraAbierta"]) && mb_strtolower($datos["ordenCompraAbierta"]) == "on" ) ? "1" : "0";
        $datos["ordenCompraCerrada"] = ( isset($datos["ordenCompraCerrada"]) && mb_strtolower($datos["ordenCompraCerrada"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["colorTexto"] = self::$type["colorTexto"];
        $arrayPDOParam["colorFondo"] = self::$type["colorFondo"];
        $arrayPDOParam["servicioAbierto"] = self::$type["servicioAbierto"];
        $arrayPDOParam["servicioCerrado"] = self::$type["servicioCerrado"];
        $arrayPDOParam["requisicionAbierta"] = self::$type["requisicionAbierta"];
        $arrayPDOParam["requisicionCerrada"] = self::$type["requisicionCerrada"];
        $arrayPDOParam["requisicionOrden"] = self::$type["requisicionOrden"];
        $arrayPDOParam["requisicionAgregarPartidas"] = self::$type["requisicionAgregarPartidas"];
        $arrayPDOParam["requisicionUsuarioCreacion"] = self::$type["requisicionUsuarioCreacion"];
        $arrayPDOParam["ordenCompraAbierta"] = self::$type["ordenCompraAbierta"];
        $arrayPDOParam["ordenCompraCerrada"] = self::$type["ordenCompraCerrada"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto, colorTexto, colorFondo, servicioAbierto, servicioCerrado, requisicionAbierta, requisicionCerrada, requisicionOrden, requisicionAgregarPartidas, requisicionUsuarioCreacion, ordenCompraAbierta, ordenCompraCerrada) VALUES (:descripcion, :nombreCorto, :colorTexto, :colorFondo, :servicioAbierto, :servicioCerrado, :requisicionAbierta, :requisicionCerrada, :requisicionOrden, :requisicionAgregarPartidas, :requisicionUsuarioCreacion, :ordenCompraAbierta, :ordenCompraCerrada)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Modificar el contenido de los checkboxes
        $datos["servicioAbierto"] = ( isset($datos["servicioAbierto"]) && mb_strtolower($datos["servicioAbierto"]) == "on" ) ? "1" : "0";
        $datos["servicioCerrado"] = ( isset($datos["servicioCerrado"]) && mb_strtolower($datos["servicioCerrado"]) == "on" ) ? "1" : "0";
        $datos["requisicionAbierta"] = ( isset($datos["requisicionAbierta"]) && mb_strtolower($datos["requisicionAbierta"]) == "on" ) ? "1" : "0";
        $datos["requisicionCerrada"] = ( isset($datos["requisicionCerrada"]) && mb_strtolower($datos["requisicionCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAgregarPartidas"] = ( isset($datos["requisicionAgregarPartidas"]) && mb_strtolower($datos["requisicionAgregarPartidas"]) == "on" ) ? "1" : "0";
        $datos["requisicionUsuarioCreacion"] = ( isset($datos["requisicionUsuarioCreacion"]) && mb_strtolower($datos["requisicionUsuarioCreacion"]) == "on" ) ? "1" : "0";
        $datos["ordenCompraAbierta"] = ( isset($datos["ordenCompraAbierta"]) && mb_strtolower($datos["ordenCompraAbierta"]) == "on" ) ? "1" : "0";
        $datos["ordenCompraCerrada"] = ( isset($datos["ordenCompraCerrada"]) && mb_strtolower($datos["ordenCompraCerrada"]) == "on" ) ? "1" : "0";
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["colorTexto"] = self::$type["colorTexto"];
        $arrayPDOParam["colorFondo"] = self::$type["colorFondo"];
        $arrayPDOParam["servicioAbierto"] = self::$type["servicioAbierto"];
        $arrayPDOParam["servicioCerrado"] = self::$type["servicioCerrado"];
        $arrayPDOParam["requisicionAbierta"] = self::$type["requisicionAbierta"];
        $arrayPDOParam["requisicionCerrada"] = self::$type["requisicionCerrada"];
        $arrayPDOParam["requisicionOrden"] = self::$type["requisicionOrden"];
        $arrayPDOParam["requisicionAgregarPartidas"] = self::$type["requisicionAgregarPartidas"];
        $arrayPDOParam["requisicionUsuarioCreacion"] = self::$type["requisicionUsuarioCreacion"];
        $arrayPDOParam["ordenCompraAbierta"] = self::$type["ordenCompraAbierta"];
        $arrayPDOParam["ordenCompraCerrada"] = self::$type["ordenCompraCerrada"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, colorTexto = :colorTexto, colorFondo = :colorFondo, servicioAbierto = :servicioAbierto, servicioCerrado = :servicioCerrado, requisicionAbierta = :requisicionAbierta, requisicionCerrada = :requisicionCerrada, requisicionOrden = :requisicionOrden, requisicionAgregarPartidas = :requisicionAgregarPartidas, requisicionUsuarioCreacion = :requisicionUsuarioCreacion, ordenCompraCerrada = :ordenCompraCerrada, ordenCompraAbierta = :ordenCompraAbierta WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
