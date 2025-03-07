<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertHerramientaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'nullable|integer',
            'nombre_herramienta' => 'required|string',
            'referencia' => 'required|string',
            'marca' => 'required|string',
            'estado' => 'nullable|in:Nuevo,Bueno,Regular,Dado de baja',
            'codigo' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'nombre_herramienta.required' => 'El nombre de la herramienta es obligatorio.',
            'referencia.required' => 'La referencia es obligatoria.',
            'marca.required' => 'La marca es obligatoria.',
            'estado.in' => 'El estado solo puede ser "Nuevo", "Bueno", "Regular" o "Dado de baja".',
            'codigo.required' => 'El código es obligatorio.',
        ];
    }
}