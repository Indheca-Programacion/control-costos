<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\ProgramacionPagos;

class SaveProgramacionPagosRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            "fecha_programada" => "required",
            "prioridad" => "required|integer|min:1",
            "tipo" => "required",
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            "fecha_programada.required" => "La fecha programada es obligatoria.",
            "prioridad.required" => "La prioridad es obligatoria.",
            "prioridad.integer" => "La prioridad debe ser un número entero.",
            "prioridad.min" => "La prioridad debe ser al menos 1.",
            "tipo.required" => "El tipo de pago es obligatorio.",
        ];
    }

    static public function validated($id = null) {
        return self::validating(ProgramacionPagos::fillable(), self::rules($id), self::messages());
    }
}