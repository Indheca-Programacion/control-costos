<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\InformacionTecnica;

class SaveInformacionTecnicaRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'titulo' => 'required|string|max:100|unique:'.CONST_BD_APP.'.informacion_tecnica',
                       'archivo' => 'requiredFile|type:application/msword:application/vnd.openxmlformats-officedocument.wordprocessingml.document:application/vnd.ms-excel:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet:application/pdf:image/jpeg:image/png|maxSize:4000000' ];
        } else {
            $rules = [ 'titulo' => 'required|string|max:100|unique:'.CONST_BD_APP.'.informacion_tecnica:id:' . $id,
                       'archivo' => 'type:application/msword:application/vnd.openxmlformats-officedocument.wordprocessingml.document:application/vnd.ms-excel:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet:application/pdf:image/jpeg:image/png|maxSize:4000000' ];
        }

        // $rules['archivo'] = 'requiredFile|type:application/msword:application/vnd.openxmlformats-officedocument.wordprocessingml.document:application/vnd.ms-excel:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet:application/pdf:image/jpeg:image/png|maxSize:4000000';
        // $rules['formato'] = 'required|string|max:255';
        // $rules['ruta'] = 'required|string|max:255';
        // $rules['tags'] = 'string|max:255';

        return $rules;
    }

    static public function messages()
    {
        return [
            'titulo.required' => 'El titulo de la Información Técnica es obligatorio.',
            'titulo.string' => 'El titulo debe ser de tipo String.',
            'titulo.max' => 'El titulo debe ser máximo de 100 caracteres.',
            'titulo.unique' => 'Este titulo ya ha sido registrado.',
            'archivo.requiredFile' => 'El archivo es obligatorio.',
            'archivo.type' => 'El archivo debe ser Word, Excel, PDF o Imágen.',
            'archivo.maxSize' => 'El tamaño del archivo debe ser máximo de 4Mb.'
            // 'formato.required' => 'El formato de la Información Técnica es obligatorio.',
            // 'formato.string' => 'El formato debe ser de tipo String.',
            // 'formato.max' => 'El formato debe ser máximo de 255 caracteres.',
            // 'ruta.required' => 'La ruta de la Información Técnica es obligatoria.',
            // 'ruta.string' => 'La ruta debe ser de tipo String.',
            // 'ruta.max' => 'La ruta debe ser máximo de 255 caracteres.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(InformacionTecnica::fillable(), self::rules($id), self::messages());
    }
}
