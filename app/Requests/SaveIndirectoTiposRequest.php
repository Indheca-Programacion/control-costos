<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\IndirectoTipo;

class SaveIndirectoTiposRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'numero' => 'required|string|max:10|unique:'.CONST_BD_APP.'.indirecto_tipos',
                       'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.indirecto_tipos',
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.indirecto_tipos' ];
        } else {
            $rules = [ 'numero' => 'required|string|max:10|unique:'.CONST_BD_APP.'.indirecto_tipos:id:' . $id,
                       'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.indirecto_tipos:id:' . $id,
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.indirecto_tipos:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'numero.required' => 'El campo número del Tipo de Indirecto es obligatorio.',
            'numero.string' => 'El campo número debe ser de tipo String.',
            'numero.max' => 'El campo número debe ser máximo de 10 caracteres.',
            'numero.unique' => 'Este número ya ha sido registrado.',
            'descripcion.required' => 'La descripcion del Tipo de Indirecto es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto del Tipo de Indirecto es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(IndirectoTipo::fillable(), self::rules($id), self::messages());
    }
}
