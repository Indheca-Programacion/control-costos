<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Partida;

class SavePartidasRequest extends Request
{    
	static public function rules($id)
    {
        $rules['costo'] = 'required|decimal';
        $rules['costo_unitario'] = 'required|decimal';
        $rules['unidadId'] = 'required|integer|exists:'.CONST_BD_APP.'.unidades:id';
        $rules['cantidad'] = 'required|decimal|min:1';
        $rules['obraDetalleId'] = 'required|integer|exists:'.CONST_BD_APP.'.obra_detalles:id';
        $rules['concepto'] = 'required|string|max:1000';

        return $rules;
    }

    static public function messages()
    {
        return [
            'costo.required' => 'El costo es requerido.',
            'costo.decimal' => 'El costo debe ser un número decimal.',
            'costo_unitario.required' => 'El costo unitario es requerido.',
            'costo_unitario.decimal' => 'El costo unitario debe ser un número decimal.',
            'unidadId.required' => 'La unidad es requerida.',
            'unidadId.integer' => 'La unidad debe ser un número entero.',
            'unidadId.exists' => 'La unidad seleccionada no existe.',
            'cantidad.required' => 'La cantidad es requerida.',
            'cantidad.decimal' => 'La cantidad debe ser un número decimal.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'obraDetalleId.required' => 'El detalle de obra es requerido.',
            'obraDetalleId.integer' => 'El detalle de obra debe ser un número entero.',
            'obraDetalleId.exists' => 'El detalle de obra seleccionado no existe.',
            'concepto.required' => 'El concepto es requerido.',
            'concepto.string' => 'El concepto debe ser una cadena de texto.',
            'concepto.max' => 'El concepto no puede exceder los 1000 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Partida::fillable(), self::rules($id), self::messages());
    }
}
