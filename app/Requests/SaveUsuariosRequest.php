<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Usuario;

class SaveUsuariosRequest extends Request
{
	static public function rules($id)
    {   
        $rules = [
            'activo' => 'value:on',
            'nombre' => 'required|string|max:40',
            'apellidoPaterno' => 'required|string|max:40',
            'apellidoMaterno' => 'string|max:40',
            'correo' => 'required|email|max:100',
            'foto' => 'image:png:jpeg|maxSize:2000000',
            'firma' => 'image:png:jpeg|maxSize:2000000',
            'empresaId' => 'required|exists:'.CONST_BD_SECURITY.'.empresas:id',
            'ubicacionId' => 'required',
            'sello' => 'string|max:300',
            'prefijo_usuario' => 'string|max:10',

        ];

        if ( self::method() === 'POST' ) {

            $rules['usuario'] = 'required|string|max:40|unique:usuarios';
            $rules['contrasena'] = 'required|string|max:20';

        } else {

            $rules['contrasena'] = 'string|max:20';

        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'prefijo_usuario.string' => 'El prefijo del usuario debe ser de tipo String.',
            'prefijo_usuario.max' => 'El prefijo del usuario debe ser máximo de 10 caracteres.',
            'sello.string' => 'El sello debe ser de tipo String.',
            'sello.max' => 'El sello debe ser máximo de 300 caracteres.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.string' => 'El usuario debe ser de tipo String.',
            'usuario.max' => 'El usuario debe ser máximo de 40 caracteres.',
            'usuario.unique' => 'Este usuario ya ha sido registrado.',
            'activo.value' => 'Selección inválida para el campo Activo.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.string' => 'La contraseña debe ser de tipo String.',
            'contrasena.max' => 'La contraseña debe ser máximo de 20 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser de tipo String.',
            'nombre.max' => 'El nombre debe ser máximo de 40 caracteres.',
            'apellidoPaterno.required' => 'El apellido paterno es obligatorio.',
            'apellidoPaterno.string' => 'El apellido paterno debe ser de tipo String.',
            'apellidoPaterno.max' => 'El apellido paterno debe ser máximo de 40 caracteres.',
            'apellidoMaterno.string' => 'El apellido materno debe ser de tipo String.',
            'apellidoMaterno.max' => 'El apellido materno debe ser máximo de 40 caracteres.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser de tipo Email.',
            'correo.max' => 'El correo electrónico debe ser máximo de 100 caracteres.',
            'foto.image' => 'La imágen debe estar en formato PNG o JPG.',
            'foto.maxSize' => 'El tamaño de la imágen debe ser máximo de 2Mb.',
            'firma.image' => 'La firma debe estar en formato PNG o JPG.',
            'firma.maxSize' => 'El tamaño de la firma debe ser máximo de 2Mb.',
            // 'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no existe.',
            'empresaId.required' => 'La empresa es requerida.',
            'ubicacionId.required' => 'La ubicacion es requerida.'

        ];
    }

    static public function validated($id = null) {
        // return self::value();
        return self::validating(Usuario::fillable(), self::rules($id), self::messages());
    }
}
