<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Requisicion;

class SaveRequisicionesRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];

        if ( isset($_REQUEST['estatusId']) ) $rules['estatusId'] = 'required|exists:'.CONST_BD_APP.'.estatus:id';
        if ( isset($_REQUEST['observacion']) ) $rules['observacion'] = 'required|string|max:100';
        $rules['proveedor'] ='string|max:50';
        $rules['direccion'] ='string|max:200';
        $rules['fax'] ='string';
        $rules['telefono'] ='string';
        $rules['email'] ='string|max:30';
        $rules['folio'] = 'integer';
        $rules['categoriaId'] = 'required';


        return $rules;
    }

    static public function messages()
    {
        return [
            'categoriaId.required' => 'La categoría es obligatoria.',
            'proveedor.string' => 'El proveedor debe ser de tipo String.',
            'proveedor.max' => 'El proveedor debe ser máximo de 50 caracteres.',
            'direccion.string' => 'La dirección debe ser de tipo String.',
            'direccion.max' => 'La dirección debe ser máximo de 200 caracteres.',
            'fax.string' => 'El fax debe ser de tipo String.',
            'telefono.string' => 'El teléfono debe ser de tipo String.',
            'email.string' => 'El email debe ser de tipo String.',
            'email.max' => 'El email debe ser máximo de 30 caracteres.',
            'folio.integer' => 'El folio debe ser un número entero.',
            'folio.unique' => 'El folio ya existe, cierre y vuelva abrir la pestaña crear Requisicion.',
            'estatusId.required' => 'El estatus es obligatorio.',
            'estatusId.exists' => 'El estatus seleccionado no existe.',
            'estatusId.exists' => 'El estatus seleccionado no existe.',
            'observacion.required' => 'La Observación es obligatoria.',
            'observacion.string' => 'La Observación debe ser de tipo String.',
            'observacion.max' => 'La Observación debe ser máximo de 100 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Requisicion::fillable(), self::rules($id), self::messages());
    }
}
