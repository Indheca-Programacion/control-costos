<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Gastos;

class SaveGastosRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 'obra' => 'required|integer',
                'tipoGasto' => 'required|integer',
                'encargado' => 'required|integer',
                'fecha_inicio' =>'required|date',
                'fecha_fin' =>'required|date',
                'banco' =>'string',
                'cuenta' =>'string|max:12',
                'clave' =>'max:20',
            ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'obra.required' => 'La obra es obligatorio.',
            'tipoGasto.required' => 'El tipo de gasto es obligatorio.',
            'encargado.required' => 'El encargado es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de finalizacion es obligatoria.',
            'banco.required' => 'El banco es obligatorio.',
            'cuenta.required' => 'La cuenta es obligatoria.',
            'cuenta.max' => 'La cuenta debe ser máximo de 12 caracteres.',
            'clave.required' => 'La clave es obligatoria.',
            'clave.max' => 'La clave debe ser máximo de 20 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Gastos::fillable(), self::rules($id), self::messages());
    }
}
