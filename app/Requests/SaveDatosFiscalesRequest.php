<?php

namespace App\Requests;

require_once "app/Requests/RequestProveedores.php";

use App\Models\DatosFiscal;

class SaveDatosFiscalesRequest extends RequestProveedores
{    
	static public function rules($id)
    {

        $rules['empresa'] = 'string';
        $rules['nombre'] = 'string';
        $rules['apellidoPaterno'] = 'string';
        $rules['apellidoMaterno'] = 'string';
        $rules['telefono'] = 'string';
        $rules['correo'] = 'string';
        $rules['condicionContado'] = 'string';
        $rules['condicionCredito'] = 'string';
        $rules['ubicacion'] = 'string';
        $rules['tiempoEntrega'] = 'string';
        $rules['modalidadEntrega'] = 'string';
        $rules['distribuidorAutorizado'] = 'string';
        $rules['recursos'] = 'string';

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombre.required' => 'La empresa es obligatoria.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(DatosFiscal::fillable(), self::rules($id), self::messages());
    }
}
