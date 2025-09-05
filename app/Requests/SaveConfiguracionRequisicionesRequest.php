<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ConfiguracionRequisicion;

class SaveConfiguracionRequisicionesRequest extends Request
{
    static public function rules($id)
    {
        $rules = [];

        $rules['inicialEstatusId'] = 'required|exists:'.CONST_BD_APP.'.estatus:id';
        $rules['usuarioCreacionEliminarPartidas'] = 'value:on';

        return $rules;
    }

    static public function messages()
    {
        return [
            'inicialEstatusId.required' => 'El estatus inicial es obligatorio.',
            'inicialEstatusId.exists' => 'El estatus inicial seleccionado no existe.',
            'usuarioCreacionEliminarPartidas.value' => 'Selección inválida para el campo Eliminar partidas.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ConfiguracionRequisicion::fillable(), self::rules($id), self::messages());
    }
}
