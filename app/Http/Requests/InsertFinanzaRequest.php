<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Proyecto;
use Illuminate\Validation\Rule;

class InsertFinanzaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipo' => 'nullable|in:Ingreso_abono,Gasto_Carpinteria,Gasto_Obra_Blanca,Gasto_Otros',
            'concepto' => 'required|string',
            'valor' => 'required|integer',
            'id_proyecto' => [
                'required',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'tipo.in' => 'El tipo solo puede ser "Ingreso_abono", "Gasto_Carpinteria", "Gasto_Obra_Blanca" o "Gasto_Otros".',
            'concepto.required' => 'El concepto es obligatorio.',
            'valor.required' => 'El valor es obligatorio.',
            'valor.integer' => 'El valor debe ser un número entero.',
            'id_proyecto.exists' => 'El proyecto no existe.',
        ];
    }
}