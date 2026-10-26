<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveConfigPlantillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('planilla.saveConfigPlantilla');
    }

    protected function prepareForValidation(): void
    {
        $conceptos = $this->input('conceptos');
        if (is_string($conceptos)) {
            $conceptos = $conceptos === '' ? null : json_decode($conceptos, true);
        }

        $valorConfig = $this->input('valor_config');
        if ($valorConfig === '') {
            $valorConfig = null;
        }

        $this->merge([
            'conceptos'    => is_array($conceptos) ? $conceptos : null,
            'valor_config' => $valorConfig,
        ]);
    }

    public function rules(): array
    {
        return [
            'id_proyecto' => 'required|exists:proyectos,id',
            'tipo' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5, 6])],

            'valor_config' => ['required_if:tipo,1,2,4,5,6', 'nullable', 'numeric', 'min:0'],

            'conceptos' => ['required_if:tipo,3', 'nullable', 'array', 'min:1'],
            'conceptos.*.concepto' => ['required_with:conceptos', 'string'],
            'conceptos.*.porcentage' => ['required_with:conceptos', 'numeric', 'min:0'],
            'conceptos.*.en_pesos' => ['required_with:conceptos', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo' => 'Tipo de Configuración',
            'valor_config' => 'Valor',
            'conceptos.*.porcentage' => 'Porcentaje',
        ];
    }
}