<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\OrdenCompra;

class SaveOrdenCompraRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];
        if ( isset($_REQUEST['estatusId']) ) $rules['estatusId'] = 'required|exists:'.CONST_BD_APP.'.estatus:id';
        if ( self::method() === 'POST' ) {
            if ( isset($_REQUEST['requisicionId']) ) $rules['requisicionId'] ='integer|exists:'.CONST_BD_APP.'.requisiciones:id';
            $rules['folio'] = 'integer|unique:'.CONST_BD_APP.'.ordencompra';
        }
        $rules['proveedorId'] ='integer|required|exists:'.CONST_BD_APP.'.proveedores:id';
        $rules['condicionPagoId'] ='integer|required';
        $rules['monedaId'] ='integer|required';
        $rules['importeControl'] ='decimal|required';
        $rules['direccion'] ='string';
        $rules['especificaciones'] ='string';
        if ( isset($_REQUEST['observacion']) ) $rules['observacion'] = 'required|string|max:100';
        $rules['retencionIva'] = 'decimal';
        $rules['retencionIsr'] = 'decimal';
        $rules['descuento'] = 'decimal|max:14';
        $rules['iva'] = 'decimal';

        return $rules;
    }

    static public function messages()
    {
        return [
            'folio.integer' => 'El folio debe ser un número entero',
            'folio.unique' => 'El folio ya existe',
            'direccion.string' => 'La dirección debe ser una cadena de texto',
            'especificaciones.string' => 'Las especificaciones deben ser una cadena de texto',
            'retencionIva.decimal' => 'La retención de IVA debe ser un número decimal',
            'retencionIsr.decimal' => 'La retención de ISR debe ser un número decimal',
            'descuento.decimal' => 'El descuento debe ser un número decimal',
            'descuento.max' => 'El descuento debe tener un máximo de 14 caracteres',
            'iva.decimal' => 'El IVA debe ser un número decimal',
            'estatusId.integer' => 'El estatus debe ser un número entero',
            'estatusId.required' => 'El estatus es requerido',
            'estatusId.exists' => 'El estatus no existe',
            'observacion.required' => 'La observación es requerida',
            'observacion.string' => 'La observación debe ser una cadena de texto',
            'observacion.max' => 'La observación debe tener un máximo de 100 caracteres',
            'proveedorId.integer' => 'El proveedor debe ser un número entero',
            'proveedorId.required' => 'El proveedor es requerido',
            'proveedorId.exists' => 'El proveedor no existe',
            'requisicionId.integer' => 'La requisición debe ser un número entero',
            'requisicionId.exists' => 'La requisición no existe',
            'condicionPagoId.integer' => 'La condición de pago debe ser un número entero',
            'condicionPagoId.required' => 'La condición de pago es requerida',
            'monedaId.integer' => 'La moneda debe ser un número entero',
            'monedaId.required' => 'La moneda es requerida',
            'importeControl.decimal' => 'El importe de control debe ser un número decimal',
            'importeControl.required' => 'El importe de control es requerido'
        ];
    }

    static public function validated($id = null) {
        return self::validating(OrdenCompra::fillable(), self::rules($id), self::messages());
    }
}
