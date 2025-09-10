<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64PngOrNull implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return; // Se permite nulo
        }

        // Validar que comience con data:image/png;base64,
        if (!preg_match('/^data:image\/png;base64,/', $value)) {
            $fail("El campo {$attribute} debe estar en formato PNG base64.");
            return;
        }

        // Quitar el encabezado y decodificar
        $decoded = base64_decode(
            preg_replace('/^data:image\/png;base64,/', '', $value),
            true
        );

        if ($decoded === false) {
            $fail("El campo {$attribute} no contiene un base64 válido.");
            return;
        }

        // Validar que sea un PNG real
        $finfo = finfo_open();
        $mime  = finfo_buffer($finfo, $decoded, FILEINFO_MIME_TYPE);
        finfo_close($finfo);

        if ($mime !== 'image/png') {
            $fail("El campo {$attribute} debe ser una imagen PNG válida.");
        }
    }
}