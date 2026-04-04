<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;


class ValorAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('configuracion.saveValorArea');
    }

    public function rules(): array
    {
        return [
            'select_año' => ['nullable', 'integer', 'digits:4'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:valor_area,id'],
            'items.*.area_min' => ['required', 'integer', 'min:0'],
            'items.*.area_max' => ['required', 'integer', 'min:0'],
            'items.*.valor_intervalo' => ['required', 'integer', 'min:0'],
            'items.*.descripccion' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Validación adicional
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                if (
                    isset($item['area_min'], $item['area_max']) &&
                    $item['area_max'] <= $item['area_min']
                ) {
                    $validator->errors()->add(
                        "items.$index.area_max",
                        'El área máxima debe ser mayor al área mínima.'
                    );
                }
            }
        });
    }

    /**
     * Nombres amigables para mensajes estándar
     */
    public function attributes(): array
    {
        return [
            'select_año' => 'Año',

            'items' => 'Intervalos',
            'items.*.area_min' => 'Área mínima',
            'items.*.area_max' => 'Área máxima',
            'items.*.valor_intervalo' => 'Valor del intervalo',
            'items.*.descripccion' => 'Descripción',
        ];
    }
}
