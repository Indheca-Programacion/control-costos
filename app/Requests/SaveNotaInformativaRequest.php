<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\NotaInformativa;

class SaveNotaInformativaRequest extends Request
{
    static public function rules($id)
    {
        
        $rules= [ 
                    'lugar' => 'required|string|max:255',
                    'fecha' => 'required|date',
                    'descripcion' => 'required|string|max:1000',
                    'requisicionId' => 'required|integer|exists:requisiciones:id',
                ];


        return $rules;
    }

    static public function messages()
    {
        return [
            'lugar.required' => 'El campo lugar es obligatorio.',
            'lugar.string' => 'El campo lugar debe ser una cadena de texto.',
            'lugar.max' => 'El campo lugar no puede exceder los 255 caracteres.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'fecha.date' => 'El campo fecha debe ser una fecha válida.',
            'descripcion.required' => 'El campo descripción es obligatorio.',
            'descripcion.string' => 'El campo descripción debe ser una cadena de texto.',
            'descripcion.max' => 'El campo descripción no puede exceder los 1000 caracteres.',
            'requisicionId.required' => 'El campo requisición es obligatorio.',
            'requisicionId.integer' => 'El campo requisición debe ser un número entero.',
            'requisicionId.exists' => 'La requisición seleccionada no existe.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(NotaInformativa::fillable(), self::rules($id), self::messages());
    }
}
