<?php

namespace App\Contracts;

use App\Models\App;

interface PlatformPlugin extends Plugin
{
    /**
     * Get platform identifier (android, ios, macos, windows, etc.).
     * This should match the plugin slug.
     */
    public function getPlatformId(): string;

    /**
     * Get supported build formats for this platform.
     * Examples: ['apk', 'aab'] for Android, ['ipa'] for iOS
     *
     * @return array Array of build format strings
     */
    public function getSupportedBuildFormats(): array;

    /**
     * Get the Blade view path for the app creation form.
     * This view is shown in step 2 of the create app modal.
     *
     * @return string View path (e.g., 'plugins.android.creation-form')
     */
    public function getCreationFormView(): string;

    /**
     * Get the Blade view path for the main app editor.
     * This view contains the full app editing interface with tabs.
     *
     * @return string View path (e.g., 'plugins.android.editor.app-editor')
     */
    public function getEditorView(): string;

    /**
     * Get default configuration for new apps on this platform.
     * These values are used to pre-fill forms and set sensible defaults.
     *
     * @return array Default configuration array
     */
    public function getDefaultConfig(): array;

    /**
     * Validate app configuration before building.
     * Returns array of error messages. Empty array means validation passed.
     *
     * @param  App  $app  The app to validate
     * @return array Array of validation error messages
     */
    public function validateForBuild(App $app): array;

    /**
     * Prepare build configuration for builder.
     * Transforms app data into format expected by builder.
     *
     * This method should return a complete configuration array in the format
     * expected by the platform's builder, including all platform-specific
     * fields, signing configuration, and build-specific data.
     *
     * @param  \App\DataTransferObjects\BuildConfigContext  $context  Build context with app and build data
     * @return array Build configuration array
     */
    public function prepareBuildConfig(\App\DataTransferObjects\BuildConfigContext $context): array;

    /**
     * Get available permissions for this platform.
     * Returns array of permission definitions with labels and descriptions.
     *
     * @return array Permission definitions
     */
    public function getAvailablePermissions(): array;

    /**
     * Get the builder platform type.
     * This is used to match apps with compatible builders.
     *
     * @return string Builder platform identifier
     */
    public function getBuilderType(): string;

    /**
     * Generate a unique package/bundle identifier for this platform.
     * Examples: com.example.app for Android, com.example.App for iOS
     *
     * @param  string  $appName  The app name
     * @param  int  $attempt  Attempt number for uniqueness (default 1)
     * @return string Generated package identifier
     */
    public function generatePackageId(string $appName, int $attempt = 1): string;

    /**
     * Validate package/bundle identifier format.
     * Returns true if valid, false otherwise.
     *
     * @param  string  $packageId  The package ID to validate
     * @return bool Whether the package ID is valid
     */
    public function validatePackageId(string $packageId): bool;

    /**
     * Check if this platform supports Appetize.io previews.
     * Platforms that support live browser-based app previews should return true.
     *
     * @return bool Whether the platform supports Appetize previews
     */
    public function supportsAppetize(): bool;

    /**
     * Get Appetize.io configuration for preview.
     * Returns platform-specific configuration for Appetize SDK.
     *
     * @param  \App\Models\AppBuild  $build  The build to configure
     * @return array Configuration array for Appetize SDK
     */
    public function getAppetizeConfig(\App\Models\AppBuild $build): array;

    /**
     * Get Appetize platform identifier (android, ios).
     * Maps internal platform to Appetize-supported platform names.
     *
     * @return string Appetize platform identifier
     */
    public function getAppetizePlatformId(): string;

    /**
     * Get file type for Appetize upload.
     * Returns the file extension/type used for this platform's builds.
     *
     * @param  \App\Models\AppBuild  $build  The build to get file type for
     * @return string File type (e.g., 'apk', 'ipa', 'zip')
     */
    public function getAppetizeFileType(\App\Models\AppBuild $build): string;

    /**
     * Get default Appetize device for preview.
     * Returns device identifier for Appetize emulator.
     *
     * @return string Device identifier (e.g., 'pixel7', 'iphone14pro')
     */
    public function getAppetizeDefaultDevice(): string;

    /**
     * Get the Livewire component class name for the app creation form.
     * This component handles platform-specific fields in the create app modal.
     *
     * @return string|null Fully qualified Livewire component class name, or null if using Blade view only
     */
    public function getCreationFormComponent(): ?string;

    /**
     * Create platform-specific configuration after app creation.
     * This method is called after the App model is created with universal fields.
     *
     * @param  App  $app  The newly created app
     * @param  array  $formData  Form data from creation form component
     */
    public function createConfig(App $app, array $formData): void;

    /**
     * Update platform-specific configuration for an existing app.
     * This method is called when editing an app's platform-specific settings.
     *
     * @param  App  $app  The app to update
     * @param  array  $formData  Form data from edit form
     */
    public function updateConfig(App $app, array $formData): void;

    /**
     * Get the Livewire component alias for the build modal.
     * This component handles platform-specific build configuration.
     *
     * @return string Livewire component alias (e.g., 'plugins.android-webview.build-modal')
     */
    public function getBuildModalComponent(): string;

    /**
     * Get the Blade view path for the app card in the apps grid.
     * This view renders a single app card with platform-specific information.
     *
     * @return string View path (e.g., 'plugins.android-webview::app-card')
     */
    public function getAppCardView(): string;

    /**
     * Get the Livewire component alias for the preview panel.
     * Returns null if the platform doesn't support preview (e.g., desktop platforms).
     *
     * @return string|null Livewire component alias (e.g., 'plugins.android-webview.app-preview-panel'), or null if no preview
     */
    public function getPreviewPanelComponent(): ?string;

    /**
     * Get the Livewire component alias for the builds table.
     *
     * @return string Livewire component alias (e.g., 'plugins.android-webview.builds-table')
     */
    public function getBuildTableComponent(): string;

    /**
     * Get the Livewire component alias for the delete build modal.
     *
     * @return string Livewire component alias (e.g., 'plugins.android-webview.delete-build-modal')
     */
    public function getDeleteBuildModalComponent(): string;

    /**
     * Get the Livewire component alias for the build logs modal.
     *
     * @return string Livewire component alias (e.g., 'plugins.android-webview.build-logs-modal')
     */
    public function getBuildLogsModalComponent(): string;

    /**
     * Get the Livewire component alias for the build download modal.
     *
     * @return string Livewire component alias (e.g., 'plugins.android-webview.build-download-modal')
     */
    public function getBuildDownloadModalComponent(): string;

    /**
     * Get the platform-specific configuration model class.
     * This is used for dynamic relationship loading in the App model.
     *
     * @return string Fully qualified model class name (e.g., 'App\Models\AndroidWebViewConfig')
     */
    public function getConfigModelClass(): string;
}
