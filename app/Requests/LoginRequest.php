<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

class LoginRequest extends Request
{
	static public function rules($id)
    {   
        $rules = [
            'usuario' => 'required|string',
            'contrasena' => 'required|string'
        ];


        return $rules;
    }

    static public function messages()
    {
        return [
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.string' => 'El usuario debe ser de tipo String.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.string' => 'La contraseña debe ser de tipo String.'
        ];
    }

    static public function validated($id = null) {        
        $fillable = [
            'usuario', 'contrasena'
        ];
        return self::validating($fillable, self::rules($id), self::messages());
    }
}
