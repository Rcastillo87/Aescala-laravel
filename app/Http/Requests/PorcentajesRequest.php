<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class PorcentajesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('configuracion.savePorcentajes');
    }

    public function rules(): array
    {
        return [
            'select_año' => ['nullable', 'integer', 'digits:4'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:config_porcentajes,id'],
            'items.*.concepto' => ['required', 'string', 'max:255'],
            'items.*.porcentage' => ['required', 'integer', 'min:0', 'max:100'],
            'items.*.descripccion' => ['nullable', 'string', 'max:500'],
            'items.*.en_pesos' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validaciones adicionales
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            // Suma total = 100%
            $suma = collect($items)
                ->filter(fn ($item) => empty($item['en_pesos']))
                ->sum('porcentage');
                
            if ($suma !== 100) {
                $validator->errors()->add(
                    'porcentaje_total',
                    "La suma total de porcentajes debe ser 100%. Actual: {$suma}%"
                );
            }

            // Conceptos duplicados
            $conceptos = collect($items)->pluck('concepto')->map(fn ($v) => mb_strtolower($v));
            if ($conceptos->duplicates()->isNotEmpty()) {
                $validator->errors()->add(
                    'conceptos_duplicados',
                    'No puede haber conceptos duplicados.'
                );
            }
        });
    }

    /**
     * Nombres amigables para mensajes en español
     */
    public function attributes(): array
    {
        return [
            'select_año' => 'Año',

            'items' => 'Conceptos',
            'items.*.concepto' => 'Concepto',
            'items.*.porcentage' => 'Porcentaje',
            'items.*.descripccion' => 'Descripción',
            'items.*.en_pesos' => 'En Pesos',
        ];
    }
}
