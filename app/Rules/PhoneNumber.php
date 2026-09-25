<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    /**
     * Valida si el teléfono cumple con un formato real y estructurado.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || !is_string($value)) {
            $fail('El teléfono es obligatorio.');
            return;
        }

        $trimmed = trim($value);

        // Permitir solo números, espacios, guiones, puntos, paréntesis y el prefijo '+'
        if (!preg_match('/^\+?[0-9\s\-\.\(\)]+$/', $trimmed)) {
            $fail('El teléfono solo puede contener números, espacios, guiones y el símbolo +.');
            return;
        }

        $digits = preg_replace('/\D/', '', $trimmed);
        $len = strlen($digits);

        // Mínimo 10 dígitos (código de área + local en Argentina, o teléfono internacional estándar) y máximo 15 (E.164)
        if ($len < 10 || $len > 15) {
            $fail('El teléfono debe tener entre 10 y 15 dígitos (ej: 11 2345-6789 o +54 9 11 2345-6789).');
            return;
        }

        // Rechazar números con todos los dígitos repetidos (ej: 1111111111, 0000000000) o más de 7 dígitos iguales al final
        if (preg_match('/^(\d)\1+$/', $digits) || preg_match('/(\d)\1{7,}$/', $digits)) {
            $fail('Por favor ingresa un número de teléfono real y válido.');
            return;
        }

        // Reglas específicas si es prefijo argentino (+54 o 54)
        if (str_starts_with($digits, '54')) {
            // Con 549 (móvil internacional): debe tener exactamente 13 dígitos (549 + 10 dígitos)
            if (str_starts_with($digits, '549') && $len !== 13) {
                $fail('El número con prefijo +54 9 debe tener exactamente 10 dígitos a continuación (ej: +54 9 11 2345-6789).');
                return;
            }
            // Con 54 sin 9: debe tener 12 dígitos (54 + 10 dígitos)
            if (!str_starts_with($digits, '549') && $len !== 12) {
                $fail('El número con prefijo +54 debe tener 10 dígitos a continuación (ej: +54 11 2345-6789).');
                return;
            }
        } elseif (str_starts_with($digits, '0')) {
            // Formato nacional argentino empezando con 0 (ej: 011 2345-6789 -> 11 dígitos, o 011 15 2345-6789 -> 13 dígitos)
            if ($len !== 11 && $len !== 13) {
                $fail('El número con prefijo 0 debe tener código de área y número local (ej: 011 2345-6789 o 0351 15 234-5678).');
                return;
            }
        } elseif (str_starts_with($digits, '9') && $len === 11) {
            // Formato móvil nacional con 9 y 10 dígitos de área+local (ej: 9 11 2345-6789)
            return;
        }
    }
}
