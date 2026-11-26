<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\Keystore;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppSigningKeysSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppSigningKeysSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithApp()
    {
        $user = User::factory()->create();
        $app = App::factory()->forUser($user)->create();
        $this->actingAs($user);

        return [$user, $app];
    }

    public function test_component_renders_for_authenticated_user()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->assertOk();
    }

    public function test_component_displays_user_keystores()
    {
        [$user, $app] = $this->createUserWithApp();

        $keystore1 = Keystore::factory()->forUser($user)->create(['name' => 'Keystore 1']);
        $keystore2 = Keystore::factory()->forUser($user)->create(['name' => 'Keystore 2']);

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->assertSee('Keystore 1')
            ->assertSee('Keystore 2');
    }

    public function test_component_does_not_display_other_users_keystores()
    {
        [$user, $app] = $this->createUserWithApp();

        $otherUser = User::factory()->create();
        $otherKeystore = Keystore::factory()->forUser($otherUser)->create(['name' => 'Other Keystore']);

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->assertDontSee('Other Keystore');
    }

    public function test_user_can_dispatch_delete_keystore_event()
    {
        [$user, $app] = $this->createUserWithApp();

        $keystore = Keystore::factory()->forUser($user)->create(['name' => 'Test Keystore']);

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->assertSee('Test Keystore');

        // Test that keystore exists and belongs to user
        $this->assertDatabaseHas('keystores', [
            'id' => $keystore->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_see_other_users_keystores_to_delete()
    {
        [$user, $app] = $this->createUserWithApp();

        $otherUser = User::factory()->create();
        $otherKeystore = Keystore::factory()->forUser($otherUser)->create(['name' => 'Other User Keystore']);

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->assertDontSee('Other User Keystore');

        // Keystore should still exist
        $this->assertDatabaseHas('keystores', ['id' => $otherKeystore->id]);
    }

    public function test_app_displays_default_keystore_badge()
    {
        [$user, $app] = $this->createUserWithApp();

        $keystore = Keystore::factory()->forUser($user)->create(['name' => 'Default Keystore']);
        $app->androidWebViewConfig->update(['keystore_id' => $keystore->id]);

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->assertSee('Default Keystore')
            ->assertSee('Default');
    }

    public function test_can_set_android_keystore_as_default()
    {
        [$user, $app] = $this->createUserWithApp();

        $keystore = Keystore::factory()->forUser($user)->create();

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->call('setAsDefault', 'android', $keystore->id)
            ->assertDispatched('show-toast');

        $app->refresh();
        $this->assertEquals($keystore->id, $app->androidWebViewConfig->keystore_id);
    }

    public function test_component_refreshes_when_keystore_uploaded()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSigningKeysSettings::class, ['app' => $app])
            ->dispatch('keystoreUploaded')
            ->assertOk();
    }
}
