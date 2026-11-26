<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class UserForm extends Component
{
    public $userId;

    public $name;

    public $email;

    public $password;

    public $password_confirmation;

    public $role = 'user';

    public $status = 'active';

    public $email_verified = false;

    public $showModal = false;

    #[On('create-user')]
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    #[On('edit-user')]
    public function edit($id)
    {
        // Protect admin account (user ID 1) in demo mode
        if (config('app.demo') && (int) $id === 1) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Demo admin account cannot be edited.'
            );

            return;
        }

        $this->resetForm();
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->status = $user->status;
        $this->email_verified = ! is_null($user->email_verified_at);
        $this->showModal = true;
    }

    #[On('closeUserForm')]
    public function closeFromDelete()
    {
        $this->closeModal();
    }

    public function save()
    {
        // Protect admin account (user ID 1) in demo mode
        if (config('app.demo') && (int) $this->userId === 1) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Demo admin account cannot be edited.'
            );
            $this->closeModal();

            return;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive',
        ];

        if ($this->userId) {
            // For updates, password is optional
            if ($this->password) {
                $rules['password'] = 'min:8|confirmed';
            }
        } else {
            // For new users, password is required
            $rules['password'] = 'required|min:8|confirmed';
        }

        $validated = $this->validate($rules);

        if ($this->userId) {
            // Update existing user
            $user = User::findOrFail($this->userId);
            $user->name = $this->name;
            $user->email = $this->email;
            $user->role = $this->role;
            $user->status = $this->status;

            if ($this->password) {
                $user->password = Hash::make($this->password);
            }

            if ($this->email_verified && ! $user->email_verified_at) {
                $user->email_verified_at = now();
            } elseif (! $this->email_verified && $user->email_verified_at) {
                $user->email_verified_at = null;
            }

            $user->save();

            $this->dispatch('show-toast',
                type: 'success',
                message: 'User updated successfully!'
            );
        } else {
            // Create new user
            // Get the default plan
            $defaultPlanId = SystemSetting::get('default_plan_id', null);
            $defaultPlan = $defaultPlanId ? Plan::find($defaultPlanId) : null;

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'status' => $this->status,
                'email_verified_at' => $this->email_verified ? now() : null,
                'plan_id' => $defaultPlan ? $defaultPlan->id : null,
                'build_credits' => $defaultPlan ? $defaultPlan->monthly_build_credits : 0,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'User created successfully!'
            );
        }

        $this->closeModal();
        $this->dispatch('userUpdated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['password', 'password_confirmation']);
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'user';
        $this->status = 'active';
        $this->email_verified = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.user-form');
    }
}
