<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Estado;
use App\Models\User;
use Illuminate\Validation\Rule;

class InsertProyectoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_proyecto' => 'required|string',
            'codigo_proyecto' => 'required|string|unique:proyectos,codigo_proyecto',
            'departamento' => 'required|string',
            'ciudad' => 'required|string',
            'direccion' => 'required|string',
            'nombre_cliente' => 'required|string',
            'telefono_cliente' => 'required|string',
            'val_obra_blanca' => 'required|integer',
            'val_obra_blanca_materiales' => 'required|integer',
            'val_obra_carpinteria' => 'required|integer',
            'val_carpinteria_materiales' => 'required|integer',
            'pres_otros' => 'required|integer',
            'observacion' => 'nullable|string',
            'fec_inicio' => 'required|date_format:Y-m-d',
            'fec_fin_estimado' => 'required|date_format:Y-m-d',
            'fec_fin_real' => 'nullable|date_format:Y-m-d',
            'id_estado' => [
                'required',
                'integer',
                Rule::exists('estados', 'id'),
            ],
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre_proyecto.required' => 'El nombre del proyecto es obligatorio.',
            'codigo_proyecto.required' => 'El código del proyecto es obligatorio.',
            'codigo_proyecto.unique' => 'El código del proyecto ya está en uso.',
            'departamento.required' => 'El departamento es obligatorio.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'direccion.required' => 'La dirección es obligatoria.',
            'nombre_cliente.required' => 'El nombre del cliente es obligatorio.',
            'telefono_cliente.required' => 'El teléfono del cliente es obligatorio.',
            'val_obra_blanca.required' => 'El valor de obra blanca es obligatorio.',
            'val_obra_blanca_materiales.required' => 'El valor de materiales de obra blanca es obligatorio.',
            'val_obra_carpinteria.required' => 'El valor de carpintería es obligatorio.',
            'val_carpinteria_materiales.required' => 'El valor de materiales de carpintería es obligatorio.',
            'pres_otros.required' => 'El valor de otros es obligatorio.',
            'fec_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fec_inicio.date_format' => 'La fecha de inicio debe tener el formato YYYY-MM-DD.',
            'fec_fin_estimado.required' => 'La fecha estimada de fin es obligatoria.',
            'fec_fin_estimado.date_format' => 'La fecha estimada de fin debe tener el formato YYYY-MM-DD.',
            'fec_fin_real.date_format' => 'La fecha real de fin debe tener el formato YYYY-MM-DD.',
            'id_estado.exists' => 'El estado no existe.',
            'id_user.exists' => 'El usuario no existe.',
        ];
    }
}