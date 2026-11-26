<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VersionNameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * Validates version name format for Android.
     * Format: X.Y or X.Y.Z where X, Y, Z are non-negative integers.
     * Examples: "1.0", "1.0.0", "2.5.3"
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Standard semantic versioning format: 1-3 dot-separated integers
        // Regex breakdown:
        // ^ - start of string
        // (\d+) - one or more digits (major version)
        // \. - literal period
        // (\d+) - one or more digits (minor version)
        // (\.(\d+))? - optional: period followed by one or more digits (patch version)
        // $ - end of string
        if (! preg_match('/^(\d+)\.(\d+)(\.(\d+))?$/', $value)) {
            $fail('The :attribute must be in format X.Y or X.Y.Z (e.g., 1.0 or 1.0.0).');
        }
    }
}
