<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Puesto;

class SavePuestosRequest extends Request
{
    static public function rules($id)
    {
                $rules = [ 
                       'nombreCorto' => 'required|string|max:50|unique:'.CONST_BD_APP.'.puestos',
                       'descripcion' => 'required'
                    ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombre.required' => 'El nombre del Puesto es obligatorio.',
            'nombre.string' => 'El nombre debe ser de tipo String.',
            'nombre.max' => 'El nombre debe ser máximo de 50 caracteres.',
            'nombre.unique' => 'Este nombre ya ha sido registrado.',
            'descripcion.required' => 'La descripción del Puesto es obligatorio.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Puesto::fillable(), self::rules($id), self::messages());
    }
}
