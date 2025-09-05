<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Tarea;

class SaveTareaRequest extends Request
{
	static public function rules($id)
    {
        $rules['descripcion'] = 'required|string|max:200';
        $rules['fk_usuario'] = 'required|integer';
        $rules['fecha_inicio'] = 'required|date';
        $rules['fecha_limite'] = 'required|date';

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion de la tarea es obligatoria.',
            'descripcion.max' => 'La descripcion de la tarea debe ser menor a 200 caracteres.',
            'fk_usuario.required' => 'El responsable es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatorio.',
            'fecha_limite.required' => 'La fecha limite es obligatorio.',

        ];
    }

    static public function validated($id = null) {
        return self::validating(Tarea::fillable(), self::rules($id), self::messages());
    }
}
