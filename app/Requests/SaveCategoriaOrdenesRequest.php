<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\CategoriaOrdenes;

class SaveCategoriaOrdenesRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            'descripcion' => 'required|string|max:80',
            'nombre' => 'required|string|max:20|unique:categoria_proveedores'
        ];
      
        return $rules;
    }

    static public function messages()
    {
        return [
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
        return self::validating(CategoriaOrdenes::fillable(), self::rules($id), self::messages());
    }
}