<?php

use karimalik\FastSetup\Tests\TestCase;
use Illuminate\Support\Facades\File;

uses(TestCase::class);

afterEach(function () {
    File::deleteDirectory(base_path('app/Services'));
    File::deleteDirectory(base_path('app/Repositories'));
    File::deleteDirectory(base_path('app/Interfaces'));
    File::deleteDirectory(base_path('app/DTOs'));
    File::deleteDirectory(base_path('app/Enums'));
});

it('warns when no structures defined', function () {
    config()->set('fast-setup.structures', []);

    $this->artisan('fast:generate-structure')
        ->expectsOutputToContain('No structures defined')
        ->assertExitCode(1);
});

it('generates structure via option', function () {
    $this->artisan('fast:generate-structure', ['--structure' => 'standard'])
        ->assertExitCode(0);

    expect(base_path('app/Services'))->toBeDirectory();
    expect(base_path('app/Repositories'))->toBeDirectory();
});

it('creates gitkeep in each folder', function () {
    $this->artisan('fast:generate-structure', ['--structure' => 'standard'])
        ->assertExitCode(0);

    expect(base_path('app/Services/.gitkeep'))->toBeFile();
    expect(base_path('app/Repositories/.gitkeep'))->toBeFile();
});

it('skips existing folders', function () {
    File::makeDirectory(base_path('app/Services'), 0755, true);

    $this->artisan('fast:generate-structure', ['--structure' => 'standard'])
        ->expectsOutputToContain('Already exists')
        ->assertExitCode(0);
});

it('blocks path traversal', function () {
    config()->set('fast-setup.structures', [
        'malicious' => ['../../etc/passwd'],
    ]);

    $this->artisan('fast:generate-structure', ['--structure' => 'malicious'])
        ->expectsOutputToContain('Blocked unsafe path')
        ->assertExitCode(1);
});

it('fails on unknown structure', function () {
    $this->artisan('fast:generate-structure', ['--structure' => 'unknown'])
        ->expectsOutputToContain('Unknown structure')
        ->assertExitCode(1);
});

it('does not create folders in dry run mode', function () {
    $this->artisan('fast:generate-structure', ['--structure' => 'standard', '--dry-run' => true])
        ->assertExitCode(0);

    expect(base_path('app/Services'))->not->toBeDirectory();
    expect(base_path('app/Repositories'))->not->toBeDirectory();
});

it('shows would create output in dry run mode', function () {
    $this->artisan('fast:generate-structure', ['--structure' => 'standard', '--dry-run' => true])
        ->expectsOutputToContain('Would create')
        ->assertExitCode(0);
});
