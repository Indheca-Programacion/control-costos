<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Asistencias;

class SaveAsistenciasRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 'fk_empleado' => 'required|integer',
                        'horaEntrada' => 'required',
                        'horaSalida' => 'required',
                        'jornadas' =>'required'];

        return $rules;
    }

    static public function messages()
    {
        return [
            
        ];
    }

    static public function validated($id = null) {
        return self::validating(Asistencias::fillable(), self::rules($id), self::messages());
    }
}
