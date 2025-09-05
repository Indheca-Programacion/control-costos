<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Plantilla;

class SavePlantillaDetallesRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            'fk_plantilla' => 'required|integer',
            'presupuesto' => 'required|max:250',
            'cantidad' => 'required|max:80',
            'indirectoId' => 'required|integer',
            'directoId' => 'required|integer',
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'presupuesto.required' => 'El presupuesto es obligatorio.',
            'presupuesto.float' => 'El presupuesto debe ser de tipo decimal.',
            'cantidad.required' => 'La cantidad es obligatorio.',
            'cantidad.float' => 'La cantidad debe ser de tipo decimal.',
            'indirectoId.required' => 'La cantidad es obligatorio.',
            'directoId.required' => 'La cantidad es obligatorio.',

        ];
    }

    static public function validated($id = null) {
        return self::validating(Plantilla::fillable(), self::rules($id), self::messages());
    }
}