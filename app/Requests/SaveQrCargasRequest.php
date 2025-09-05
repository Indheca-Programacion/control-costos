<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\QrCarga;

class SaveQrCargasRequest extends Request
{
    static public function rules($id)
    {

        $rules = [ 
                'operadorId' => 'required', 
                'sMarca' => 'required', 
                'sModelo' => 'required', 
                'sYear' => 'required', 
                'sPlaca' => 'required', 
                'sCapacidad' => 'required', 
                'sNumeroEconomico' => 'string',

                'condiciones' => 'required',
                'arrendadorEquipo' => 'required',
                
                ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'condiciones.required' => 'La condición es obligatoria.',
            'arrendadorEquipo.required' => 'El nombre de la arrendadora es obligatoria.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(QrCarga::fillable(), self::rules($id), self::messages());
    }

}