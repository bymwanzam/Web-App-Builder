<?php

namespace Tests\Feature\App;

use App\Models\Keystore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SigningKeyUploadModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    protected function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function modal_renders_for_authenticated_user()
    {
        $this->createAuthenticatedUser();

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->assertOk();
    }

    #[Test]
    public function modal_requires_authentication()
    {
        $this->get('/user/apps/1#signing')
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function modal_opens_when_event_dispatched()
    {
        $this->createAuthenticatedUser();

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->dispatch('openSigningKeyUploadModal')
            ->assertSet('showModal', true);
    }

    #[Test]
    public function modal_closes_on_cancel()
    {
        $this->createAuthenticatedUser();

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function upload_custom_key_defaults_to_true()
    {
        $this->createAuthenticatedUser();

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->assertSet('upload_custom_key', true);
    }

    #[Test]
    public function can_toggle_upload_custom_key()
    {
        $this->createAuthenticatedUser();

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('upload_custom_key', false)
            ->assertSet('upload_custom_key', false);
    }

    // ==================== ANDROID KEYSTORE TESTS ====================

    #[Test]
    public function can_upload_android_keystore_with_valid_data()
    {
        $user = $this->createAuthenticatedUser();

        // Create a fake JKS file with proper magic number
        $jksContent = "\xFE\xED\xFE\xED".str_repeat('x', 96);
        $keystoreFile = UploadedFile::fake()->createWithContent('release.keystore', $jksContent);

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_file', $keystoreFile)
            ->set('keystore_alias', 'release-key')
            ->set('keystore_password', 'password123')
            ->call('save')
            ->assertDispatched('keystoreUploaded')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('keystores', [
            'user_id' => $user->id,
            'name' => 'My Release Keystore',
            'alias' => 'release-key',
        ]);
    }

    #[Test]
    public function keystore_name_is_required()
    {
        $this->createAuthenticatedUser();

        $jksContent = "\xFE\xED\xFE\xED".str_repeat('x', 96);
        $keystoreFile = UploadedFile::fake()->createWithContent('release.keystore', $jksContent);

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_file', $keystoreFile)
            ->set('keystore_alias', 'release-key')
            ->set('keystore_password', 'password123')
            ->call('save')
            ->assertHasErrors(['keystore_name' => 'required']);
    }

    #[Test]
    public function keystore_file_is_required()
    {
        $this->createAuthenticatedUser();

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_alias', 'release-key')
            ->set('keystore_password', 'password123')
            ->call('save')
            ->assertHasErrors(['keystore_file' => 'required']);
    }

    #[Test]
    public function keystore_alias_required_when_upload_custom_key_is_true()
    {
        $this->createAuthenticatedUser();

        $jksContent = "\xFE\xED\xFE\xED".str_repeat('x', 96);
        $keystoreFile = UploadedFile::fake()->createWithContent('release.keystore', $jksContent);

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('upload_custom_key', true)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_file', $keystoreFile)
            ->set('keystore_password', 'password123')
            ->call('save')
            ->assertHasErrors(['keystore_alias' => 'required']);
    }

    #[Test]
    public function keystore_password_required_when_upload_custom_key_is_true()
    {
        $this->createAuthenticatedUser();

        $jksContent = "\xFE\xED\xFE\xED".str_repeat('x', 96);
        $keystoreFile = UploadedFile::fake()->createWithContent('release.keystore', $jksContent);

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('upload_custom_key', true)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_file', $keystoreFile)
            ->set('keystore_alias', 'release-key')
            ->call('save')
            ->assertHasErrors(['keystore_password' => 'required']);
    }

    #[Test]
    public function keystore_alias_not_required_when_upload_custom_key_is_false()
    {
        $user = $this->createAuthenticatedUser();

        $jksContent = "\xFE\xED\xFE\xED".str_repeat('x', 96);
        $keystoreFile = UploadedFile::fake()->createWithContent('debug.keystore', $jksContent);

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('upload_custom_key', false)
            ->set('keystore_name', 'Debug Keystore')
            ->set('keystore_file', $keystoreFile)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('keystoreUploaded');

        $this->assertDatabaseHas('keystores', [
            'user_id' => $user->id,
            'name' => 'Debug Keystore',
            'alias' => 'auto-generated',
        ]);
    }

    #[Test]
    public function keystore_password_must_be_at_least_6_characters()
    {
        $this->createAuthenticatedUser();

        $jksContent = "\xFE\xED\xFE\xED".str_repeat('x', 96);
        $keystoreFile = UploadedFile::fake()->createWithContent('release.keystore', $jksContent);

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_file', $keystoreFile)
            ->set('keystore_alias', 'release-key')
            ->set('keystore_password', '123')
            ->call('save')
            ->assertHasErrors(['keystore_password' => 'min']);
    }

    #[Test]
    public function keystore_file_accepts_any_file_type()
    {
        $user = $this->createAuthenticatedUser();

        // Accept any file since .jks and .keystore are not standard MIME types
        $file = UploadedFile::fake()->createWithContent('release.jks', 'fake-keystore-content');

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_file', $file)
            ->set('keystore_alias', 'release-key')
            ->set('keystore_password', 'password123')
            ->call('save')
            ->assertHasNoErrors();
    }

    #[Test]
    public function keystore_file_validates_max_size()
    {
        $this->createAuthenticatedUser();

        $largeFile = UploadedFile::fake()->create('large.keystore', 6000); // 6MB

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_file', $largeFile)
            ->set('keystore_alias', 'release-key')
            ->set('keystore_password', 'password123')
            ->call('save')
            ->assertHasErrors(['keystore_file' => 'max']);
    }

    #[Test]
    public function uploaded_keystore_belongs_to_current_user()
    {
        $user = $this->createAuthenticatedUser();

        $jksContent = "\xFE\xED\xFE\xED".str_repeat('x', 96);
        $keystoreFile = UploadedFile::fake()->createWithContent('release.keystore', $jksContent);

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_name', 'My Release Keystore')
            ->set('keystore_file', $keystoreFile)
            ->set('keystore_alias', 'release-key')
            ->set('keystore_password', 'password123')
            ->call('save');

        $keystore = Keystore::latest()->first();
        $this->assertEquals($user->id, $keystore->user_id);
    }

    #[Test]
    public function modal_resets_fields_when_opened()
    {
        $this->createAuthenticatedUser();

        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->set('keystore_name', 'Test')
            ->call('openModal')
            ->assertSet('keystore_name', null)
            ->assertSet('upload_custom_key', true);
    }

    #[Test]
    public function shows_error_toast_on_upload_failure()
    {
        $this->createAuthenticatedUser();

        // Trigger validation error
        Livewire::test(\App\Plugins\Platforms\AndroidWebView\Livewire\SigningKeyUploadModal::class)
            ->call('save')
            ->assertHasErrors();
    }
}
