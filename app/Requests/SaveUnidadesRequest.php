<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Unidad;

class SaveUnidadesRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.unidades', 
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.unidades' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.unidades:id:' . $id, 
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.unidades:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion de la Unidad es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 30 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto de la Unidad es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 10 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Unidad::fillable(), self::rules($id), self::messages());
    }
}
