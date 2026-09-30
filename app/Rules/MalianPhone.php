<?php

namespace App\Rules;

use App\Support\PhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MalianPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (!PhoneNumber::valid($value)) {
            $fail('Numéro invalide : 8 chiffres maliens attendus (ex : 70 00 00 00).');
        }
    }
}
