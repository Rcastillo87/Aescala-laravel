<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\InventarioMaterial;

class SaveSolicitudRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitir siempre (o pon tu lógica)
    }

    public function rules()
    {
        return [
            'isAnalista' => ['required', 'integer', 'in:0,1'],

            'id_solicitud' => [
                'required',
                'integer',
                Rule::exists('solicitud_material', 'id'),
            ],

            'materiales' => ['required', 'array', 'min:1'],

            'materiales.*.id_material' => [
                'required',
                'integer',
                /*Rule::exists('inventario_materiales', 'id'),
                function ($attribute, $value, $fail) {
                    $material = InventarioMaterial::find($value);
                    if (!$material || $material->activo != 1) {
                        $fail('El material seleccionado no está disponible, recargue la página.');
                    }
                }*/
            ],

            'materiales.*.cantidad' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {

                    $index = explode('.', $attribute)[1];
                    $materialId = $this->input("materiales.{$index}.id_material");
                    $material = InventarioMaterial::find($materialId);

                    // Valor que mandas en el <input type="hidden" name="isAnalista" ... >
                    $isAnalista = (int) $this->input('isAnalista', 0);

                    // Si es analista → NO validar cantidad
                    if ($isAnalista === 1) {
                        return;
                    }

                    // Para despachos → validar stock
                    if ($material && $value > $material->cantidad) {
                        $fail("La cantidad para {$material->nombre_material} excede el stock ({$material->cantidad}).");
                    }
                }
            ],

            'materiales.*.cancelo' => ['required', 'in:0,1'],
            'materiales.*.cobro' => ['nullable', 'in:0,1'],
            'materiales.*.nota_aprobacion' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'materiales.required' => 'Debe incluir al menos un material.',
            'materiales.*.cancelo.boolean' => 'El campo cancelar debe ser válido.',
        ];
    }
}
