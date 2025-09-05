<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\MaterialCarga;

class SaveMaterialCargasRequest extends Request
{
    static public function rules($id)
    {
            $rules = [ 'sDescripcion' => 'required'];

        return $rules;
    }

    static public function messages()
    {
        return [
            'sDescripcion.required' => 'La descripcion del material es obligatoria.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(MaterialCarga::fillable(), self::rules($id), self::messages());
    }
}
