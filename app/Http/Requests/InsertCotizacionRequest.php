<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Proyecto;
use App\Models\InventarioMaterial;
use Illuminate\Validation\Rule;

class InsertCotizacionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cantidad' => 'required|integer',
            'valor_unidad' => 'required|numeric',
            'id_proyecto' => [
                'required',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'id_inventario' => [
                'required',
                'integer',
                Rule::exists('inventario_materiales', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'valor_unidad.required' => 'El valor por unidad es obligatorio.',
            'valor_unidad.numeric' => 'El valor por unidad debe ser un valor numérico.',
            'id_proyecto.exists' => 'El proyecto no existe.',
            'id_inventario.exists' => 'El material no existe.',
        ];
    }
}