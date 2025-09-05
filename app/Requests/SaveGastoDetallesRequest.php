<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\GastoDetalles;

class SaveGastoDetallesRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 'fecha' => 'required|date',
                'tipoGasto' => 'required|integer',
                'costo' =>'required|decimal|maxDecimal:13',
                'cantidad' =>'required|decimal|maxDecimal:13',
                'economico' =>'required|string|max:50',
                'solicito' =>'required|string',
                'proveedor' =>'required|string|max:250',
                'factura' =>'string|max:100',
                'obra' =>'required|integer',
                'obraDetalle' =>'required|integer',
                'observaciones' =>'string',
            ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'fecha.required' => 'La fecha es requerida.',
            'tipoGasto.required' => 'El tipo de gasto es requerido.',
            'costo.required' => 'El costo es requerido.',
            'cantidad.required' => 'La cantidad es requerida.',
            'economico.required' => 'El economico es requerido.',
            'solicito.required' => 'El solicitante es requerido.',
            'proveedor.required' => 'El proveedor es requerido.',
            'factura.required' => 'La factura es requerida.',
            'obra.required' => 'La obra es requerida.',
            'obraDetalle.required' => 'El detalle de la obra es requerido.',
            'observaciones.string' => 'Las observaciones deben ser un texto.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'tipoGasto.integer' => 'El tipo de gasto debe ser un número entero.',
            'costo.decimal' => 'El costo debe ser un número decimal.',
            'costo.maxDecimal' => 'El costo no puede ser mayor a 13 dígitos.',
            'cantidad.decimal' => 'La cantidad debe ser un número decimal.',
            'cantidad.maxDecimal' => 'La cantidad no puede ser mayor a 13 dígitos.',
            'economico.string' => 'El economico debe ser un texto.',
            'economico.max' => 'El economico no puede ser mayor a 50 caracteres.',
            'solicito.string' => 'El solicitante debe ser un texto.',
            'proveedor.string' => 'El proveedor debe ser un texto.',
            'proveedor.max' => 'El proveedor no puede ser mayor a 250 caracteres.',
            'factura.string' => 'La factura debe ser un texto.',
            
        ];
    }

    static public function validated($id = null) {
        return self::validating(GastoDetalles::fillable(), self::rules($id), self::messages());
    }
}
