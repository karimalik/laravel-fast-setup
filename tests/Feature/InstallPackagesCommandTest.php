<?php

use karimalik\FastSetup\Tests\TestCase;

uses(TestCase::class);

it('warns when no packages defined', function () {
    config()->set('fast-setup.packages', []);

    $this->artisan('fast:install-packages')
        ->expectsOutputToContain('No packages defined')
        ->assertExitCode(1);
});

it('warns when no packages selected', function () {
    $this->artisan('fast:install-packages')
        ->expectsQuestion('Select the packages you want to install', [])
        ->expectsOutputToContain('No packages selected')
        ->assertExitCode(0);
});

it('asks confirmation before installing', function () {
    $this->artisan('fast:install-packages')
        ->expectsQuestion('Select the packages you want to install', ['spatie/laravel-permission'])
        ->expectsConfirmation('Proceed with installation?', 'no')
        ->expectsOutputToContain('Installation cancelled')
        ->assertExitCode(0);
});

it('shows dry run message', function () {
    $this->artisan('fast:install-packages', [
        '--packages' => ['spatie/laravel-permission'],
        '--dry-run'  => true,
    ])
        ->expectsOutputToContain('DRY RUN')
        ->assertExitCode(0);
});

it('skips multiselect when packages option provided', function () {
    $this->artisan('fast:install-packages', [
        '--packages' => ['spatie/laravel-permission'],
        '--dry-run'  => true,
    ])
        ->assertExitCode(0);
});

it('fails when unknown packages are provided via option', function () {
    $this->artisan('fast:install-packages', [
        '--packages' => ['unknown/package-xyz'],
        '--dry-run'  => true,
    ])
        ->expectsOutputToContain('Unknown package')
        ->assertExitCode(1);
});
