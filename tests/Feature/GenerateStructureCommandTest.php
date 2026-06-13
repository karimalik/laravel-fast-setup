<?php

use karimalik\FastSetup\FastSetupServiceProvider;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\File;

class GenerateStructureCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FastSetupServiceProvider::class];
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('app/Services'));
        File::deleteDirectory(base_path('app/Repositories'));
        File::deleteDirectory(base_path('app/Interfaces'));
        File::deleteDirectory(base_path('app/DTOs'));
        File::deleteDirectory(base_path('app/Enums'));

        parent::tearDown();
    }

    public function test_warns_when_no_structures_defined(): void
    {
        config()->set('fast-setup.structures', []);

        $this->artisan('fast:generate-structure')
            ->expectsOutputToContain('No structures defined')
            ->assertExitCode(1);
    }

    public function test_generates_structure_via_option(): void
    {
        $this->artisan('fast:generate-structure', ['--structure' => 'standard'])
            ->assertExitCode(0);

        $this->assertDirectoryExists(base_path('app/Services'));
        $this->assertDirectoryExists(base_path('app/Repositories'));
    }

    public function test_creates_gitkeep_in_each_folder(): void
    {
        $this->artisan('fast:generate-structure', ['--structure' => 'standard'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('app/Services/.gitkeep'));
        $this->assertFileExists(base_path('app/Repositories/.gitkeep'));
    }

    public function test_skips_existing_folders(): void
    {
        File::makeDirectory(base_path('app/Services'), 0755, true);

        $this->artisan('fast:generate-structure', ['--structure' => 'standard'])
            ->expectsOutputToContain('Already exists')
            ->assertExitCode(0);
    }

    public function test_blocks_path_traversal(): void
    {
        config()->set('fast-setup.structures', [
            'malicious' => ['../../etc/passwd'],
        ]);

        $this->artisan('fast:generate-structure', ['--structure' => 'malicious'])
            ->expectsOutputToContain('Blocked unsafe path')
            ->assertExitCode(1);
    }

    public function test_fails_on_unknown_structure(): void
    {
        $this->artisan('fast:generate-structure', ['--structure' => 'unknown'])
            ->expectsOutputToContain('Unknown structure')
            ->assertExitCode(1);
    }

    public function test_dry_run_does_not_create_folders(): void
    {
        $this->artisan('fast:generate-structure', ['--structure' => 'standard', '--dry-run' => true])
            ->assertExitCode(0);

        $this->assertDirectoryDoesNotExist(base_path('app/Services'));
        $this->assertDirectoryDoesNotExist(base_path('app/Repositories'));
    }

    public function test_dry_run_shows_would_create_output(): void
    {
        $this->artisan('fast:generate-structure', ['--structure' => 'standard', '--dry-run' => true])
            ->expectsOutputToContain('Would create')
            ->assertExitCode(0);
    }
}
