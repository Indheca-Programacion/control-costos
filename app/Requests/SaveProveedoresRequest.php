<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Proveedor;

class SaveProveedoresRequest extends Request
{    
	static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [
                'personaFisica' => 'value:on',
                'nombre' => 'required|string|max:40|uniqueFields:'.CONST_BD_APP.'.proveedores:apellidoPaterno:apellidoMaterno',
                'razonSocial' => 'required|string|max:100|unique:'.CONST_BD_APP.'.proveedores'
            ];
        } else {
            $rules = [
                'nombre' => 'required|string|max:40|uniqueFields:'.CONST_BD_APP.'.proveedores:apellidoPaterno:apellidoMaterno:id:' . $id,
                'razonSocial' => 'required|string|max:100|unique:'.CONST_BD_APP.'.proveedores:id:' . $id
            ];
        }

        $rules['estrellas'] = 'required|integer';
        $rules['zona'] = 'required|integer';
        $rules['domicilio'] = 'required|max:200';
        $rules['activo'] = 'value:on';
        $rules['apellidoPaterno'] = 'required|string|max:40';
        $rules['apellidoMaterno'] = 'string|max:40';
        $rules['nombreComercial'] = 'string|max:100';
        $rules['telefono'] = 'string|max:10';
        // $rules['rfc'] = 'required|string|min:12|max:13';
        $rules['rfc'] = 'string|min:12|max:20';
        $rules['correo'] = 'email|max:100';
        $rules['credito'] = 'value:on';
        $rules['limiteCredito'] = 'decimal|max:13';

        return $rules;
    }

    static public function messages()
    {
        return [
            'zona.required' => 'La zona del Proveedor es obligatoria.',
            'domicilio.required' => 'El domicilio del Proveedor es obligatorio.',

            'zona.integer' => 'La zona debe ser de tipo Integer.',
            'estrellas.value' => 'Selección inválida para el campo Estrellas.',
            'telefono.max' => 'El teléfono debe ser máximo de 10 caracteres.',
            'activo.value' => 'Selección inválida para el campo Activo.',
            'personaFisica.value' => 'Selección inválida para el campo Persona Física.',
            'nombre.required' => 'El nombre del Proveedor es obligatorio.',
            'nombre.string' => 'El nombre debe ser de tipo String.',
            'nombre.max' => 'El nombre debe ser máximo de 40 caracteres.',
            'nombre.uniqueFields' => 'Este nombre ya ha sido registrado.',
            'apellidoPaterno.required' => 'El apellido paterno del Proveedor es obligatorio.',
            'apellidoPaterno.string' => 'El apellido paterno debe ser de tipo String.',
            'apellidoPaterno.max' => 'El apellido paterno debe ser máximo de 40 caracteres.',
            'apellidoMaterno.string' => 'El apellido materno debe ser de tipo String.',
            'apellidoMaterno.max' => 'El apellido materno debe ser máximo de 40 caracteres.',
            'razonSocial.required' => 'La razón social del Proveedor es obligatoria.',
            'razonSocial.string' => 'La razón social debe ser de tipo String.',
            'razonSocial.max' => 'La razón social debe ser máximo de 100 caracteres.',
            'razonSocial.unique' => 'Esta razón social ya ha sido registrada.',
            'nombreComercial.string' => 'El nombre comercial debe ser de tipo String.',
            'nombreComercial.max' => 'El nombre comercial debe ser máximo de 100 caracteres.',
            'rfc.required' => 'El RFC del Proveedor es obligatorio.',
            'rfc.string' => 'El RFC debe ser de tipo String.',
            'rfc.min' => 'El RFC debe ser mínimo de 12 caracteres.',
            'rfc.max' => 'El RFC debe ser máximo de 13 caracteres.',
            // 'rfc.unique' => 'Este RFC ya ha sido registrado.'
            'correo.email' => 'El correo electrónico debe ser de tipo Email.',
            'correo.max' => 'El correo electrónico debe ser máximo de 100 caracteres.',
            'credito.value' => 'Selección inválida para el campo Crédito.',
            'limiteCredito.decimal' => 'El límite de crédito debe ser de tipo Decimal.',            
            'limiteCredito.max' => 'El límite de crédito debe ser máximo de 13 dígitos.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Proveedor::fillable(), self::rules($id), self::messages());
    }
}
