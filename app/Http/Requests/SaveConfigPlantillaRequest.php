<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\PlanillaConfigProyecto;

class SaveConfigPlantillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('planilla.saveConfigPlantilla');
    }

    // 👇 esto es lo que probablemente falta
    protected function prepareForValidation(): void
    {
        $tipo = (int) $this->input('tipo');

        // Si conceptos llega como string (JSON), decodifícalo; si viene vacío, ponlo en null
        $conceptos = $this->input('conceptos');
        if (is_string($conceptos)) {
            $conceptos = $conceptos === '' ? null : json_decode($conceptos, true);
        }

        // Si valor_config llega vacío, ponlo en null
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
            'tipo' => ['required', 'integer', Rule::in(array_keys(PlanillaConfigProyecto::$txTipo))],

            'valor_config' => ['required_if:tipo,1,2', 'nullable', 'numeric', 'min:0'],

            'conceptos' => ['required_if:tipo,3', 'nullable', 'array', 'min:1'], // 👈 agregué nullable
            'conceptos.*.concepto' => ['required_with:conceptos', 'string'],
            'conceptos.*.porcentage' => ['required_with:conceptos', 'numeric', 'min:0'],
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