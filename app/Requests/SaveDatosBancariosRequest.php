<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}


use App\Models\DatosBancarios;

class SaveDatosBancariosRequest extends Request
{
	static public function rules($id)
    {
        $rules['nombreTitular'] = 'required|string|max:100';
        $rules['nombreBanco'] = 'required|string|max:50';
        $rules['cuenta'] = 'string|max:30';
        $rules['cuentaClave'] = 'string|max:30';

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombreTitular.string' => 'El nombre del titular debe ser una cadena de texto.',
            'nombreTitular.max' => 'El nombre del titular no debe exceder los 100 caracteres.',
            'nombreTitular.required' => 'El nombre del titular es obligatoria.',
            'nombreTitular.max' => 'El nombre del titular debe ser menor a 50 caracteres.',
            'nombreBanco.required' => 'El nombre del banco es obligatorio.',
            'nombreBanco.max' => 'El nombre del titular debe ser menor a 50 caracteres.',
            'cuenta.required' => 'La cuenta es obligatoria.',
            'cuenta.max' => 'La cuenta debe ser menor a 16 numeros.',
            'cuentaClave.required' => 'La cuenta clave es obligatoria.',
            'cuentaClave.max' => 'La cuenta clave debe ser menor a 20 numeros.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(DatosBancarios::fillable(), self::rules($id), self::messages());
    }
}
