<?php

namespace App\Requests;

// require_once "app/Requests/Request.php";
if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Indirecto;

class SaveIndirectosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'numero' => 'required|string|max:10|unique:'.CONST_BD_APP.'.indirectos',
                       'descripcion' => 'required|string|max:255|unique:'.CONST_BD_APP.'.indirectos' ];
        } else {
            $rules = [ 'numero' => 'required|string|max:10|unique:'.CONST_BD_APP.'.indirectos:id:' . $id,
                       'descripcion' => 'required|string|max:255|unique:'.CONST_BD_APP.'.indirectos:id:' . $id ];
        }

        $rules['indirectoTipoId'] = 'required|exists:'.CONST_BD_APP.'.indirecto_tipos:id';
        $rules['unidadId'] = 'required|exists:'.CONST_BD_APP.'.unidades:id';

        return $rules;
    }

    static public function messages()
    {
        return [
            'indirectoTipoId.required' => 'El tipo de indirecto es obligatorio.',
            'indirectoTipoId.exists' => 'El tipo de indirecto seleccionado no existe.',
            'numero.required' => 'El número del Indirecto es obligatorio.',
            'numero.string' => 'El número debe ser de tipo String.',
            'numero.max' => 'El número debe ser máximo de 10 caracteres.',
            'numero.unique' => 'Este número ya ha sido registrado.',
            'descripcion.required' => 'La descripcion del Indirecto es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 255 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'unidadId.required' => 'La unidad es obligatoria.',
            'unidadId.exists' => 'La unidad seleccionada no existe.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Indirecto::fillable(), self::rules($id), self::messages());
    }
}
