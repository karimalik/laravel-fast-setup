<?php

namespace karimalik\FastSetup;

use Illuminate\Support\ServiceProvider;
use Karimalik\FastSetup\Commands\FastSetupCommand;
use Karimalik\FastSetup\Commands\GenerateEnvCommand;
use Karimalik\FastSetup\Commands\InstallPackagesCommand;
use Karimalik\FastSetup\Commands\GenerateStructureCommand;

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
            __DIR__ . '/../config/fast-setup.php', 'fast-setup'
        );
    }
}