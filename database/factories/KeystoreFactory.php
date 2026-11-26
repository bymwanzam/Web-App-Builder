<?php

namespace Database\Factories;

use App\Models\Keystore;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Keystore>
 */
class KeystoreFactory extends Factory
{
    protected $model = Keystore::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true).' Keystore',
            'alias' => fake()->word(),
            'encrypted_keystore' => Crypt::encryptString(base64_encode('fake-keystore-data')),
            'encrypted_password' => Crypt::encryptString('password123'),
            'encrypted_key_password' => Crypt::encryptString('keypassword123'),
        ];
    }

    /**
     * Set a specific user for the keystore.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
