<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\InsumoTipo;

class SaveInsumoTiposRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.insumo_tipos', 
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.insumo_tipos' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.insumo_tipos:id:' . $id,
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.insumo_tipos:id:' . $id ];
        }

        $rules['orden'] = 'required|integer|max:4';

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion del Tipo de Insumo es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto del Tipo de Insumo es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.',
            'orden.required' => 'El campo orden es obligatorio.',
            'orden.integer' => 'El campo orden debe ser de tipo Numérico.',
            'orden.max' => 'El campo orden debe ser máximo de 4 dígitos.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(InsumoTipo::fillable(), self::rules($id), self::messages());
    }
}
