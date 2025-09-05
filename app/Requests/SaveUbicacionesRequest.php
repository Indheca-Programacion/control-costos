<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Ubicacion;

class SaveUbicacionesRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.ubicaciones', 
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.ubicaciones' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.ubicaciones:id:' . $id,
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.ubicaciones:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion de la Ubicación es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto de la Ubicación es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Ubicacion::fillable(), self::rules($id), self::messages());
    }
}
