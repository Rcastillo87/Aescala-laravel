<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Tarea;
use Illuminate\Validation\Rule;

class InsertTareaAvanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'avance' => 'required|string',
            'id_tarea' => [
                'required',
                'integer',
                Rule::exists('tareas', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'avance.required' => 'El avance es obligatorio.',
            'id_tarea.exists' => 'La tarea no existe.',
        ];
    }
}