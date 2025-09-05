<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Requisicion;

class SaveRequisicionRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [
                        'folio' => 'required|integer|uniqueKeys:requisiciones:fk_idObra:'.$id,
                        'periodo' => 'required|integer',
                        'divisa' => 'required'
                    ];
        }
        if ( isset($_POST['observacion']) ) $rules['observacion'] = 'required|string|max:100';
        $rules['tipoRequisicion'] = 'required|integer';
        $rules['fechaRequerida'] = 'required|date';
        $rules['direccion'] = 'string|max:250';
        $rules['especificaciones'] = 'string|max:250';
        $rules['justificacion'] = 'string|max:250';
        
        return $rules;
    }

    static public function messages()
    {
        return [
            'justificacion.string' => 'La justificación debe ser una cadena de texto.',
            'justificacion.max' => 'La justificación no debe exceder los 250 caracteres.',
            'direccion.string' => 'La dirección debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no debe exceder los 250 caracteres.',
            'especificaciones.string' => 'Las especificaciones deben ser una cadena de texto.',
            'especificaciones.max' => 'Las especificaciones no deben exceder los 250 caracteres.',
            'tipoRequisicion.required' => 'El tipo de requisición es obligatorio.',
            'tipoRequisicion.integer' => 'El tipo de requisición debe ser un número entero.',
            'fechaRequerida.required' => 'La fecha requerida es obligatoria.',
            'fechaRequerida.date' => 'La fecha requerida debe ser una fecha válida.',
            'folio.required' => 'El folio es obligatorio.',
            'folio.integer' => 'El folio debe ser un número entero.',
            'folio.uniqueKeys' => 'El folio ya existe, seleccione otro folio',
            'periodo.required' => 'El periodo es obligatorio.',
            'periodo.integer' => 'El periodo debe ser un número entero.',
            'divisa.required' => 'La divisa es obligatoria.',
            'divisa.integer' => 'La divisa debe ser un número entero.',
            'observacion.required' => 'La observación es obligatoria.',
            'observacion.string' => 'La observación debe ser una cadena de texto.',
            'observacion.max' => 'La observación no debe exceder los 100 caracteres'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Requisicion::fillable(), self::rules($id), self::messages());
    }
}
