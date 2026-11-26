<?php

namespace App\Http\Requests\Api;

use App\Models\Plugin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAppRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $activePlatforms = Plugin::where('type', 'platform')
            ->where('status', 'active')
            ->pluck('slug')
            ->toArray();

        return [
            'name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', Rule::in($activePlatforms)],
            'url' => ['required', 'url', 'max:500'],
            'package_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+$/i'],
            'version_name' => ['nullable', 'string', 'max:50', 'regex:/^\d+\.\d+(\.\d+)?$/'],
            'version_code' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'package_name.regex' => 'The package name must be in reverse domain format (e.g., com.example.app).',
            'version_name.regex' => 'The version name must be in semantic versioning format (e.g., 1.0.0).',
        ];
    }
}
