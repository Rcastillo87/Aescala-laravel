<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class saveValorAreaRequest extends FormRequest
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
            'items.*.id' => ['nullable', 'integer', 'exists:valor_area,id'],
            'items.*.area_min' => ['required', 'integer', 'min:0'],
            'items.*.area_max' => ['required', 'integer', 'min:0'],
            'items.*.valor_intervalo' => ['required', 'integer', 'min:0'],
            'items.*.descripccion' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {

                // area_max > area_min
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

    public function messages(): array
    {
        return [
            'items.required' => 'Debe existir al menos un intervalo.',
            'items.*.area_min.required' => 'Área mínima es obligatoria.',
            'items.*.area_max.required' => 'Área máxima es obligatoria.',
            'items.*.valor_intervalo.required' => 'El valor del intervalo es obligatorio.',
            'items.*.area_min.integer' => 'Área mínima debe ser un número entero.',
            'items.*.area_max.integer' => 'Área máxima debe ser un número entero.',
            'items.*.valor_intervalo.integer' => 'El valor debe ser un número entero.',
            'items.*.area_min.min' => 'Área mínima no puede ser negativa.',
            'items.*.area_max.min' => 'Área máxima no puede ser negativa.',
            'items.*.valor_intervalo.min' => 'El valor no puede ser negativo.',
        ];
    }
}
