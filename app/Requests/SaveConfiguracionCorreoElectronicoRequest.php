<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ConfiguracionCorreoElectronico;

class SaveConfiguracionCorreoElectronicoRequest extends Request
{
    static public function rules($id)
    {
        $rules = [];

        $rules['servidor'] = 'required|string|max:100';
        $rules['puerto'] = 'required|integer|min:3|max:4';
        $rules['puertoSSL'] = 'value:on';
        $rules['usuario'] = 'required|string|max:100';
        $rules['contrasena'] = 'string|max:20';
        $rules['visualizacionCorreo'] = 'required|email|max:100';
        $rules['visualizacionNombre'] = 'required|string|max:100';
        $rules['respuestaCorreo'] = 'email|max:100';
        $rules['respuestaNombre'] = 'string|max:100';
        $rules['comprobacionCorreo'] = 'email|max:100';

        return $rules;
    }

    static public function messages()
    {
        return [
            'servidor.required' => 'El servidor es obligatorio.',
            'servidor.string' => 'El servidor debe ser de tipo String.',
            'servidor.max' => 'El servidor debe ser máximo de 100 caracteres.',
            'puerto.required' => 'El puerto es obligatorio.',
            'puerto.integer' => 'El puerto debe ser de tipo Numérico.',
            'puerto.min' => 'El puerto debe ser mínimo de 3 dígitos.',
            'puerto.max' => 'El puerto debe ser máximo de 4 dígitos.',
            'puertoSSL.value' => 'Selección inválida para el campo Puerto SSL.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.string' => 'El usuario debe ser de tipo String.',
            'usuario.max' => 'El usuario debe ser máximo de 100 caracteres.',
            // 'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.string' => 'La contraseña debe ser de tipo String.',
            'contrasena.max' => 'La contraseña debe ser máximo de 20 caracteres.',
            'visualizacionCorreo.required' => 'El correo de visualización es obligatorio.',
            'visualizacionCorreo.email' => 'El correo de visualización debe ser de tipo Email.',
            'visualizacionCorreo.max' => 'El correo de visualización debe ser máximo de 100 caracteres.',
            'visualizacionNombre.required' => 'El nombre de visualización es obligatorio.',
            'visualizacionNombre.string' => 'El nombre de visualización debe ser de tipo String.',
            'visualizacionNombre.max' => 'El nombre de visualización debe ser máximo de 100 caracteres.',
            // 'respuestaCorreo.required' => 'El correo de respuesta es obligatorio.',
            'respuestaCorreo.email' => 'El correo de respuesta debe ser de tipo Email.',
            'respuestaCorreo.max' => 'El correo de respuesta debe ser máximo de 100 caracteres.',
            // 'respuestaNombre.required' => 'El nombre de respuesta es obligatorio.',
            'respuestaNombre.string' => 'El nombre de respuesta debe ser de tipo String.',
            'respuestaNombre.max' => 'El nombre de respuesta debe ser máximo de 100 caracteres.',
            // 'comprobacionCorreo.required' => 'El correo de comprobación es obligatorio.',
            'comprobacionCorreo.email' => 'El correo de comprobación debe ser de tipo Email.',
            'comprobacionCorreo.max' => 'El correo de comprobación debe ser máximo de 100 caracteres.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ConfiguracionCorreoElectronico::fillable(), self::rules($id), self::messages());
    }
}
