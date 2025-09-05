<?php

namespace App\Requests;

require_once "app/Requests/Request.php";
require_once "app/Models/FormularioProveedor.php";


use App\Models\FormularioProveedor;

class SaveFormularioProveedorRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            "razonSocial" => 'required|string|max:100',
            "rfc" => 'required|string|max:100',
            "correoElectronico" => 'required|email|max:100',
            "nombreApellido" => 'required|string|max:100',
            "telefono" => 'required|integer|min:10|max:10',

            "origenProveedor" => 'required|string|max:100',
            "tipoProveedor" => 'required|string|max:100',
            "claveProveedor" => 'required|string|max:100',
            "entregaMaterial" => 'required|string|max:100',

            "diasCredito" => 'required|integer',
            
            "terminosCondiciones" => "accepted",
            "tipoProveedor" => "accepted",
            "claveProveedor" => "accepted",

            "constanciaFiscal" => 'requiredArchivo',
            "opinionCumplimiento" => 'requiredArchivo',
            "comprobanteDomicilio" => 'requiredArchivo',
            "datosBancarios" => 'requiredArchivo',

        ];

       return $rules;
    }

    static public function messages()
    {

        return [
                'razonSocial.required' => 'La razón social es obligatoria.',
                'razonSocial.string' => 'La razón social debe ser de tipo texto.',
                'razonSocial.max' => 'La razón social no debe exceder los 100 caracteres.',

                'rfc.required' => 'El RFC es obligatorio.',
                'rfc.string' => 'El RFC debe ser de tipo texto.',
                'rfc.max' => 'El RFC no debe exceder los 100 caracteres.',

                'correoElectronico.required' => 'El correo electrónico es obligatorio.',
                'correoElectronico.email' => 'El correo electrónico debe contener un @.',
                'correoElectronico.max' => 'El correo electrónico no debe exceder los 100 caracteres.',

                'nombreApellido.required' => 'El nombre y apellido es obligatorio.',
                'nombreApellido.string' => 'El nombre y apellido debe ser de tipo texto.',
                'nombreApellido.max' => 'El nombre y apellido no debe exceder los 100 caracteres.',

                'telefono.required' => 'El teléfono es obligatorio.',
                'telefono.integer' => 'El teléfono debe ser un número entero.',
                'telefono.min' => 'El teléfono debe tener al menos 10 dígitos.',
                'telefono.max' => 'El teléfono no debe exceder los 10 dígitos.',

                'origenProveedor.required' => 'El origen del proveedor es obligatorio.',
                'origenProveedor.string' => 'El origen del proveedor debe ser de tipo texto.',
                'origenProveedor.max' => 'El origen del proveedor no debe exceder los 100 caracteres.',

                'tipoProveedor.required' => 'El tipo de proveedor es obligatorio.',
                'tipoProveedor.string' => 'El tipo de proveedor debe ser de tipo texto.',
                'tipoProveedor.max' => 'El tipo de proveedor no debe exceder los 100 caracteres.',

                'claveProveedor.required' => 'La clave del proveedor es obligatoria.',
                'claveProveedor.string' => 'La clave del proveedor debe ser de tipo texto.',
                'claveProveedor.max' => 'La clave del proveedor no debe exceder los 100 caracteres.',

                'entregaMaterial.required' => 'El campo entrega de material es obligatorio.',
                'entregaMaterial.string' => 'El campo entrega de material debe ser de tipo texto.',
                'entregaMaterial.max' => 'El campo entrega de material no debe exceder los 100 caracteres.',

                'diasCredito.required' => 'Los días de crédito son obligatorios.',
                'diasCredito.integer' => 'Los días de crédito deben ser un número entero.',

                'terminosCondiciones.accepted' => 'Debe aceptar los términos y condiciones.',
                'tipoProveedor.accepted' => 'El tipo de proveedor es requerido.',
                'claveProveedor.accepted' => 'La clabe del proveedor es requerida.',

                'constanciaFiscal.requiredArchivo' => 'La constancia de identidad fiscal del proveedor es requerida.',
                'opinionCumplimiento.requiredArchivo' => 'La opinión de cumplimiento proveedor es requerida.',
                'comprobanteDomicilio.requiredArchivo' => 'El comprobante de domicilio del proveedor es requerida.',
                'datosBancarios.requiredArchivo' => 'La carta con los datos bancarios del proveedor es requerida.',

        ];
    }

    static public function validated($id = null) {
        return self::validating(FormularioProveedor::fillable(), self::rules($id), self::messages());
    }
}
