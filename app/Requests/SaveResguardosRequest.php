<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Resguardo;

class SaveResguardosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 
                        'fechaAsignacion' => 'required|date',
                        'obra' => 'required|integer|exists:'.CONST_BD_APP.'.obras:id',
                        'inventario' => 'required|integer|exists:'.CONST_BD_APP.'.inventarios:id',                        
                    ];
        }
        $rules["cantidad"] = 'required|decimal|minValue:1';
        $rules["usuarioRecibio"] = 'required|integer|exists:'.CONST_BD_APP.'.empleados:id';
        $rules["observaciones"] = 'string';
        $rules["estatus"] = 'required|integer';
        
        return $rules;
    }

    static public function messages()
    {
        return [
            'estatus.required' => 'El estatus es obligatorio.',
            'empresa.required' => 'La empresa es obligatoria.',
            'obra.required' => 'La obra es obligatoria.',
            'obra.integer' => 'La obra debe de ser valor entero.',
            'obra.exists' => 'La obra seleccionada no existe.',
            'inventario.required' => 'El inventario es obligatorio.',
            'inventario.integer' => 'El inventario debe de ser valor entero.',
            'inventario.exists' => 'El inventario seleccionado no existe.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.decimal' => 'La cantidad debe de ser de valor decimal.',
            'fechaAsignacion.required' => 'La fecha es obligatoria.',
            'fechaAsignacion.date' => 'La fecha no es valida.',
            'usuarioRecibio.required' => 'El usuario es obligatorio.',
            'usuarioRecibio.integer' => 'El usuario debe de ser valor entero.',
            'usuarioRecibio.exists' => 'El usuario seleccionado no existe.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Resguardo::fillable(), self::rules($id), self::messages());
    }
}
