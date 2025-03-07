<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Herramienta;
use App\Models\User;
use Illuminate\Validation\Rule;

class InsertPrestamoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'nullable|integer',
            'tipo_prestamo' => 'nullable|in:Prestamo,Devolucion',
            'observacion' => 'required|string',
            'fec_prestamo' => 'required|date_format:Y-m-d',
            'id_herramienta' => [
                'required',
                'integer',
                Rule::exists('herramientas', 'id'),
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
            'tipo_prestamo.in' => 'El tipo de préstamo solo puede ser "Prestamo" o "Devolucion".',
            'observacion.required' => 'La observación es obligatoria.',
            'fec_prestamo.required' => 'La fecha de préstamo es obligatoria.',
            'fec_prestamo.date_format' => 'La fecha de préstamo debe tener el formato YYYY-MM-DD.',
            'id_herramienta.exists' => 'La herramienta no existe.',
            'id_user.exists' => 'El usuario no existe.',
        ];
    }
}