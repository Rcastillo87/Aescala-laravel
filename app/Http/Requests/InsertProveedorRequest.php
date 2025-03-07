<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'razon_social' => 'required|string',
            'nit' => 'required|string',
            'direccion' => 'required|string',
            'telefono' => 'required|string',
            'activo' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'razon_social.required' => 'La razón social es obligatoria.',
            'nit.required' => 'El NIT es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'activo.required' => 'El estado activo es obligatorio.',
            'activo.boolean' => 'El estado activo debe ser verdadero o falso.',
        ];
    }
}