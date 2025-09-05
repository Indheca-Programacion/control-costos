<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\RequisicionPersonal;

class SaveRequisicionPersonalRequest extends Request
{
    static public function rules($id)
    {

        $rules['cantidad'] = 'required';
        $rules['salario_semanal'] = 'required';
        $rules['fecha_inicio'] = 'required';
        $rules['fecha_fin'] = 'required';
        $rules['costo_neto'] = 'required';
        
        return $rules;
    }

    static public function messages()
    {
        return [
            'otros.float' => 'Los otros costos debe ser numerico',
            'cantidad.required' => 'La cantidad es obligatorio.',
            'salario_semanal.required' => 'El salario es obligatorio.',
            'fecha_inicio.required' => 'La fecha inicial es obligatorio.',
            'fecha_fin.required' => 'La fecha de terminacion es obligatorio.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(RequisicionPersonal::fillable(), self::rules($id), self::messages());
    }
}
