<?php

use karimalik\FastSetup\FastSetupServiceProvider;
use Orchestra\Testbench\TestCase;

class InstallPackagesCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FastSetupServiceProvider::class];
    }

    public function test_warns_when_no_packages_defined(): void
    {
        config()->set('fast-setup.packages', []);

        $this->artisan('fast:install-packages')
            ->expectsOutputToContain('No packages defined')
            ->assertExitCode(1);
    }

    public function test_warns_when_no_packages_selected(): void
    {
        $this->artisan('fast:install-packages')
            ->expectsQuestion('Select the packages you want to install', [])
            ->expectsOutputToContain('No packages selected')
            ->assertExitCode(0);
    }

    public function test_asks_confirmation_before_installing(): void
    {
        $this->artisan('fast:install-packages')
            ->expectsQuestion('Select the packages you want to install', ['spatie/laravel-permission'])
            ->expectsConfirmation('Proceed with installation?', 'no')
            ->expectsOutputToContain('Installation cancelled')
            ->assertExitCode(0);
    }

    public function test_dry_run_shows_would_install_message(): void
    {
        $this->artisan('fast:install-packages', [
            '--packages' => ['spatie/laravel-permission'],
            '--dry-run'  => true,
        ])
            ->expectsOutputToContain('DRY RUN')
            ->assertExitCode(0);
    }

    public function test_skips_multiselect_when_packages_option_provided(): void
    {
        $this->artisan('fast:install-packages', [
            '--packages' => ['spatie/laravel-permission'],
            '--dry-run'  => true,
        ])
            ->assertExitCode(0);
    }
}
