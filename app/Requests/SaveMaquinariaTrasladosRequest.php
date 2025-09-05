<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\MaquinariaTraslado;

class SaveMaquinariaTrasladosRequest extends Request
{
    static public function rules($id)
    {

        $rules = [
                    'sModelo' => 'required|string|max:100',
                    'sPlaca' => 'required|string|max:100',
                    'sMarca' => 'required|string|max:100',
                    'sYear' => 'required|string|max:100',
                    'sCapacidad' => 'required|string|max:100',
                    'sNumeroEconomico' => 'string|max:100',
                    'nIdOperador03Operador' => 'required|integer|exists:'.CONST_BD_SECURITY.'.COSTOS_03_OPERADOR:nId03Operador',
                    'condiciones' => 'required|string|max:100',
                    'arrendadorEquipo' => 'required|string|max:100',
                ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'condiciones.required' => 'El campo condiciones es requerido',
            'condiciones.string' => 'El campo condiciones debe ser una cadena de texto',
            'condiciones.max' => 'El campo condiciones no debe exceder los 100 caracteres',

            'arrendadorEquipo.required' => 'El campo condiciones es requerido',
            'arrendadorEquipo.string' => 'El campo condiciones debe ser una cadena de texto',
            'arrendadorEquipo.max' => 'El campo condiciones no debe exceder los 100 caracteres',

            'sModelo.required' => 'El campo modelo es requerido',
            'sModelo.string' => 'El campo modelo debe ser una cadena de texto',
            'sModelo.max' => 'El campo modelo no debe exceder los 100 caracteres',
            'sPlaca.required' => 'El campo placa es requerido',
            'sPlaca.string' => 'El campo placa debe ser una cadena de texto',
            'sPlaca.max' => 'El campo placa no debe exceder los 100 caracteres',
            'sMarca.required' => 'El campo marca es requerido',
            'sMarca.string' => 'El campo marca debe ser una cadena de texto',
            'sMarca.max' => 'El campo marca no debe exceder los 100 caracteres',
            'sYear.required' => 'El campo año es requerido',
            'sYear.string' => 'El campo año debe ser una cadena de texto',
            'sYear.max' => 'El campo año no debe exceder los 100 caracteres',
            'sCapacidad.required' => 'El campo capacidad es requerido',
            'sCapacidad.string' => 'El campo capacidad debe ser una cadena de texto',
            'sCapacidad.max' => 'El campo capacidad no debe exceder los 100 caracteres',
            'sNumeroEconomico.required' => 'El campo numero economico es requerido',
            'sNumeroEconomico.string' => 'El campo numero economico debe ser una cadena de texto',
            'sNumeroEconomico.max' => 'El campo numero economico no debe exceder los 100 caracteres',
            'nIdOperador03Operador.required' => 'El campo operador es requerido',
            'nIdOperador03Operador.integer' => 'El campo operador debe ser un número entero',
            'nIdOperador03Operador.exists' => 'El operador seleccionado no existe',

        ];
    }

    static public function validated($id = null) {
        return self::validating(MaquinariaTraslado::fillable(), self::rules($id), self::messages());
    }
}
