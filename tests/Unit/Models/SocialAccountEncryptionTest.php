<?php

namespace Tests\Unit\Models;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SocialAccountEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_token_is_encrypted_in_database(): void
    {
        $user = User::factory()->create();
        $token = 'test_oauth_token_12345';

        $account = SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => '123456789',
            'provider_token' => $token,
        ]);

        // Get raw value from database
        $rawValue = DB::table('social_accounts')
            ->where('id', $account->id)
            ->value('provider_token');

        // Raw value should not match the original token (it's encrypted)
        $this->assertNotEquals($token, $rawValue);

        // But the model should decrypt it correctly
        $this->assertEquals($token, $account->fresh()->provider_token);
    }

    public function test_provider_refresh_token_is_encrypted_in_database(): void
    {
        $user = User::factory()->create();
        $refreshToken = 'test_refresh_token_67890';

        $account = SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => '123456789',
            'provider_refresh_token' => $refreshToken,
        ]);

        // Get raw value from database
        $rawValue = DB::table('social_accounts')
            ->where('id', $account->id)
            ->value('provider_refresh_token');

        // Raw value should not match the original token (it's encrypted)
        $this->assertNotEquals($refreshToken, $rawValue);

        // But the model should decrypt it correctly
        $this->assertEquals($refreshToken, $account->fresh()->provider_refresh_token);
    }

    public function test_null_tokens_are_handled_correctly(): void
    {
        $user = User::factory()->create();

        $account = SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'github',
            'provider_id' => '987654321',
            'provider_token' => null,
            'provider_refresh_token' => null,
        ]);

        $this->assertNull($account->fresh()->provider_token);
        $this->assertNull($account->fresh()->provider_refresh_token);
    }

    public function test_tokens_can_be_updated(): void
    {
        $user = User::factory()->create();
        $originalToken = 'original_token';
        $newToken = 'updated_token';

        $account = SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'facebook',
            'provider_id' => '111222333',
            'provider_token' => $originalToken,
        ]);

        $account->update(['provider_token' => $newToken]);

        $this->assertEquals($newToken, $account->fresh()->provider_token);
    }
}
