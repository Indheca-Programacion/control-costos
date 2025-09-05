<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Empresa;

class SaveEmpresasRequest extends Request
{
	static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'razonSocial' => 'required|string|max:100|unique:empresas',
                       'nomenclaturaOT' => 'required|string|max:5|unique:empresas' ];
        } else {
            $rules = [ 'razonSocial' => 'required|string|max:100|unique:empresas:id:' . $id,
                       'nomenclaturaOT' => 'required|string|max:5|unique:empresas:id:' . $id ];
        }

        $rules['nombreCorto'] = 'required|string|max:20';
        $rules['rfc'] = 'required|string|max:13';
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
        $rules['logo'] = 'image:png:jpeg|maxSize:2000000';
        $rules['imagen'] = 'image:png:jpeg|maxSize:2000000';

        return $rules;
    }

    static public function messages()
    {
        return [
            'razonSocial.required' => 'La razón social de la Empresa es obligatoria.',
            'razonSocial.string' => 'La razón social debe ser de tipo String.',
            'razonSocial.max' => 'La razón social debe ser máximo de 100 caracteres.',
            'razonSocial.unique' => 'Esta razón social ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
            'rfc.required' => 'El RFC es obligatorio.',
            'rfc.string' => 'El RFC debe ser de tipo String.',
            'rfc.max' => 'El RFC debe ser máximo de 13 caracteres.',
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
            'codigoPostal.max' => 'El codigo postal debe ser máximo de 5 dígitos.',
            'nomenclaturaOT.required' => 'La nomenclatura para la Orden de Trabajo es obligatoria.',
            'nomenclaturaOT.string' => 'La nomenclatura debe ser de tipo String.',
            'nomenclaturaOT.max' => 'La nomenclatura debe ser máximo de 5 caracteres.',
            'nomenclaturaOT.unique' => 'Esta nomenclatura ya ha sido registrada.',
            'logo.image' => 'El logo debe estar en formato PNG o JPG.',
            'logo.maxSize' => 'El tamaño del logo debe ser máximo de 2Mb.',
            'imagen.image' => 'La imágen debe estar en formato PNG o JPG.',
            'imagen.maxSize' => 'El tamaño de la imágen debe ser máximo de 2Mb.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Empresa::fillable(), self::rules($id), self::messages());
    }
}
