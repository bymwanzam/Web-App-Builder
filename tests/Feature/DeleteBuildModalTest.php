<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\AppBuild;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\DeleteBuildModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteBuildModalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private App $testApp;

    private AppBuild $testBuild;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->testApp = App::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $this->testBuild = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'build_type' => 'debug',
            'version_name' => '1.0.0',
            'version_code' => 1,
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function it_opens_modal_when_delete_build_event_dispatched()
    {
        $this->actingAs($this->user);

        Livewire::test(DeleteBuildModal::class)
            ->dispatch('delete-build', id: $this->testBuild->id)
            ->assertSet('showModal', true)
            ->assertSet('buildId', $this->testBuild->id)
            ->assertSet('platform', 'Android-webview')
            ->assertSet('version', '1.0.0 (Build #1)');
    }

    #[Test]
    public function it_closes_modal_when_close_method_called()
    {
        $this->actingAs($this->user);

        Livewire::test(DeleteBuildModal::class)
            ->set('showModal', true)
            ->set('buildId', $this->testBuild->id)
            ->call('closeModal')
            ->assertSet('showModal', false)
            ->assertSet('buildId', null);
    }

    #[Test]
    public function it_deletes_build_when_delete_method_called()
    {
        $this->actingAs($this->user);

        $this->assertDatabaseHas('app_builds', [
            'id' => $this->testBuild->id,
        ]);

        Livewire::test(DeleteBuildModal::class)
            ->set('buildId', $this->testBuild->id)
            ->call('delete')
            ->assertDispatched('buildDeleted')
            ->assertDispatched('show-toast');

        $this->assertDatabaseMissing('app_builds', [
            'id' => $this->testBuild->id,
        ]);
    }

    #[Test]
    public function it_shows_correct_build_information_in_modal()
    {
        $this->actingAs($this->user);

        $iosBuild = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'ios',
            'version_name' => '2.1.0',
            'version_code' => 5,
        ]);

        Livewire::test(DeleteBuildModal::class)
            ->dispatch('delete-build', id: $iosBuild->id)
            ->assertSet('platform', 'Ios')
            ->assertSet('version', '2.1.0 (Build #5)');
    }

    #[Test]
    public function it_prevents_unauthorized_user_from_deleting_build()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $otherApp = App::factory()->create(['user_id' => $otherUser->id]);
        $otherBuild = AppBuild::factory()->create(['app_id' => $otherApp->id]);

        Livewire::test(DeleteBuildModal::class)
            ->dispatch('delete-build', id: $otherBuild->id)
            ->assertForbidden();
    }

    #[Test]
    public function it_prevents_unauthorized_user_from_executing_delete()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $otherApp = App::factory()->create(['user_id' => $otherUser->id]);
        $otherBuild = AppBuild::factory()->create(['app_id' => $otherApp->id]);

        Livewire::test(DeleteBuildModal::class)
            ->set('buildId', $otherBuild->id)
            ->call('delete')
            ->assertForbidden();
    }

    #[Test]
    public function it_closes_modal_after_successful_deletion()
    {
        $this->actingAs($this->user);

        Livewire::test(DeleteBuildModal::class)
            ->set('showModal', true)
            ->set('buildId', $this->testBuild->id)
            ->call('delete')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function it_resets_properties_after_deletion()
    {
        $this->actingAs($this->user);

        Livewire::test(DeleteBuildModal::class)
            ->set('buildId', $this->testBuild->id)
            ->set('platform', 'Android')
            ->set('version', '1.0.0 (Build #1)')
            ->call('delete')
            ->assertSet('buildId', null)
            ->assertSet('platform', '')
            ->assertSet('version', '');
    }
}
