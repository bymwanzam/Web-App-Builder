<?php

namespace Tests\Unit\Services;

use App\Models\App;
use App\Models\DataExportRequest;
use App\Models\User;
use App\Models\UserConsent;
use App\Services\DataExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class DataExportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DataExportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataExportService;
        Storage::fake('local');
    }

    protected function getZipContents(string $storagePath): array
    {
        // Get contents from fake storage, write to temp file, open with ZipArchive
        $contents = Storage::disk('local')->get($storagePath);
        $tempPath = sys_get_temp_dir().'/'.uniqid('test_zip_').'.zip';
        file_put_contents($tempPath, $contents);

        $zip = new ZipArchive;
        $zip->open($tempPath);

        $result = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $result[$filename] = $zip->getFromIndex($i);
        }

        $zip->close();
        @unlink($tempPath);

        return $result;
    }

    public function test_generate_export_creates_zip_file(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::createForUser($user);

        $this->service->generateExport($request);

        $request->refresh();
        $this->assertEquals('completed', $request->status);
        $this->assertNotNull($request->file_path);
        Storage::disk('local')->assertExists($request->file_path);
    }

    public function test_export_includes_profile_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $request = DataExportRequest::createForUser($user);

        $this->service->generateExport($request);

        $request->refresh();
        $zipContents = $this->getZipContents($request->file_path);

        $profile = json_decode($zipContents['profile.json'], true);

        $this->assertEquals('Test User', $profile['name']);
        $this->assertEquals('test@example.com', $profile['email']);
    }

    public function test_export_includes_apps_data(): void
    {
        $user = User::factory()->create();
        App::factory()->count(2)->create(['user_id' => $user->id]);
        $request = DataExportRequest::createForUser($user);

        $this->service->generateExport($request);

        $request->refresh();
        $zipContents = $this->getZipContents($request->file_path);

        $apps = json_decode($zipContents['apps.json'], true);

        $this->assertCount(2, $apps);
    }

    public function test_export_includes_consents_data(): void
    {
        $user = User::factory()->create();
        UserConsent::factory()->create([
            'user_id' => $user->id,
            'consent_type' => 'marketing',
            'consented' => true,
        ]);
        $request = DataExportRequest::createForUser($user);

        $this->service->generateExport($request);

        $request->refresh();
        $zipContents = $this->getZipContents($request->file_path);

        $consents = json_decode($zipContents['consents.json'], true);

        $this->assertCount(1, $consents);
        $this->assertEquals('marketing', $consents[0]['consent_type']);
    }

    public function test_export_sets_file_size(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::createForUser($user);

        $this->service->generateExport($request);

        $request->refresh();
        $this->assertNotNull($request->file_size);
        $this->assertGreaterThan(0, $request->file_size);
    }

    public function test_export_sets_completed_at(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::createForUser($user);

        $this->service->generateExport($request);

        $request->refresh();
        $this->assertNotNull($request->completed_at);
    }

    public function test_failed_export_sets_error_message(): void
    {
        // Create a request and mock the user relationship to return null
        $user = User::factory()->create();
        $request = DataExportRequest::createForUser($user);

        // Use a partial mock to return null for the user relationship
        $mockRequest = $this->partialMock(DataExportRequest::class, function ($mock) use ($request) {
            $mock->shouldReceive('getAttribute')->with('user')->andReturn(null);
            $mock->shouldReceive('update')->andReturnUsing(function ($data) use ($request) {
                $request->update($data);

                return true;
            });
        });

        $this->service->generateExport($mockRequest);

        $request->refresh();
        $this->assertEquals('failed', $request->status);
        $this->assertNotNull($request->error_message);
    }
}
