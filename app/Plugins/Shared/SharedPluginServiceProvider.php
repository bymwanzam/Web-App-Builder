<?php

namespace App\Plugins\Shared;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SharedPluginServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register shared plugin views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'plugins.shared');

        // Register shared Livewire components
        Livewire::component(
            'plugins.shared.keystore-generator-modal',
            \App\Plugins\Shared\Livewire\KeystoreGeneratorModal::class
        );
    }
}
