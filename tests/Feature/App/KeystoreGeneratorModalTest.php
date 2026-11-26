<?php

namespace Tests\Feature\App;

use App\Models\AppBuilder;
use App\Models\Plugin;
use App\Models\User;
use App\Plugins\Shared\Livewire\KeystoreGeneratorModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KeystoreGeneratorModalTest extends TestCase
{
    use RefreshDatabase;

    protected function createAuthenticatedUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function modal_renders_for_authenticated_user(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->assertOk();
    }

    #[Test]
    public function modal_opens_when_event_dispatched(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->dispatch('openKeystoreGeneratorModal')
            ->assertSet('showModal', true);
    }

    #[Test]
    public function modal_closes_on_close(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function modal_resets_fields_when_opened(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test')
            ->set('alias', 'test-alias')
            ->dispatch('openKeystoreGeneratorModal')
            ->assertSet('keystore_name', '')
            ->assertSet('alias', '')
            ->assertSet('validity_years', 25)
            ->assertSet('c', 'US');
    }

    #[Test]
    public function keystore_name_is_required(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('alias', 'test-alias')
            ->set('store_password', 'password123')
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertHasErrors(['keystore_name' => 'required']);
    }

    #[Test]
    public function alias_is_required(): void
    {
        $this->createAuthenticatedUser();

        // Set fields in specific order to avoid auto-fill from keystore_name
        Livewire::test(KeystoreGeneratorModal::class)
            ->set('cn', 'Test CN')
            ->set('store_password', 'password123')
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', '') // Clear the auto-generated alias
            ->call('generate')
            ->assertHasErrors(['alias' => 'required']);
    }

    #[Test]
    public function alias_must_start_with_letter(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', '123-invalid')
            ->set('store_password', 'password123')
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertHasErrors(['alias' => 'regex']);
    }

    #[Test]
    public function store_password_is_required(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', 'test-alias')
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertHasErrors(['store_password' => 'required']);
    }

    #[Test]
    public function store_password_must_be_at_least_6_characters(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', 'test-alias')
            ->set('store_password', '123')
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertHasErrors(['store_password' => 'min']);
    }

    #[Test]
    public function cn_is_required(): void
    {
        $this->createAuthenticatedUser();

        // Set keystore_name first, then clear auto-filled cn
        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', 'test-alias')
            ->set('store_password', 'password123')
            ->set('cn', '') // Clear the auto-generated cn
            ->call('generate')
            ->assertHasErrors(['cn' => 'required']);
    }

    #[Test]
    public function key_password_required_when_use_same_password_is_false(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', 'test-alias')
            ->set('store_password', 'password123')
            ->set('use_same_password', false)
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertHasErrors(['key_password' => 'required']);
    }

    #[Test]
    public function auto_generates_alias_from_keystore_name(): void
    {
        $this->createAuthenticatedUser();

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'My App Release')
            ->assertSet('alias', 'my-app-release')
            ->assertSet('cn', 'My App Release');
    }

    #[Test]
    public function shows_error_when_no_builders_available(): void
    {
        $this->createAuthenticatedUser();

        // No builders exist
        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', 'test-alias')
            ->set('store_password', 'password123')
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertSet('error', 'No active build server available for your plan. Please contact your administrator.');
    }

    #[Test]
    public function finds_builder_with_android_wordpress_platform(): void
    {
        $this->createAuthenticatedUser();

        // Create the android-wordpress plugin so PluginManager can discover it
        Plugin::firstOrCreate(
            ['slug' => 'android-wordpress'],
            [
                'name' => 'Android WordPress',
                'type' => 'platform',
                'class' => 'App\Plugins\Platforms\AndroidWordPress\AndroidWordPressPlatform',
                'version' => '1.0.0',
                'status' => 'active',
                'installed_at' => now(),
            ]
        );

        // Create a builder that only supports android-wordpress
        AppBuilder::factory()->create([
            'platforms' => ['android-wordpress'],
            'status' => 'active',
        ]);

        // The generate will fail at HTTP call, but it should find the builder
        // and not show "No active build server" error
        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', 'test-alias')
            ->set('store_password', 'password123')
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertNotSet('error', 'No active build server available for your plan. Please contact your administrator.');
    }

    #[Test]
    public function finds_builder_with_android_webview_platform(): void
    {
        $this->createAuthenticatedUser();

        // Create the android-webview plugin so PluginManager can discover it
        Plugin::firstOrCreate(
            ['slug' => 'android-webview'],
            [
                'name' => 'Android WebView Platform',
                'type' => 'platform',
                'class' => 'App\Plugins\Platforms\AndroidWebView\AndroidWebViewPlatform',
                'version' => '1.0.0',
                'status' => 'active',
                'installed_at' => now(),
            ]
        );

        // Create a builder that only supports android-webview
        AppBuilder::factory()->create([
            'platforms' => ['android-webview'],
            'status' => 'active',
        ]);

        Livewire::test(KeystoreGeneratorModal::class)
            ->set('keystore_name', 'Test Keystore')
            ->set('alias', 'test-alias')
            ->set('store_password', 'password123')
            ->set('cn', 'Test CN')
            ->call('generate')
            ->assertNotSet('error', 'No active build server available for your plan. Please contact your administrator.');
    }
}
