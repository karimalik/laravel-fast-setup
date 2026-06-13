<?php

use karimalik\FastSetup\FastSetupServiceProvider;
use Orchestra\Testbench\TestCase;

class FastSetupCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FastSetupServiceProvider::class];
    }

    public function test_skips_all_steps_when_user_declines(): void
    {
        $this->artisan('fast:setup')
            ->expectsConfirmation(' Do you want to install packages?', 'no')
            ->expectsConfirmation(' Do you want to generate a folder structure?', 'no')
            ->expectsConfirmation(' Do you want to generate .env files?', 'no')
            ->assertExitCode(0);
    }

    public function test_displays_success_message(): void
    {
        $this->artisan('fast:setup')
            ->expectsConfirmation(' Do you want to install packages?', 'no')
            ->expectsConfirmation(' Do you want to generate a folder structure?', 'no')
            ->expectsConfirmation(' Do you want to generate .env files?', 'no')
            ->expectsOutputToContain('Project setup complete')
            ->assertExitCode(0);
    }

    public function test_preset_runs_complete_dry_run(): void
    {
        $this->artisan('fast:setup', ['--preset' => 'api', '--dry-run' => true])
            ->expectsOutputToContain('Dry run complete')
            ->assertExitCode(0);
    }

    public function test_preset_fails_with_unknown_preset(): void
    {
        $this->artisan('fast:setup', ['--preset' => 'unknown'])
            ->expectsOutputToContain('Unknown preset')
            ->assertExitCode(1);
    }

    public function test_dry_run_shows_dry_run_label(): void
    {
        $this->artisan('fast:setup', ['--dry-run' => true])
            ->expectsConfirmation(' Do you want to install packages?', 'no')
            ->expectsConfirmation(' Do you want to generate a folder structure?', 'no')
            ->expectsConfirmation(' Do you want to generate .env files?', 'no')
            ->expectsOutputToContain('DRY RUN')
            ->assertExitCode(0);
    }
}
