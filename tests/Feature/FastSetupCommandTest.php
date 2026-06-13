<?php

use karimalik\FastSetup\Tests\TestCase;

uses(TestCase::class);

it('skips all steps when user declines', function () {
    $this->artisan('fast:setup')
        ->expectsConfirmation(' Do you want to install packages?', 'no')
        ->expectsConfirmation(' Do you want to generate a folder structure?', 'no')
        ->expectsConfirmation(' Do you want to generate .env files?', 'no')
        ->assertExitCode(0);
});

it('displays success message', function () {
    $this->artisan('fast:setup')
        ->expectsConfirmation(' Do you want to install packages?', 'no')
        ->expectsConfirmation(' Do you want to generate a folder structure?', 'no')
        ->expectsConfirmation(' Do you want to generate .env files?', 'no')
        ->expectsOutputToContain('Project setup complete')
        ->assertExitCode(0);
});

it('runs preset in dry run mode', function () {
    $this->artisan('fast:setup', ['--preset' => 'api', '--dry-run' => true])
        ->expectsOutputToContain('Dry run complete')
        ->assertExitCode(0);
});

it('fails with unknown preset', function () {
    $this->artisan('fast:setup', ['--preset' => 'unknown'])
        ->expectsOutputToContain('Unknown preset')
        ->assertExitCode(1);
});

it('shows dry run label', function () {
    $this->artisan('fast:setup', ['--dry-run' => true])
        ->expectsConfirmation(' Do you want to install packages?', 'no')
        ->expectsConfirmation(' Do you want to generate a folder structure?', 'no')
        ->expectsConfirmation(' Do you want to generate .env files?', 'no')
        ->expectsOutputToContain('DRY RUN')
        ->assertExitCode(0);
});

it('skips top-level confirmations with skip-interaction flag', function () {
    $this->artisan('fast:setup', ['--skip-interaction' => true, '--dry-run' => true])
        ->expectsQuestion('Select the packages you want to install', [])
        ->expectsQuestion('Which structure do you want to generate?', 'standard')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', [])
        ->expectsOutputToContain('Dry run complete')
        ->assertExitCode(0);
});
