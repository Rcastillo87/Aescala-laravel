<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\InventarioMaterial;
use Illuminate\Validation\Rule;

class InventarioSolicitudRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipo' => 'nullable|in:Despachado,Devolucion',
            'codigo' => 'required|string',
            'id_proyecto' => [
                'required',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'data' => 'required|array',
            'data.*.valor_unidad' => 'required|integer',
            'data.*.cantidad' => 'required|numeric',
            'data.*.id_material' => [
                'required',
                'integer',
                Rule::exists('inventario_materiales', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'tipo.in' => 'El tipo solo puede ser "Despachado" o "Devolucion".',
            'codigo.required' => 'El código es obligatorio.',
            'id_proyecto.exists' => 'El proyecto no existe.',
            'id_user.exists' => 'El usuario no existe.',
            'data.required' => 'Los datos son obligatorios.',
            'data.*.valor_unidad.required' => 'El valor por unidad es obligatorio.',
            'data.*.valor_unidad.integer' => 'El valor por unidad debe ser un número entero.',
            'data.*.cantidad.required' => 'La cantidad es obligatoria.',
            'data.*.cantidad.numeric' => 'La cantidad debe ser un valor numérico.',
            'data.*.id_material.exists' => 'El material no existe.',
        ];
    }
}