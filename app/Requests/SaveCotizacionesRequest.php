<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/PuestoUsuario.php";

} else {
    require_once "../Requests/Request.php";
    require_once "../Models/PuestoUsuario.php";
}

use App\Models\Cotizacion;

class SaveCotizacionesRequest extends Request
{
	static public function rules($id)
    {   
        $rules = [
            'fechaLimite' => 'required|datetime',
        ];
        return $rules;
    }

    static public function messages()
    {
        return [
            'proveedorId.required' => 'El campo proveedor es requerido.',
            'proveedorId.string' => 'El campo proveedor debe ser un texto.',
            'fechaLimite.datetime' => 'El campo fecha limite no es una fecha valida.',
        ];
    }

    static public function validated($id = null) {        
        return self::validating(Cotizacion::fillable(), self::rules($id), self::messages());
    }
}
