<?php

namespace App\Contracts;

interface Plugin
{
    /**
     * Get the plugin name.
     */
    public function getName(): string;

    /**
     * Get the plugin description.
     */
    public function getDescription(): string;

    /**
     * Get the plugin type (e.g., 'payment_gateway', 'bridge').
     */
    public function getType(): string;

    /**
     * Get the plugin icon path.
     */
    public function getIcon(): string;

    /**
     * Get the plugin version.
     */
    public function getVersion(): string;

    /**
     * Get the plugin author.
     */
    public function getAuthor(): string;

    /**
     * Get the plugin author URL.
     */
    public function getAuthorUrl(): string;

    /**
     * Check if the plugin is properly configured.
     */
    public function isConfigured(): bool;

    /**
     * Validate the plugin configuration.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateConfig(array $config): void;
}
