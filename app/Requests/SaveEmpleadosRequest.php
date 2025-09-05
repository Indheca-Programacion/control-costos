<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Empleado;

class SaveEmpleadosRequest extends Request
{
	static public function rules($id)
    {   
        $rules = [
            'activo' => 'value:on',
            'nombre' => 'required|string|max:40',
            'apellidoPaterno' => 'required|string|max:40',
            'apellidoMaterno' => 'string|max:40',
            'correo' => 'email|max:100',
            'foto' => 'image:png:jpeg|maxSize:2000000',
            'rfc' => 'string|max:13',
            'cbanco' => 'integer',
            'curp' => 'string|max:18',
            'seguro' => 'integer|max:11'
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'cupr.max' => 'El curp debe ser máximo de 18 carácteres.',
            'seguro.max' => 'El seguro debe ser máximo de 11 carácteres.',
            'seguro.integer' => 'El seguro debe ser solo numeros.',
            'rfc.max' => 'El rfc debe ser máximo de 13 carácteres.',
            'activo.value' => 'Selección inválida para el campo Activo.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser de tipo String.',
            'nombre.max' => 'El nombre debe ser máximo de 40 caracteres.',
            'apellidoPaterno.required' => 'El apellido paterno es obligatorio.',
            'apellidoPaterno.string' => 'El apellido paterno debe ser de tipo String.',
            'apellidoPaterno.max' => 'El apellido paterno debe ser máximo de 40 caracteres.',
            'apellidoMaterno.string' => 'El apellido materno debe ser de tipo String.',
            'apellidoMaterno.max' => 'El apellido materno debe ser máximo de 40 caracteres.',
            'correo.email' => 'El correo electrónico debe ser de tipo Email.',
            'correo.max' => 'El correo electrónico debe ser máximo de 100 caracteres.',
            'foto.image' => 'La imágen debe estar en formato PNG o JPG.',
            'foto.maxSize' => 'El tamaño de la imágen debe ser máximo de 2Mb.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Empleado::fillable(), self::rules($id), self::messages());
    }
}