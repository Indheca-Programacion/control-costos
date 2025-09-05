<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Sucursal;

class SaveSucursalesRequest extends Request
{
	static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = ['empresaId' => 'required|exists:empresas:id'];
        } 
        // else {
        //     $rules = ['descripcion' => 'required|string|max:100|unique:empresas:id:' . $id];
        // }

        $rules['descripcion'] = 'required|string|max:80';
        $rules['nombreCorto'] = 'required|string|max:20';
        $rules['domicilioFiscal'] = 'value:on';
        $rules['calle'] = 'required|string|max:80';
        $rules['noExterior'] = 'string|max:30';
        $rules['noInterior'] = 'string|max:20';
        $rules['colonia'] = 'string|max:50';
        $rules['localidad'] = 'string|max:80';
        $rules['referencia'] = 'string|max:80';
        $rules['municipio'] = 'required|string|max:80';
        $rules['estado'] = 'required|string|max:50';
        $rules['pais'] = 'required|string|max:50';
        $rules['codigoPostal'] = 'required|min:5|max:5';

        return $rules;
    }

    static public function messages()
    {
        return [
            'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no existe.',
            'descripcion.required' => 'La descripción de la Sucursal es obligatoria.',
            'descripcion.string' => 'La descripción debe ser de tipo String.',
            'descripcion.max' => 'La descripción debe ser máximo de 80 caracteres.',
            // 'descripcion.unique' => 'Esta descripción ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
            'domicilioFiscal.value' => 'Selección inválida para el campo Domicilio Fiscal.',
            'calle.required' => 'La calle es obligatoria.',
            'calle.string' => 'La calle debe ser de tipo String.',
            'calle.max' => 'La calle debe ser máximo de 80 caracteres.',
            // 'noExterior.required' => 'El número exterior es obligatorio.',
            'noExterior.string' => 'El número exterior debe ser de tipo String.',
            'noExterior.max' => 'El número exterior debe ser máximo de 30 caracteres.',
            'noInterior.string' => 'El número interior debe ser de tipo String.',
            'noInterior.max' => 'El número interior debe ser máximo de 20 caracteres.',
            // 'colonia.required' => 'La colonia es obligatoria.',
            'colonia.string' => 'La colonia debe ser de tipo String.',
            'colonia.max' => 'La colonia debe ser máximo de 50 caracteres.',
            // 'localidad.required' => 'La localidad es obligatorio.',
            'localidad.string' => 'La localidad debe ser de tipo String.',
            'localidad.max' => 'La localidad debe ser máximo de 80 caracteres.',
            // 'referencia.required' => 'La referencia es obligatorio.',
            'referencia.string' => 'La referencia debe ser de tipo String.',
            'referencia.max' => 'La referencia debe ser máximo de 80 caracteres.',
            'municipio.required' => 'El municipio es obligatorio.',
            'municipio.string' => 'El municipio debe ser de tipo String.',
            'municipio.max' => 'El municipio debe ser máximo de 80 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.string' => 'El estado debe ser de tipo String.',
            'estado.max' => 'El estado debe ser máximo de 50 caracteres.',
            'pais.required' => 'El país es obligatorio.',
            'pais.string' => 'El país debe ser de tipo String.',
            'pais.max' => 'El país debe ser máximo de 50 caracteres.',
            'codigoPostal.required' => 'El codigo postal es obligatorio.',
            'codigoPostal.min' => 'El codigo postal debe ser mínimo de 5 dígitos.',
            'codigoPostal.max' => 'El codigo postal debe ser máximo de 5 dígitos.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Sucursal::fillable(), self::rules($id), self::messages());
    }
}
