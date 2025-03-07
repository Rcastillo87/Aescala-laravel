<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Proveedor;
use App\Models\InventarioMaterial;
use Illuminate\Validation\Rule;

class InsertPedidoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_proyecto' => 'nullable|integer',
            'codigo' => 'required|string',
            'fecha' => 'required|date_format:Y-m-d',
            'id_proveedor' => [
                'required',
                'integer',
                Rule::exists('proveedores', 'id'),
            ],
            'pedidos' => 'required|array',
            'pedidos.*.id_material' => [
                'required',
                'integer',
                Rule::exists('inventario_materiales', 'id'),
            ],
            'pedidos.*.cantidad' => 'required|numeric|min:1',
            'pedidos.*.vr_unidad' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date_format' => 'La fecha debe tener el formato YYYY-MM-DD.',
            'id_proveedor.exists' => 'El proveedor no existe.',
            'pedidos.required' => 'Los pedidos son obligatorios.',
            'pedidos.*.id_material.exists' => 'El material no existe.',
            'pedidos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'pedidos.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'pedidos.*.vr_unidad.required' => 'El valor por unidad es obligatorio.',
            'pedidos.*.vr_unidad.min' => 'El valor por unidad debe ser mayor a 0.',
        ];
    }
}