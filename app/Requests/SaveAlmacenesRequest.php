<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Almacen;

class SaveAlmacenesRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'nombre' => 'required|string|max:100|unique:'.CONST_BD_APP.'.almacenes', 
                       'nombreCorto' => 'string|max:20|unique:'.CONST_BD_APP.'.almacenes' ];
        } else {
            $rules = [ 'nombre' => 'required|string|max:100|unique:'.CONST_BD_APP.'.almacenes:id:' . $id, 
                       'nombreCorto' => 'string|max:20|unique:'.CONST_BD_APP.'.almacenes:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombre.required' => 'La descripcion del almacen es obligatoria.',
            'nombre.string' => 'La descripcion debe ser de tipo String.',
            'nombre.max' => 'La descripcion debe ser máximo de 100 caracteres.',
            'nombre.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'La nombre corto es obligatoria.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Almacen::fillable(), self::rules($id), self::messages());
    }
}
