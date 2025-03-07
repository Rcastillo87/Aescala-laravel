<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitir que todos los usuarios accedan a esta validación
    }

    public function rules()
    {
        return [
            'correo' => ['required', 'email', function ($attribute, $value, $fail) {
                $user = User::where('correo', $value)->where('activo', 1)->first();
                if (!$user) {
                    $fail('Usuario no existe.');
                } elseif ($user->id_rol == 3) {
                    $fail('Usuario sin acceso a la app.');
                }
            }],
        ];
    }

    public function messages()
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Solo se admiten correos válidos.',
        ];
    }
}