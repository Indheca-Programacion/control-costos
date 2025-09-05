<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\ObraDetalles;

class SaveObraDetallesRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'obraId' => 'required:'.CONST_BD_APP.'.obra_detalles',
                       'indirectoId' => 'required:'.CONST_BD_APP.'.obra_detalles',
                       'insumoId' => 'required:'.CONST_BD_APP.'.obra_detalles',
                       ];
        }
        $rules['cantidad'] = 'required:'.CONST_BD_APP.'.obra_detalles';
        $rules['presupuesto'] = 'required:'.CONST_BD_APP.'.obra_detalles' ;
        $rules['presupuesto_dolares'] = 'decimal';

        return $rules;
    }

    static public function messages()
    {
        return [
            'obraId.required' => 'La obra es obligatoria.',
            'indirectoId.required' => 'El indireco Id es obligatoria.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'presupuesto.required' => 'La presupuesto es obligatoria.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(ObraDetalles::fillable(), self::rules($id), self::messages());
    }
}
