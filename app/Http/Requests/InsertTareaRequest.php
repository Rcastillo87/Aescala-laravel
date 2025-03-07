<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TareaEstado;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\TareaTipo;
use Illuminate\Validation\Rule;

class InsertTareaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'descripccion' => 'required|string',
            'fec_inicio' => 'required|date_format:Y-m-d',
            'fec_fin' => 'required|date_format:Y-m-d',
            'id_tarea_estado' => [
                'required',
                'integer',
                Rule::exists('tarea_estados', 'id'),
            ],
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'id_proyecto' => [
                'required',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'id_tarea_tipo' => [
                'required',
                'integer',
                Rule::exists('tarea_tipos', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'descripccion.required' => 'La descripción es obligatoria.',
            'fec_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fec_inicio.date_format' => 'La fecha de inicio debe tener el formato YYYY-MM-DD.',
            'fec_fin.required' => 'La fecha de fin es obligatoria.',
            'fec_fin.date_format' => 'La fecha de fin debe tener el formato YYYY-MM-DD.',
            'id_tarea_estado.exists' => 'El estado de la tarea no existe.',
            'id_user.exists' => 'El usuario no existe.',
            'id_proyecto.exists' => 'El proyecto no existe.',
            'id_tarea_tipo.exists' => 'El tipo de tarea no existe.',
        ];
    }
}