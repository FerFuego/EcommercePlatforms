<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Get the validation rules for reCAPTCHA based on settings.
     */
    public static function rules()
    {
        $isEnabled = \App\Models\Setting::get('recaptcha_enabled', '0') === '1';
        
        if (app()->runningUnitTests() || !$isEnabled) {
            return ['nullable'];
        }

        return ['required', new self];
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        // Permitir bypass si está desactivado en la configuración o si el valor es "bypass" (cuando está desactivado)
        $isEnabled = \App\Models\Setting::get('recaptcha_enabled', '0') === '1';
        if (!$isEnabled) {
            return;
        }

        if (empty($value)) {
            $fail('La verificación de seguridad es obligatoria.');
            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $data = $response->json();

        if (!$data['success'] || $data['score'] < config('services.recaptcha.score_threshold')) {
            $fail('La verificación de seguridad ha fallado. Por favor, inténtalo de nuevo.');
        }
    }
}
