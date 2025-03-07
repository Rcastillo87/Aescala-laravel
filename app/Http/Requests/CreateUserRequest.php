<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rol;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_completo' => 'required|string',
            'correo' => 'required|email|unique:users,correo',
            'pass' => 'required',
            'cedula' => 'required',
            'telefono' => 'required|string',
            'direccion' => 'required',
            'activo' => 'required|boolean',
            'id_rol' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Solo se admiten correos válidos.',
            'correo.unique' => 'El correo ya está en uso.',
            'pass.required' => 'La contraseña es obligatoria.',
            'cedula.required' => 'La cédula es obligatoria.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'activo.required' => 'El estado activo es obligatorio.',
            'id_rol.exists' => 'El rol no existe.',
        ];
    }
}