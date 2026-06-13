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

    public function test_runs_only_selected_steps(): void
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
}
