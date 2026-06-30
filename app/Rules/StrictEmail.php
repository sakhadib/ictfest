<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrictEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::isValid($value)) {
            $fail('The :attribute must be a valid email address with a full domain, for example name@example.com.');
        }
    }

    public static function isValid(mixed $value): bool
    {
        $email = trim((string) $value);

        if ($email === '' || strlen($email) > 255) {
            return false;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return (bool) preg_match('/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/', $email);
    }
}
