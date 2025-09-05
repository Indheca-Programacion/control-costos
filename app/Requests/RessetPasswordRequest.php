<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

class RessetPasswordRequest extends Request
{
	static public function rules($id)
    {   
        $rules = [
            'correo' => 'required|email',
            'contrasena' => 'required',
            'confirmar-contrasena' => 'required'
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe ser de tipo Email.',
        ];
    }

    static public function validated($id = null) {        
        $fillable = [
            'correo',
            'contrasena',
            'confirmar-contrasena'
        ];
        return self::validating($fillable, self::rules($id), self::messages());
    }
}
