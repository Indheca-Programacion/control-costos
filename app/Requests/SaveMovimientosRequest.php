<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Movimientos;

class SaveMovimientosRequest extends Request
{
    static public function rules($id)
    {
        
        $rules= [ 
                    'nIdObra' => 'required|integer|exists:obras:id',
                    'nTipo' => 'required',
                    'nEstatus' => 'required',
                    'nIdMaquinariaTraslado02MaquinariaTraslado' => 'required|integer',
                ];


        return $rules;
    }

    static public function messages()
    {
        return [
            'nIdObra.required' => 'El campo Obra es requerido',
            'nIdObra.integer' => 'El campo Obra debe ser un número entero',
            'nIdObra.exists' => 'El campo Obra no existe en la base de datos',
            'nTipo.required' => 'El campo Tipo es requerido',
            'nEstatus.required' => 'El campo Estatus es requerido',
            'nIdMaquinariaTraslado02MaquinariaTraslado.required' => 'El campo Maquinaria Traslado es requerido',
            'nIdMaquinariaTraslado02MaquinariaTraslado.integer' => 'El campo Maquinaria Traslado debe ser un número entero',
            'nIdMaquinariaTraslado02MaquinariaTraslado.exists' => 'El campo Maquinaria Traslado no existe en la base de datos',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Movimientos::fillable(), self::rules($id), self::messages());
    }
}
