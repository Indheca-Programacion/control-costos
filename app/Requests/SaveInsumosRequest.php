<?php

namespace App\Requests;

// require_once "app/Requests/Request.php";
if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Insumo;

class SaveInsumosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'codigo' => 'required|string|max:20|unique:'.CONST_BD_APP.'.insumos',
            'descripcion' => 'required|string|max:255|unique:'.CONST_BD_APP.'.insumos' ];
            if($_POST["accion"] == "agregar"){
                $rules['cantidad'] = 'required|string:'.CONST_BD_APP.'.cantidad:id';
                $rules['presupuesto'] = 'required|string:'.CONST_BD_APP.'.presupuesto:id';
            }
        } else {
            $rules = [ 'codigo' => 'required|string|max:20|unique:'.CONST_BD_APP.'.insumos:id:' . $id,
                       'descripcion' => 'required|string|max:255|unique:'.CONST_BD_APP.'.insumos:id:' . $id ];
        }
        
        
        $rules['unidadId'] = 'required|exists:'.CONST_BD_APP.'.unidades:id';

        return $rules;
    }

    static public function messages()
    {
        return [
            'codigo.required' => 'El código del Insumo es obligatorio.',
            'codigo.string' => 'El código debe ser de tipo String.',
            'codigo.max' => 'El código debe ser máximo de 20 caracteres.',
            'codigo.unique' => 'Este código ya ha sido registrado.',
            'descripcion.required' => 'La descripcion del Insumo es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 255 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'unidadId.required' => 'La unidad es obligatoria.',
            'unidadId.exists' => 'La unidad seleccionada no existe.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'presupuesto.required' => 'El presupuesto es obligatorio.'

        ];
    }

    static public function validated($id = null) {
        return self::validating(Insumo::fillable(), self::rules($id), self::messages());
    }
}
