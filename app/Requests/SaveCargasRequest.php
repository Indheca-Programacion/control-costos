<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/Carga.php";
    
} else {
    require_once "../Requests/Request.php";
    require_once "../Models/Carga.php";

}

use App\Models\Carga;

class SaveCargasRequest extends Request
{
    static public function rules($id)
    {

        $rules = [ 
                'idObra' =>  'required', 
                'idMaquinaria' => 'required', 
                'idMaterial' => 'required', 
                'nPeso' => 'required', 
                'sFolio' => 'required', 
                'dFechaHora' => 'required', 
                ];

        return $rules;
    }

    static public function messages()
    {
        return [

        ];
    }

    static public function validated($id = null) {
        return self::validating(Carga::fillable(), self::rules($id), self::messages());
    }

}