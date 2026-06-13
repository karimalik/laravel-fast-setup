<?php

use karimalik\FastSetup\FastSetupServiceProvider;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\File;

class GenerateEnvCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FastSetupServiceProvider::class];
    }

    protected function tearDown(): void
    {
        File::delete(base_path('.env'));
        File::delete(base_path('.env.staging'));
        File::delete(base_path('.env.production'));

        parent::tearDown();
    }

    public function test_generates_local_env_file(): void
    {
        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['local'],
                ['local', 'staging', 'production']
            )
            ->assertExitCode(0);

        $this->assertFileExists(base_path('.env'));
    }

    public function test_generates_staging_env_file(): void
    {
        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['staging'],
                ['local', 'staging', 'production']
            )
            ->assertExitCode(0);

        $this->assertFileExists(base_path('.env.staging'));
    }

    public function test_generates_production_env_file(): void
    {
        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['production'],
                ['local', 'staging', 'production']
            )
            ->assertExitCode(0);

        $this->assertFileExists(base_path('.env.production'));
    }

    public function test_generates_multiple_env_files(): void
    {
        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['staging', 'production'],
                ['local', 'staging', 'production']
            )
            ->assertExitCode(0);

        $this->assertFileExists(base_path('.env.staging'));
        $this->assertFileExists(base_path('.env.production'));
    }

    public function test_skips_existing_env_file_when_user_declines(): void
    {
        File::put(base_path('.env'), 'EXISTING_CONTENT');

        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['local'],
                ['local', 'staging', 'production']
            )
            ->expectsConfirmation('.env already exists. Overwrite?', 'no')
            ->assertExitCode(0);

        $this->assertEquals('EXISTING_CONTENT', File::get(base_path('.env')));
    }

    public function test_overwrites_existing_env_file_when_user_confirms(): void
    {
        File::put(base_path('.env'), 'EXISTING_CONTENT');

        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['local'],
                ['local', 'staging', 'production']
            )
            ->expectsConfirmation('.env already exists. Overwrite?', 'yes')
            ->assertExitCode(0);

        $this->assertNotEquals('EXISTING_CONTENT', File::get(base_path('.env')));
    }

    public function test_production_env_has_correct_values(): void
    {
        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['production'],
                ['local', 'staging', 'production']
            )
            ->assertExitCode(0);

        $content = File::get(base_path('.env.production'));

        $this->assertStringContainsString('APP_DEBUG=false', $content);
        $this->assertStringContainsString('LOG_LEVEL=error', $content);
        $this->assertStringContainsString('CACHE_DRIVER=redis', $content);
        $this->assertStringContainsString('QUEUE_CONNECTION=redis', $content);
        $this->assertStringContainsString('SESSION_DRIVER=redis', $content);
    }

    public function test_local_env_has_correct_values(): void
    {
        $this->artisan('fast:generate-env')
            ->expectsChoice(
                'Which environment(s) do you want to generate .env files for?',
                ['local'],
                ['local', 'staging', 'production']
            )
            ->assertExitCode(0);

        $content = File::get(base_path('.env'));

        $this->assertStringContainsString('APP_DEBUG=true', $content);
        $this->assertStringContainsString('LOG_LEVEL=debug', $content);
        $this->assertStringContainsString('CACHE_DRIVER=file', $content);
        $this->assertStringContainsString('QUEUE_CONNECTION=sync', $content);
        $this->assertStringContainsString('SESSION_DRIVER=file', $content);
    }
}
