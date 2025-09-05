<?php

namespace App\Models;

if ( file_exists ( "app/Policies/DatosFiscalPolicy.php" ) ) {
    require_once "app/Policies/DatosFiscalPolicy.php";
} else {
    require_once "../Policies/DatosFiscalPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\DatosFiscalPolicy;
// extends DatosFiscalPolicy

class DatosFiscal 
{
    static protected $fillable = [
        'empresa',
        'razonSocial',
        'nombreComercial',
        'nombre',
        'apellidoPaterno',
        'apellidoMaterno',
        'telefono',
        'correo',
        'condicionContado',
        'condicionCredito',
        'ubicacion',
        'tiempoEntrega',
        'modalidadEntrega',
        'distribuidorAutorizado',
        'recursos',
        'zona',
        'domicilio',

        'tags' 
    ];

    static protected $type = [
        'id' => 'integer',
        'tags' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "proveedores";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function actualizar($datos) {

        // ACTUALIZAR PROVEEDOR        +
        $respuesta = $this->actualizarDatosProveedor($datos);
        return $respuesta;

    }

    // FUNCION PARA ACTUALIZAR DATOS
    public function actualizarDatosProveedor($datos) {
    
        $arrayPDOParam = array();
        $arrayPDOParam["nombre"] = 'string';
        $arrayPDOParam["apellidoPaterno"] = 'string';
        $arrayPDOParam["apellidoMaterno"] = 'string';
        $arrayPDOParam["telefono"] = 'string';
        $arrayPDOParam["correo"] = 'string';
        $arrayPDOParam["condicionContado"] = 'string';
        $arrayPDOParam["condicionCredito"] = 'string';
        $arrayPDOParam["ubicacion"] = 'string';
        $arrayPDOParam["tiempoEntrega"] = 'string';
        $arrayPDOParam["modalidadEntrega"] = 'string';
        $arrayPDOParam["distribuidorAutorizado"] = 'string';
        $arrayPDOParam["recursos"] = 'string';
        $arrayPDOParam["domicilio"] = 'string';
        $arrayPDOParam["zona"] = 'string';
        $arrayPDOParam["tags"] = self::$type["tags"];
        
        $datos["tags"] = ( isset($datos["tags"]) ) ? json_encode($datos["tags"]) : null;

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE proveedores SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);


    }
    
    // FUNCION PARA ACTUALIZAR CALIDAD DE PRODUCTO
    public function actualizarCalidadProductoProveedor($datos) {
    
        $arrayPDOParam = array();
        $arrayPDOParam["tiempoEntrega"] = 'string';
        $arrayPDOParam["modalidadEntrega"] = 'string';
        $arrayPDOParam["distribuidorAutorizado"] = 'string';
        $arrayPDOParam["recursos"] = 'string';

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE proveedores SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);


    }


}
