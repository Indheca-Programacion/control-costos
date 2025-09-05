<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Nominas;

class SaveNominasRequest extends Request
{
    static public function rules($id)
    {
        
        $rules= [ 
                    'obraId' => 'required',
                    'semana' => 'required',
                ];


        return $rules;
    }

    static public function messages()
    {
        return [
            'semana.required' => 'La semana es requerida',
            'semana.min' => 'Escoja una semana',
            'obraId.required' => 'La obra es requerida.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Nominas::fillable(), self::rules($id), self::messages());
    }
}
