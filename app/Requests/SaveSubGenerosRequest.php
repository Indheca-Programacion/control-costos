<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\SubGenero;

class SaveSubGenerosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.subgeneros', 
                       'nombreCorto' => 'string|max:20' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.subgeneros:id:' . $id, 
                       'nombreCorto' => 'string|max:20' ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion del subgenero es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',

        ];
    }

    static public function validated($id = null) {
        return self::validating(SubGenero::fillable(), self::rules($id), self::messages());
    }
}
