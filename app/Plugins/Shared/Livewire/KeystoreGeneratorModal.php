<?php

namespace App\Plugins\Shared\Livewire;

use App\Models\AppBuilder;
use App\Models\Keystore;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class KeystoreGeneratorModal extends Component
{
    public $showModal = false;

    // Basic info
    public $keystore_name = '';

    public $alias = '';

    // Password fields
    public $store_password = '';

    public $key_password = '';

    public $use_same_password = true;

    // Validity
    public $validity_years = 25;

    // Distinguished Name fields
    public $cn = ''; // Common Name

    public $ou = ''; // Organizational Unit

    public $o = '';  // Organization

    public $l = '';  // Locality (City)

    public $st = ''; // State

    public $c = 'US'; // Country code

    // State
    public $isGenerating = false;

    public $error = null;

    protected function rules()
    {
        $rules = [
            'keystore_name' => ['required', 'string', 'max:255'],
            'alias' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z][a-zA-Z0-9_-]*$/'],
            'store_password' => ['required', 'string', 'min:6'],
            'cn' => ['required', 'string', 'max:64'],
            'ou' => ['nullable', 'string', 'max:64'],
            'o' => ['nullable', 'string', 'max:64'],
            'l' => ['nullable', 'string', 'max:64'],
            'st' => ['nullable', 'string', 'max:64'],
            'c' => ['nullable', 'string', 'size:2'],
            'validity_years' => ['required', 'integer', 'min:1', 'max:100'],
        ];

        if (! $this->use_same_password) {
            $rules['key_password'] = ['required', 'string', 'min:6'];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'keystore_name.required' => 'Please provide a name for the keystore.',
            'alias.required' => 'Key alias is required.',
            'alias.regex' => 'Alias must start with a letter and contain only letters, numbers, underscores, and hyphens.',
            'store_password.required' => 'Password is required.',
            'store_password.min' => 'Password must be at least 6 characters.',
            'key_password.required' => 'Key password is required when using different passwords.',
            'key_password.min' => 'Key password must be at least 6 characters.',
            'cn.required' => 'Common Name is required (typically your app or organization name).',
            'c.size' => 'Country code must be exactly 2 letters (e.g., US, UK, PH).',
        ];
    }

    #[On('openKeystoreGeneratorModal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->reset([
            'keystore_name', 'alias', 'store_password', 'key_password',
            'cn', 'ou', 'o', 'l', 'st',
            'error', 'isGenerating',
        ]);
        $this->use_same_password = true;
        $this->validity_years = 25;
        $this->c = 'US';
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function updatedKeystoreName($value)
    {
        // Auto-fill CN with keystore name if CN is empty
        if (empty($this->cn)) {
            $this->cn = $value;
        }

        // Auto-generate alias from keystore name if alias is empty
        if (empty($this->alias) && ! empty($value)) {
            $this->alias = $this->generateAlias($value);
        }
    }

    protected function generateAlias(string $name): string
    {
        // Convert to lowercase, replace spaces with hyphens
        $alias = strtolower(trim($name));
        $alias = preg_replace('/[^a-z0-9\s-]/', '', $alias);
        $alias = preg_replace('/[\s]+/', '-', $alias);

        // Ensure it starts with a letter
        if (! preg_match('/^[a-z]/', $alias)) {
            $alias = 'key-'.$alias;
        }

        return $alias;
    }

    public function generate()
    {
        $this->validate();
        $this->isGenerating = true;
        $this->error = null;

        try {
            // Get all available builders respecting user's plan restrictions
            $user = auth()->user();
            $allowedBuilderIds = $user->plan?->allowed_builders;

            $query = AppBuilder::active()->forAndroidPlatforms();
            if ($allowedBuilderIds !== null) {
                $query->whereIn('id', $allowedBuilderIds);
            }
            $builders = $query->orderBy('current_builds_count')->get();

            if ($builders->isEmpty()) {
                throw new \Exception('No active build server available for your plan. Please contact your administrator.');
            }

            $keyPassword = $this->use_same_password
                ? $this->store_password
                : $this->key_password;

            // Build request payload
            $payload = [
                'alias' => $this->alias,
                'store_password' => $this->store_password,
                'key_password' => $keyPassword,
                'validity_days' => $this->validity_years * 365,
                'dn' => [
                    'cn' => $this->cn ?: $this->keystore_name,
                    'ou' => $this->ou ?: null,
                    'o' => $this->o ?: null,
                    'l' => $this->l ?: null,
                    'st' => $this->st ?: null,
                    'c' => $this->c ?: null,
                ],
            ];

            // Remove null DN values
            $payload['dn'] = array_filter($payload['dn'], fn ($v) => $v !== null);

            // Try each builder until one succeeds
            $lastError = null;
            $response = null;
            $successfulBuilder = null;

            foreach ($builders as $builder) {
                try {
                    $response = Http::timeout(30)->post(
                        "{$builder->url}:{$builder->port}/keystores/generate/{$builder->server_key}",
                        $payload
                    );

                    if ($response->successful()) {
                        $successfulBuilder = $builder;
                        break;
                    }

                    // Non-connection error, store it but try next server
                    $lastError = $response->json('error', 'Failed to generate keystore');

                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::warning('Builder connection failed, trying next', [
                        'builder_id' => $builder->id,
                        'builder_name' => $builder->name,
                        'error' => $e->getMessage(),
                    ]);
                    $lastError = 'Unable to connect to build server';

                    continue;
                }
            }

            if (! $successfulBuilder || ! $response?->successful()) {
                throw new \Exception($lastError ?? 'All build servers are unavailable. Please try again later.');
            }

            $data = $response->json('data');

            if (empty($data['keystore_base64'])) {
                throw new \Exception('Invalid response from build server');
            }

            // Store in database
            $keystore = new Keystore;
            $keystore->user_id = auth()->id();
            $keystore->name = $this->keystore_name;
            $keystore->alias = $this->alias;
            $keystore->setKeystoreData(base64_decode($data['keystore_base64']));
            $keystore->setPassword($this->store_password);
            $keystore->setKeyPassword($keyPassword);
            $keystore->save();

            Log::info('Keystore generated successfully', [
                'keystore_id' => $keystore->id,
                'user_id' => auth()->id(),
                'alias' => $this->alias,
                'builder_id' => $successfulBuilder->id,
                'fingerprint_sha1' => $data['fingerprint_sha1'] ?? null,
            ]);

            $this->dispatch('keystoreGenerated', keystoreId: $keystore->id);
            $this->dispatch('show-toast',
                type: 'success',
                message: 'Keystore generated successfully!'
            );
            $this->closeModal();

        } catch (\Exception $e) {
            Log::error('Keystore generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            $this->error = $e->getMessage();
        } finally {
            $this->isGenerating = false;
        }
    }

    public function render()
    {
        return view('plugins.shared::livewire.keystore-generator-modal');
    }
}
