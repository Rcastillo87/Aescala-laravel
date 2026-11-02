<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveCobrosPlanillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('planilla.saveCobros');
    }

    public function rules(): array
    {
        return [
            'id_proyecto'             => ['required', 'integer', 'exists:proyectos,id'],
            'cobros'                  => ['required', 'array'],
            'cobros.*'                => ['array'],
            'cobros.*.*.valor_cobrar' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'cobros.*.*.aprobacion'   => ['sometimes', 'nullable', 'integer', Rule::in([0, 50, 100])],
            'cobros.*.*.fecha_pago'   => ['sometimes', 'nullable', 'date_format:Y-m-d'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cobros'                  => 'Cobros',
            'cobros.*.*.valor_cobrar' => 'Valor a cobrar',
            'cobros.*.*.aprobacion'   => 'Aprobación',
            'cobros.*.*.fecha_pago'   => 'Fecha de pago',
        ];
    }
}