<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/PuestoUsuario.php";

} else {
    require_once "../Requests/Request.php";
    require_once "../Models/PuestoUsuario.php";
}

use App\Models\PuestoUsuario;

class SavePuestoUsuarioRequest extends Request
{
	static public function rules($id)
    {   
        $rules = [
            'idUsuario' => 'required|string',
            'idPuesto' => 'required|string',
            'idUbicacion' => 'required|string',
        ];
        return $rules;
    }

    static public function messages()
    {
        return [
        ];
    }

    static public function validated($id = null) {        
        return self::validating(PuestoUsuario::fillable(), self::rules($id), self::messages());
    }
}
