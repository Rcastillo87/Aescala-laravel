<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\InventarioUnidad;
use Illuminate\Validation\Rule;

class InsertInventarioMaterialRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_material' => 'required|string',
            'cantidad' => 'required|numeric',
            'tipo' => 'nullable|in:Obra Blanca,Carpinteria',
            'cantidad_min' => 'required|numeric',
            'valor_unidad' => 'required|integer',
            'id_unidad' => [
                'required',
                'integer',
                Rule::exists('inventario_unidades', 'id'),
            ],
            'descripccion' => 'required|string',
            'activo' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'nombre_material.required' => 'El nombre del material es obligatorio.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un valor numérico.',
            'tipo.in' => 'El tipo solo puede ser "Obra Blanca" o "Carpinteria".',
            'cantidad_min.required' => 'La cantidad mínima es obligatoria.',
            'cantidad_min.numeric' => 'La cantidad mínima debe ser un valor numérico.',
            'valor_unidad.required' => 'El valor por unidad es obligatorio.',
            'valor_unidad.integer' => 'El valor por unidad debe ser un número entero.',
            'id_unidad.exists' => 'La unidad no existe.',
            'descripccion.required' => 'La descripción es obligatoria.',
            'activo.required' => 'El estado activo es obligatorio.',
            'activo.boolean' => 'El estado activo debe ser verdadero o falso.',
        ];
    }
}