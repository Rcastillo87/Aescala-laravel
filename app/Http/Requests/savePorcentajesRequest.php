<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class savePorcentajesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Año importado (opcional)
            'select_año' => ['nullable', 'integer', 'digits:4'],

            // Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:config_porcentajes,id'],
            'items.*.concepto' => ['required', 'string', 'max:255'],
            'items.*.porcentage' => ['required', 'integer', 'min:0', 'max:100'],
            'items.*.descripccion' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Calcular suma de porcentajes
            $sumaPorcentajes = collect($this->items)->sum('porcentage');
            
            if ($sumaPorcentajes !== 100) {
                $validator->errors()->add(
                    'porcentaje_total',
                    "La suma total de porcentajes debe ser 100%. Actual: {$sumaPorcentajes}%"
                );
            }
            
            // También puedes agregar validaciones adicionales aquí
            // Por ejemplo, verificar que no haya duplicados en los conceptos
            $conceptos = collect($this->items)->pluck('concepto')->map('strtolower');
            if ($conceptos->duplicates()->isNotEmpty()) {
                $validator->errors()->add(
                    'conceptos_duplicados',
                    'No puede haber conceptos duplicados'
                );
            }
        });
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Debe agregar al menos un concepto.',
            'items.*.concepto.required' => 'El concepto es obligatorio.',
            'items.*.porcentage.required' => 'El porcentaje es obligatorio.',
            'items.*.porcentage.integer' => 'El porcentaje debe ser un número entero.',
            'items.*.porcentage.min' => 'El porcentaje no puede ser negativo.',
            'items.*.porcentage.max' => 'El porcentaje no puede ser mayor a 100%.',
            'items.*.descripccion.max' => 'La descripción no puede exceder los 500 caracteres.',
        ];
    }
}