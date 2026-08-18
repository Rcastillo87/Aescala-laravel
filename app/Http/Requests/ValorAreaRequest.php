<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

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
            $año = $this->input('select_año');
            
            // 1. Obtener todos los valores min y max que ya están en la BD para ese año
            // Excluimos los IDs que se están enviando en el request (en caso de edición)
            $idsEnRequest = array_filter(array_column($items, 'id'));
            
            $valoresExistentes = DB::table('valor_area')
                ->where('año', $año)
                ->whereNotIn('id', $idsEnRequest)
                ->get()
                ->flatMap(function ($item) {
                    return [$item->area_min, $item->area_max];
                })
                ->toArray();

            $valoresProcesados = []; // Para detectar duplicados dentro del mismo request

            foreach ($items as $index => $item) {
                if (isset($item['area_min'], $item['area_max'])) {
                    $min = (int) $item['area_min'];
                    $max = (int) $item['area_max'];

                    // Regla A: El máximo debe ser mayor al mínimo
                    if ($max <= $min) {
                        $validator->errors()->add(
                            "items.$index.area_max",
                            'El área máxima debe ser mayor al área mínima.'
                        );
                    }

                    // Regla B: Validar el valor mínimo (no repetirse en BD ni dentro del request)
                    if (in_array($min, $valoresExistentes) || in_array($min, $valoresProcesados)) {
                        $validator->errors()->add(
                            "items.$index.area_min",
                            "El valor mínimo ($min) ya está registrado o se repite para este año."
                        );
                    }

                    // Regla C: Validar el valor máximo (no repetirse en BD ni dentro del request)
                    if (in_array($max, $valoresExistentes) || in_array($max, $valoresProcesados)) {
                        $validator->errors()->add(
                            "items.$index.area_max",
                            "El valor máximo ($max) ya está registrado o se repite para este año."
                        );
                    }

                    // Guardar en el historial local para contrastar con los siguientes ítems
                    $valoresProcesados[] = $min;
                    $valoresProcesados[] = $max;
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