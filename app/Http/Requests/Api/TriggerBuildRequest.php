<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TriggerBuildRequest extends FormRequest
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
        return [
            'build_format' => ['sometimes', 'string', Rule::in(['apk', 'aab'])],
            'build_type' => ['sometimes', 'string', Rule::in(['debug', 'release'])],
            'keystore_id' => [
                'nullable',
                'integer',
                Rule::exists('keystores', 'id')->where(function ($query) {
                    $query->where('user_id', $this->user()->id);
                }),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'build_format.in' => 'The build format must be either apk or aab.',
            'build_type.in' => 'The build type must be either debug or release.',
            'keystore_id.exists' => 'The selected keystore does not exist or does not belong to you.',
        ];
    }
}
