<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Plantilla;

class SavePlantillasRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            'descripcion' => 'required|string|max:250',
            'nombreCorto' => 'required|string|max:80'
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombreCorto.required' => 'El nombre del permiso es obligatorio.',
            'nombreCorto.string' => 'El nombre debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre debe ser máximo de 20 caracteres.',
            'descripcion.required' => 'La descripción del permiso es obligatorio.',
            'descripcion.string' => 'La descripción debe ser de tipo String.',
            'descripcion.max' => 'La descripción debe ser máximo de 80 caracteres.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Plantilla::fillable(), self::rules($id), self::messages());
    }
}