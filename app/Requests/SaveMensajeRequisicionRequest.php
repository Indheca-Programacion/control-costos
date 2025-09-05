<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/MensajeRequisicion.php";

} else {
    require_once "../Requests/Request.php";
    require_once "../Models/MensajeRequisicion.php";
}

use App\Models\MensajeRequisicion;

class SaveMensajeRequisicionRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 
                'mensaje' => 'required|max:30', 
                'idRequisicion' => 'required', 
            ];
     
        return $rules;
    }

    static public function messages()
    {
        return [
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.max' => 'El mensaje debe ser máximo de 30 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(MensajeRequisicion::fillable(), self::rules($id), self::messages());
    }
}
