<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
        'flag',
        'status',
    ];

    /**
     * Create translation directory and files for this language
     */
    public function createTranslationFiles(): void
    {
        $langPath = lang_path($this->code);

        // Create language directory if it doesn't exist
        if (! File::exists($langPath)) {
            File::makeDirectory($langPath, 0755, true);
        }

        // Copy base translation files from English if they exist
        $enPath = lang_path('en');
        if (File::exists($enPath)) {
            $files = File::files($enPath);
            foreach ($files as $file) {
                $targetFile = $langPath.'/'.$file->getFilename();
                if (! File::exists($targetFile)) {
                    File::copy($file->getPathname(), $targetFile);
                }
            }
        } else {
            // Create default translation files if English doesn't exist
            $this->createDefaultTranslationFiles($langPath);
        }
    }

    /**
     * Create default Laravel translation files
     */
    private function createDefaultTranslationFiles(string $path): void
    {
        // Create auth.php
        File::put($path.'/auth.php', $this->getAuthTranslations());

        // Create pagination.php
        File::put($path.'/pagination.php', $this->getPaginationTranslations());

        // Create passwords.php
        File::put($path.'/passwords.php', $this->getPasswordsTranslations());

        // Create validation.php
        File::put($path.'/validation.php', $this->getValidationTranslations());
    }

    private function getAuthTranslations(): string
    {
        return <<<'PHP'
<?php

return [
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
];
PHP;
    }

    private function getPaginationTranslations(): string
    {
        return <<<'PHP'
<?php

return [
    'previous' => '&laquo; Previous',
    'next' => 'Next &raquo;',
];
PHP;
    }

    private function getPasswordsTranslations(): string
    {
        return <<<'PHP'
<?php

return [
    'reset' => 'Your password has been reset.',
    'sent' => 'We have emailed your password reset link.',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",
];
PHP;
    }

    private function getValidationTranslations(): string
    {
        return <<<'PHP'
<?php

return [
    'accepted' => 'The :attribute field must be accepted.',
    'active_url' => 'The :attribute field must be a valid URL.',
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'max' => [
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'unique' => 'The :attribute has already been taken.',
    'attributes' => [],
];
PHP;
    }
}
