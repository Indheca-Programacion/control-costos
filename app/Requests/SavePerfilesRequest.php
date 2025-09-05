<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Perfil;

class SavePerfilesRequest extends Request
{
	static public function rules($id)
    {
        $rules = ['descripcion' => 'required|string|max:80'];

        if ( self::method() === 'POST' )
        {
            $rules['nombre'] = 'required|string|max:20|unique:perfiles';
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombre.required' => 'El nombre del perfil es obligatorio.',
            'nombre.string' => 'El nombre debe ser de tipo String.',
            'nombre.max' => 'El nombre debe ser máximo de 20 caracteres.',
            'nombre.unique' => 'Este nombre ya ha sido registrado.',
            'descripcion.required' => 'La descripción del perfil es obligatorio.',
            'descripcion.string' => 'La descripción debe ser de tipo String.',
            'descripcion.max' => 'La descripción debe ser máximo de 80 caracteres.'
        ];
    }

    static public function validated($id = null) {
        // return self::value();
        return self::validating(Perfil::fillable(), self::rules($id), self::messages());
    }
}