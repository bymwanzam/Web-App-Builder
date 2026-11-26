<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;

return [
    // The HTML <title> for the generated documentation.
    // Will be set dynamically in AppServiceProvider
    'title' => config('app.name').' API Documentation',

    // A short description of your API.
    'description' => 'Programmatic access to your apps, builds, and push notifications.',

    // Text to place in the "Introduction" section.
    'intro_text' => 'This API allows you to programmatically manage your apps, trigger builds, and send push notifications.

**Authentication:** All API endpoints require authentication using Bearer tokens. Get your API token from the [API Keys](/user/api-keys) page in your dashboard.',

    // The base URL displayed in the docs.
    // Empty string means relative URLs (auto-detects from current page origin)
    'base_url' => '',

    // Routes to include in the docs
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains' => ['*'],
            ],

            'include' => [],

            // Exclude builder/internal routes
            'exclude' => [
                'api/v1/builds/pending/*',
                'api/v1/builds/*/status/*',
                'api/v1/keystores/*/download/*',
            ],
        ],
    ],

    // Use laravel type for dynamic base URL detection
    'type' => 'laravel',

    'theme' => 'default',

    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes' => true,
        'docs_url' => '/docs/api',
        'assets_directory' => null,
        'middleware' => [],
    ],

    'external' => [
        'html_attributes' => [],
    ],

    'try_it_out' => [
        'enabled' => true,
        // Use empty string to make requests relative to current page origin
        'base_url' => '',
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    // API authentication configuration
    'auth' => [
        'enabled' => true,
        'default' => true,
        'in' => AuthIn::BEARER->value,
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{YOUR_API_TOKEN}',
        'extra_info' => 'Get your API token from the <a href="/user/api-keys">API Keys</a> page in your dashboard.',
    ],

    'example_languages' => [
        'bash',
        'javascript',
        'php',
    ],

    'postman' => [
        'enabled' => true,
        'overrides' => [],
    ],

    'openapi' => [
        'enabled' => true,
        'version' => '3.0.3',
        'overrides' => [],
        'generators' => [],
    ],

    'groups' => [
        'default' => 'Endpoints',
        'order' => [
            'Account',
            'Apps',
            'Builds',
            'Notifications',
        ],
    ],

    'logo' => false,

    'last_updated' => 'Last updated: {date:F j, Y}',

    'examples' => [
        'faker_seed' => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],
        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
        ],
        'urlParameters' => [
            ...Defaults::URL_PARAMETERS_STRATEGIES,
        ],
        'queryParameters' => [
            ...Defaults::QUERY_PARAMETERS_STRATEGIES,
        ],
        'bodyParameters' => [
            ...Defaults::BODY_PARAMETERS_STRATEGIES,
        ],
        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                only: ['GET *'],
                config: [
                    'app.debug' => false,
                ]
            )
        ),
        'responseFields' => [
            ...Defaults::RESPONSE_FIELDS_STRATEGIES,
        ],
    ],

    'database_connections_to_transact' => [config('database.default')],

    'fractal' => [
        'serializer' => null,
    ],
];
