<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\TareaObservaciones;

class SaveTareaObservacionesRequest extends Request
{
	static public function rules($id)
    {
        
        $rules['observacion'] = 'required|string|max:200';

        return $rules;
    }

    static public function messages()
    {
        return [
            'observacion.required' => 'Debe agregar una observacion',

        ];
    }

    static public function validated($id = null) {
        return self::validating(TareaObservaciones::fillable(), self::rules($id), self::messages());
    }
}
