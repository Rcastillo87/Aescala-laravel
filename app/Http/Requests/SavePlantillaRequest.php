<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\OtroSi;
use App\Models\PlanillaEntregables;

class SavePlantillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('planilla.savePlantilla');
    }

    public function rules(): array
    {
        return [
            'id_proyecto' => 'required|exists:proyectos,id',
            'entregables' => 'required|array|min:1',
            'entregables.*.id' => 'nullable|integer|exists:planilla_entregables,id',
            'entregables.*.id_area' => ['required','integer', Rule::in(array_keys(PlanillaEntregables::$txTipo))],
            'entregables.*.material' => 'required|string',
            'entregables.*.cantidad' => 'required|numeric|min:0',
            'entregables.*.valor_unitario' => 'required|integer',
            'entregables.*.unidad' => ['required','integer', Rule::in(array_keys(Otrosi::$unidades))],
        ];
    }

    /**
     * Nombres amigables para los campos
     */
    public function attributes(): array
    {
        return [
            'entregables.*.id_area' => 'Tipo de Entregable',
            'entregables.*.material' => 'Descripción',
            'entregables.*.cantidad' => 'Cantidad',
            'entregables.*.valor_unitario' => 'Valor Unitario',
            'entregables.*.unidad' => 'Unidad',
        ];
    }
}
