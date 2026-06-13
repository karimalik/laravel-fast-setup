<?php

use karimalik\FastSetup\Tests\TestCase;
use Illuminate\Support\Facades\File;

uses(TestCase::class);

afterEach(function () {
    File::delete(base_path('.env'));
    File::delete(base_path('.env.staging'));
    File::delete(base_path('.env.production'));
});

it('generates local env file', function () {
    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['local'])
        ->assertExitCode(0);

    expect(base_path('.env'))->toBeFile();
});

it('generates staging env file', function () {
    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['staging'])
        ->assertExitCode(0);

    expect(base_path('.env.staging'))->toBeFile();
});

it('generates production env file', function () {
    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['production'])
        ->assertExitCode(0);

    expect(base_path('.env.production'))->toBeFile();
});

it('generates multiple env files', function () {
    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['staging', 'production'])
        ->assertExitCode(0);

    expect(base_path('.env.staging'))->toBeFile();
    expect(base_path('.env.production'))->toBeFile();
});

it('skips existing env file when user declines', function () {
    File::put(base_path('.env'), 'EXISTING_CONTENT');

    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['local'])
        ->expectsConfirmation('.env already exists. Overwrite?', 'no')
        ->assertExitCode(0);

    expect(File::get(base_path('.env')))->toBe('EXISTING_CONTENT');
});

it('overwrites existing env file when user confirms', function () {
    File::put(base_path('.env'), 'EXISTING_CONTENT');

    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['local'])
        ->expectsConfirmation('.env already exists. Overwrite?', 'yes')
        ->assertExitCode(0);

    expect(File::get(base_path('.env')))->not->toBe('EXISTING_CONTENT');
});

it('generates production env with correct values', function () {
    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['production'])
        ->assertExitCode(0);

    $content = File::get(base_path('.env.production'));

    expect($content)
        ->toContain('APP_DEBUG=false')
        ->toContain('LOG_LEVEL=error')
        ->toContain('CACHE_DRIVER=redis')
        ->toContain('QUEUE_CONNECTION=redis')
        ->toContain('SESSION_DRIVER=redis');
});

it('generates local env with correct values', function () {
    $this->artisan('fast:generate-env')
        ->expectsQuestion('Which environment(s) do you want to generate .env files for?', ['local'])
        ->assertExitCode(0);

    $content = File::get(base_path('.env'));

    expect($content)
        ->toContain('APP_DEBUG=true')
        ->toContain('LOG_LEVEL=debug')
        ->toContain('CACHE_DRIVER=file')
        ->toContain('QUEUE_CONNECTION=sync')
        ->toContain('SESSION_DRIVER=file');
});

it('generates env via option without prompt', function () {
    $this->artisan('fast:generate-env', ['--envs' => ['staging']])
        ->assertExitCode(0);

    expect(base_path('.env.staging'))->toBeFile();
});

it('does not create files in dry run mode', function () {
    $this->artisan('fast:generate-env', ['--envs' => ['staging'], '--dry-run' => true])
        ->assertExitCode(0);

    expect(base_path('.env.staging'))->not->toBeFile();
});

it('shows would create output in dry run mode', function () {
    $this->artisan('fast:generate-env', ['--envs' => ['local'], '--dry-run' => true])
        ->expectsOutputToContain('would create')
        ->assertExitCode(0);
});
