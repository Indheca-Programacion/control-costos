<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/PuestoSuperiorInferior.php";

} else {
    require_once "../Requests/Request.php";
    require_once "../Models/PuestoSuperiorInferior.php";
}

use App\Models\PuestoSuperiorInferior;

class SavePuestoSuperiorInferiorRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 

            ];
     
        return $rules;
    }

    static public function messages()
    {
        return [
            
        ];
    }

    static public function validated($id = null) {
        return self::validating(PuestoSuperiorInferior::fillable(), self::rules($id), self::messages());
    }
}
