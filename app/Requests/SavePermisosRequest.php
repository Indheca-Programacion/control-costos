<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Permiso;

class SavePermisosRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            'descripcion' => 'required|string|max:80'
        ];

        if ( self::method() === 'POST' )
        {
            $rules['codigo'] = 'string|max:10|unique:permisos';
            $rules['nombre'] = 'required|string|max:20|unique:permisos';
        } else {
            $rules['codigo'] = 'string|max:10|unique:permisos:id:' . $id;
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'codigo.string' => 'El código debe ser de tipo String.',
            'codigo.max' => 'El código debe ser máximo de 10 caracteres.',
            'codigo.unique' => 'Este código ya ha sido registrado.',
            'nombre.required' => 'El nombre del permiso es obligatorio.',
            'nombre.string' => 'El nombre debe ser de tipo String.',
            'nombre.max' => 'El nombre debe ser máximo de 20 caracteres.',
            'nombre.unique' => 'Este nombre ya ha sido registrado.',
            'descripcion.required' => 'La descripción del permiso es obligatorio.',
            'descripcion.string' => 'La descripción debe ser de tipo String.',
            'descripcion.max' => 'La descripción debe ser máximo de 80 caracteres.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Permiso::fillable(), self::rules($id), self::messages());
    }
}