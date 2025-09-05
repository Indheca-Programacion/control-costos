<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\OperadorMaquinaria;

class SaveOperadorMaquinariasRequest extends Request
{
    static public function rules($id)
    {
            $rules = [ 'sNombre' => 'required'];

        return $rules;
    }

    static public function messages()
    {
        return [
            'sNombre.required' => 'La nombre del operador es obligatoria.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(OperadorMaquinaria::fillable(), self::rules($id), self::messages());
    }
}
