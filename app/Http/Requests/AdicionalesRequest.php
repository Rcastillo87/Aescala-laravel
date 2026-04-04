<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class AdicionalesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('configuracion.saveAdicionales');
    }

    public function rules(): array
    {
        return [
            'select_año' => ['nullable', 'integer', 'digits:4'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:config_adicionales_mo,id'],
            'items.*.producto' => ['required', 'string', 'max:255'],
            'items.*.valor_unidad' => ['required', 'integer', 'min:0'],
            'items.*.tipo' => ['required', 'integer', 'in:1,2'],
            'items.*.descripccion' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Nombres amigables para los campos
     */
    public function attributes(): array
    {
        return [
            'select_año' => 'Año',

            'items' => 'Registros',
            'items.*.producto' => 'Producto',
            'items.*.valor_unidad' => 'Valor por unidad',
            'items.*.tipo' => 'Tipo',
            'items.*.descripccion' => 'Descripción',
        ];
    }
}
