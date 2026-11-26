<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppIconSettings;
use App\Plugins\Platforms\AndroidWebView\Livewire\DeleteIconModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AppIconSettingsTest extends TestCase
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

        Livewire::test(AppIconSettings::class, ['app' => $app])
            ->assertOk();
    }

    public function test_icon_zip_file_is_required()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        Livewire::test(AppIconSettings::class, ['app' => $app])
            ->set('iconZipFile', null)
            ->call('saveIcon')
            ->assertHasErrors(['iconZipFile' => 'required']);
    }

    public function test_icon_zip_file_must_be_a_file()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        Livewire::test(AppIconSettings::class, ['app' => $app])
            ->set('iconZipFile', 'not-a-file')
            ->call('saveIcon')
            ->assertHasErrors('iconZipFile');
    }

    public function test_icon_zip_file_must_be_zip_format()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->create('icon.txt', 100);

        Livewire::test(AppIconSettings::class, ['app' => $app])
            ->set('iconZipFile', $file)
            ->call('saveIcon')
            ->assertHasErrors(['iconZipFile' => 'mimes']);
    }

    public function test_icon_zip_file_cannot_exceed_max_size()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->create('icon.zip', 11000); // 11MB

        Livewire::test(AppIconSettings::class, ['app' => $app])
            ->set('iconZipFile', $file)
            ->call('saveIcon')
            ->assertHasErrors(['iconZipFile' => 'max']);
    }

    public function test_delete_icon_removes_uploaded_files()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        // Create fake files
        Storage::disk('public')->put('icons/test-app/icon.zip', 'test content');
        Storage::disk('public')->put('icons/test-app/extracted/icon.png', 'test icon');
        Storage::disk('public')->put('icons/test-app/app-icon.png', 'app icon');

        $app->androidWebViewConfig->update([
            'uploaded_icon_zip' => 'icons/test-app/icon.zip',
            'icon_extracted_path' => 'icons/test-app/extracted',
            'app_icon' => 'icons/test-app/app-icon.png',
        ]);

        Livewire::test(DeleteIconModal::class, ['app' => $app])
            ->call('confirmDelete')
            ->assertDispatched('show-toast')
            ->assertDispatched('settings-updated');

        $app->refresh();
        $this->assertNull($app->androidWebViewConfig->uploaded_icon_zip);
        $this->assertNull($app->androidWebViewConfig->icon_extracted_path);
        $this->assertNull($app->androidWebViewConfig->app_icon);
    }

    public function test_delete_icon_handles_null_values_gracefully()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $app->androidWebViewConfig->update([
            'uploaded_icon_zip' => null,
            'icon_extracted_path' => null,
            'app_icon' => null,
        ]);

        Livewire::test(DeleteIconModal::class, ['app' => $app])
            ->call('confirmDelete')
            ->assertDispatched('show-toast')
            ->assertDispatched('settings-updated');

        $app->refresh();
        $this->assertNull($app->androidWebViewConfig->uploaded_icon_zip);
        $this->assertNull($app->androidWebViewConfig->icon_extracted_path);
        $this->assertNull($app->androidWebViewConfig->app_icon);
    }
}
