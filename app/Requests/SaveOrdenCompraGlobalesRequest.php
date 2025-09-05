<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\OrdenCompraGlobales;

class SaveOrdenCompraGlobalesRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];
        if ( isset($_REQUEST['estatusId']) ) $rules['estatusId'] = 'required|exists:'.CONST_BD_APP.'.estatus:id';
        if ( self::method() === 'POST' ) {
            if ( isset($_REQUEST['requisicionId']) ) $rules['requisicionId'] ='integer|required|exists:'.CONST_BD_APP.'.requisiciones:id';
            $rules['folio'] = 'integer|required|unique:'.CONST_BD_APP.'.ordencompraglobal';
        }
        $rules['proveedorId'] ='integer|required|exists:'.CONST_BD_APP.'.proveedores:id';
        $rules['condicionPagoId'] ='integer|required';
        $rules['monedaId'] ='integer|required';
        $rules['importeControl'] ='decimal|required';

        $rules['direccion'] ='string';
        $rules['especificaciones'] ='string';
        $rules['justificacion'] ='string';

        $rules['tipoRequisicion'] = 'required';

        if ( isset($_REQUEST['observacion']) ) $rules['observacion'] = 'required|string|max:100|required';
        $rules['retencionIva'] = 'decimal|required';
        $rules['retencionIsr'] = 'decimal|required';
        $rules['descuento'] = 'decimal|max:14|required';
        $rules['iva'] = 'decimal|required';
        $rules['categoriaId'] = 'required';

        return $rules;
    }

    static public function messages()
    {
        return [
            'folio.integer' => 'El folio debe ser un número entero',
            'folio.unique' => 'El folio ya existe',
            'folio.required' => 'El folio es requerida',

            'direccion.string' => 'La dirección debe ser una cadena de texto',
            'direccion.required' => 'La dirección es requerido',

            'especificaciones.string' => 'Las especificaciones deben ser una cadena de texto',
            'especificaciones.required' => 'Las especificaciones  es requerida',
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
            'importeControl.required' => 'El importe de control es requerido',
            'justificacion.required' => 'La justificación es requerido',
            'categoriaId.required' => 'La categoria es requerido',
            'tipoRequisicion.required' => 'El tipo de requisición es requerido'
        ];
    }

    static public function validated($id = null) {
        return self::validating(OrdenCompraGlobales::fillable(), self::rules($id), self::messages());
    }
}
