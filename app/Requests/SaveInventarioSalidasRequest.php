<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\InventarioSalida;

class SaveInventarioSalidasRequest extends Request
{
    static public function rules($id)
    {

        $rules = [
                    'ordenCompra' => 'required|string',
                    'almacen' => 'required|integer|exists:'.CONST_BD_SECURITY.'.almacenes:id',
                    'observaciones' => 'string',
                    'entrega' => 'required|string',
                    'usuarioIdRecibe' => 'integer|exists:'.CONST_BD_SECURITY.'.usuarios:id',
                ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'usuarioIdRecibe.integer' => 'El usuario que recibe debe ser de tipo Entero.',
            'usuarioIdRecibe.exists' => 'El usuario que recibe no existe.',
            'entrega.required' => 'La persona que entrega es obligatoria.',
            'ordenCompra.required' => 'La orden de compra es obligatoria.',
            'ordenCompra.string' => 'La orden de compra debe ser una cadena de caracteres.',
            'almacen.required' => 'El almacen es obligatorio.',
            'almacen.integer' => 'El almacen debe ser de tipo Entero.',
            'almacen.exists' => 'El almacen que ingreso no existe.',
            'observaciones.string' => 'Las observaciones debe ser una cadena de caracteres.',

        ];
    }

    static public function validated($id = null) {
        return self::validating(InventarioSalida::fillable(), self::rules($id), self::messages());
    }
}
