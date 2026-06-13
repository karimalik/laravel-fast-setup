<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Available Packages
    |--------------------------------------------------------------------------
    | List of packages the developer can choose to install during setup.
    | Each entry defines the composer package name, a human-readable label,
    | and optional post-install actions (publish assets, run migrations).
    */
    'packages' => [
        'spatie/laravel-permission' => [
            'name'         => 'Spatie Permission (roles & permissions)',
            'post_install' => [
                'publish' => '--provider="Spatie\Permission\PermissionServiceProvider"',
                'migrate' => true,
            ],
        ],
        'spatie/laravel-activitylog' => [
            'name'         => 'Spatie Activity Log',
            'post_install' => [
                'publish' => '--provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"',
                'migrate' => true,
            ],
        ],
        'spatie/laravel-medialibrary' => [
            'name'         => 'Spatie Media Library',
            'post_install' => [
                'publish' => '--provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"',
                'migrate' => true,
            ],
        ],
        'laravel/telescope' => [
            'name'         => 'Laravel Telescope (debugging)',
            'post_install' => [
                'artisan'  => 'telescope:install',
                'migrate'  => true,
            ],
        ],
        'laravel/sanctum' => [
            'name'         => 'Laravel Sanctum (API authentication)',
            'post_install' => [
                'publish' => '--provider="Laravel\Sanctum\SanctumServiceProvider"',
                'migrate' => true,
            ],
        ],
        'barryvdh/laravel-debugbar' => [
            'name'         => 'Laravel Debugbar',
            'post_install' => [],
        ],
        'spatie/laravel-query-builder' => [
            'name'         => 'Spatie Query Builder',
            'post_install' => [],
        ],
        'league/flysystem-aws-s3-v3' => [
            'name'         => 'AWS S3 Storage (Flysystem)',
            'post_install' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Folder Structures
    |--------------------------------------------------------------------------
    | Predefined folder architectures the developer can choose from.
    | A .gitkeep file is added to each folder so Git tracks empty directories.
    */
    'structures' => [
        'standard' => [
            'app/Services',
            'app/Repositories',
            'app/Interfaces',
            'app/DTOs',
            'app/Enums',
        ],
        'ddd' => [
            'app/Domain/Models',
            'app/Domain/Services',
            'app/Domain/Repositories',
            'app/Application/UseCases',
            'app/Application/DTOs',
            'app/Infrastructure/Persistence',
            'app/Infrastructure/Http',
        ],
        'api' => [
            'app/Services',
            'app/Repositories',
            'app/DTOs',
            'app/Enums',
            'app/Traits',
            'app/Http/Resources',
            'app/Http/Requests/Api',
        ],
        'hexagonal' => [
            'app/Core/Domain/Models',
            'app/Core/Domain/Ports',
            'app/Core/Application/Services',
            'app/Core/Application/Commands',
            'app/Core/Application/Queries',
            'app/Adapters/Http',
            'app/Adapters/Persistence',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Presets
    |--------------------------------------------------------------------------
    | Named project configurations. Run a preset with:
    |   php artisan fast:setup --preset=api
    | Each preset defines which packages to install, which folder structure
    | to scaffold, and which .env files to generate.
    */
    'presets' => [
        'api' => [
            'name'      => 'REST API',
            'packages'  => [
                'laravel/sanctum',
                'spatie/laravel-query-builder',
                'spatie/laravel-permission',
            ],
            'structure' => 'api',
            'envs'      => ['local', 'staging', 'production'],
        ],
        'standard' => [
            'name'      => 'Standard Laravel',
            'packages'  => [
                'barryvdh/laravel-debugbar',
                'laravel/telescope',
                'spatie/laravel-permission',
            ],
            'structure' => 'standard',
            'envs'      => ['local'],
        ],
        'ddd' => [
            'name'      => 'Domain-Driven Design',
            'packages'  => [
                'spatie/laravel-permission',
                'spatie/laravel-activitylog',
            ],
            'structure' => 'ddd',
            'envs'      => ['local', 'staging', 'production'],
        ],
    ],

];