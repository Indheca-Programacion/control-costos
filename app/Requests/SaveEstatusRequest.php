<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Estatus;

class SaveEstatusRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.estatus', 
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.estatus' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.estatus:id:' . $id, 
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.estatus:id:' . $id ];
        }

        $rules['colorTexto'] = 'string|max:7';
        $rules['colorFondo'] = 'string|max:7';
        $rules['obraAbierta'] = 'value:on';
        $rules['obraCerrada'] = 'value:on';
        $rules['requisicionAbierta'] = 'value:on';
        $rules['requisicionCerrada'] = 'value:on';
        $rules['requisicionOrden'] = 'required|integer|max:4';
        $rules['requisicionAgregarPartidas'] = 'value:on';

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion del Estatus es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 30 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto del Estatus es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 10 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.',
            'colorTexto.string' => 'El color texto debe ser de tipo String.',
            'colorTexto.max' => 'El color texto debe ser máximo de 7 caracteres.',
            'colorFondo.string' => 'El color fondo debe ser de tipo String.',
            'colorFondo.max' => 'El color fondo debe ser máximo de 7 caracteres.',
            'obraAbierta.value' => 'Selección inválida para el campo Obra Abierta.',
            'obraCerrada.value' => 'Selección inválida para el campo Obra Cerrada.',
            'requisicionAbierta.value' => 'Selección inválida para el campo Requisicion Abierta.',
            'requisicionCerrada.value' => 'Selección inválida para el campo Requisicion Cerrada.',
            'requisicionOrden.required' => 'El campo orden es obligatorio.',
            'requisicionOrden.integer' => 'El campo orden debe ser de tipo Numérico.',
            'requisicionOrden.max' => 'El campo orden debe ser máximo de 4 dígitos.',
            'requisicionAgregarPartidas.value' => 'Selección inválida para el campo Permitir Agregar/Eliminar partidas.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Estatus::fillable(), self::rules($id), self::messages());
    }
}
