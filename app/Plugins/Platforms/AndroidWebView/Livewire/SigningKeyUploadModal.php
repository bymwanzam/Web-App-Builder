<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\Keystore;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class SigningKeyUploadModal extends Component
{
    use WithFileUploads;

    public $showModal = false;

    // Android fields
    public $keystore_name;

    public $keystore_file;

    public $keystore_alias;

    public $keystore_password;

    public $upload_custom_key = true;

    public function mount()
    {
        //
    }

    #[On('openSigningKeyUploadModal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->reset([
            'keystore_name',
            'keystore_file',
            'keystore_alias',
            'keystore_password',
            'upload_custom_key',
        ]);
        $this->upload_custom_key = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    protected function rules()
    {
        $rules = [
            'keystore_name' => ['required', 'string', 'max:255'],
            'keystore_file' => ['required', 'file', 'max:5120'],
        ];

        // Only require alias and password if upload_custom_key is enabled
        if ($this->upload_custom_key) {
            $rules['keystore_alias'] = ['required', 'string', 'max:255'];
            $rules['keystore_password'] = ['required', 'string', 'min:6'];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'keystore_file.required' => 'Please upload a keystore file.',
            'keystore_file.max' => 'The keystore file must not exceed 5MB.',
            'keystore_name.required' => 'Please provide a name for the keystore.',
            'keystore_alias.required' => 'Keystore alias is required for custom keys.',
            'keystore_password.required' => 'Keystore password is required.',
            'keystore_password.min' => 'Keystore password must be at least 6 characters.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $this->uploadKeystore();
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to upload signing key: '.$e->getMessage()
            );
        }
    }

    protected function uploadKeystore()
    {
        // Read and validate the keystore file
        $keystoreContent = file_get_contents($this->keystore_file->getRealPath());

        // Basic validation: Check if file is binary and not empty
        if (empty($keystoreContent)) {
            throw new \Exception('The keystore file is empty or corrupted.');
        }

        // Check if file appears to be a valid binary keystore (basic magic number check)
        // JKS files typically start with 0xFEEDFEED magic number
        // PKCS12 files start with 0x30 (ASN.1 SEQUENCE)
        $fileHeader = substr($keystoreContent, 0, 4);
        $isJKS = $fileHeader === "\xFE\xED\xFE\xED";
        $isPKCS12 = ord($fileHeader[0]) === 0x30;

        if (! $isJKS && ! $isPKCS12) {
            throw new \Exception('The file does not appear to be a valid Java keystore. Please upload a valid .jks or .keystore file.');
        }

        $keystore = new Keystore;
        $keystore->user_id = auth()->id();
        $keystore->name = $this->keystore_name;
        $keystore->setKeystoreData($keystoreContent);

        // Only set alias and password if upload_custom_key is enabled
        if ($this->upload_custom_key) {
            $keystore->alias = $this->keystore_alias;
            $keystore->setPassword($this->keystore_password);
        } else {
            // For auto-generated keystores (debug builds), set placeholder values
            $keystore->alias = 'auto-generated';
            $keystore->setPassword('auto-generated');
        }

        $keystore->save();

        $this->dispatch('keystoreUploaded', keystoreId: $keystore->id);
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.signing-key-upload-modal');
    }
}
