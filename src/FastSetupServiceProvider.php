<?php

declare(strict_types=1);

namespace karimalik\FastSetup;

use Illuminate\Support\ServiceProvider;
use karimalik\FastSetup\Commands\FastSetupCommand;
use karimalik\FastSetup\Commands\GenerateEnvCommand;
use karimalik\FastSetup\Commands\InstallPackagesCommand;
use karimalik\FastSetup\Commands\GenerateStructureCommand;

class FastSetupServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FastSetupCommand::class,
                InstallPackagesCommand::class,
                GenerateStructureCommand::class,
                GenerateEnvCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/fast-setup.php' => config_path('fast-setup.php'),
            ], 'fast-setup-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/fast-setup.php',
            'fast-setup'
        );
    }

    private function validateConfig(): void
    {
        $packages = config('fast-setup.packages', []);
        $structures = config('fast-setup.structures', []);

        if (! is_array($packages) || ! is_array($structures)) {
            throw new \InvalidArgumentException('[FastSetup] config packages must be an array.');
        }

        foreach ($packages as $composer => $config) {
            if (! is_string($composer) || ! str_contains($composer, '/')) {
                throw new \InvalidArgumentException("[FastSetup] Invalid package name: {$composer}. Expected vendor/package format.");
            }

            if (! isset($config['name']) || ! is_string($config['name'])) {
                throw new \InvalidArgumentException("[FastSetup] Package {$composer} is missing a valid 'name' key.");
            }

            if (isset($config['post_install'])) {
                if (! is_array($config['post_install'])) {
                    throw new \InvalidArgumentException("[FastSetup] post_install for {$composer} must be an array.");
                }

                if (isset($config['post_install']['migrate']) && ! is_bool($config['post_install']['migrate'])) {
                    throw new \InvalidArgumentException("[FastSetup] post_install.migrate for {$composer} must be a boolean.");
                }
            }
        }

        if (! is_array($structures)) {
            throw new \InvalidArgumentException('[FastSetup] config structures must be an array.');
        }

        foreach ($structures as $name => $folders) {
            if (! is_array($folders) || empty($folders)) {
                throw new \InvalidArgumentException("[FastSetup] Structure '{$name}' must be a non-empty array of folders.");
            }

            foreach ($folders as $folder) {
                if (! is_string($folder) || empty($folder)) {
                    throw new \InvalidArgumentException("[FastSetup] Structure '{$name}' contains an invalid folder path.");
                }

                if (str_contains($folder, '..')) {
                    throw new \InvalidArgumentException("[FastSetup] Structure '{$name}' contains an unsafe path: {$folder}");
                }
            }
        }
    }
}
