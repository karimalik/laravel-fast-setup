<?php

namespace Karimalik\FastSetup\Services;

use Illuminate\Console\Command;

class PackageInstaller
{
    public function __construct(private Command $command) {}

    /**
     * Install a list of composer packages and run their post-install actions.
     *
     * @param  array<string>  $selectedPackages  Composer package names to install
     * @param  array<string, array>  $allPackages  Full package config from fast-setup.php
     */
    public function install(array $selectedPackages, array $allPackages): void
    {
        $total   = count($selectedPackages);
        $success = 0;
        $failed  = [];

        foreach ($selectedPackages as $index => $package) {
            $step = $index + 1;
            $this->command->newLine();
            $this->command->line("<fg=cyan>[{$step}/{$total}]</> Installing <fg=white>{$package}</>...");

            if (! $this->requirePackage($package)) {
                $failed[] = $package;
                continue;
            }

            $this->command->line("  <fg=green>✔ Installed:</> {$package}");

            $postInstall = $allPackages[$package]['post_install'] ?? [];
            $this->runPostInstall($package, $postInstall);

            $success++;
        }

        $this->command->newLine();
        $this->command->info("📦 Installation complete — {$success}/{$total} package(s) installed.");

        if (! empty($failed)) {
            $this->command->warn('The following packages failed to install:');
            foreach ($failed as $pkg) {
                $this->command->line("  <fg=red>✘ {$pkg}</>");
            }
        }
    }

    /**
     * Run `composer require` for a single package.
     */
    private function requirePackage(string $package): bool
    {
        exec("composer require {$package} 2>&1", $output, $exitCode);

        if ($exitCode !== 0) {
            $this->command->error("  ✘ Failed to install {$package}.");
            $this->command->line('  <fg=gray>' . implode("\n  ", $output) . '</>');
            return false;
        }

        return true;
    }

    /**
     * Run post-install steps: publish assets and/or migrate.
     */
    private function runPostInstall(string $package, array $postInstall): void
    {
        if (empty($postInstall)) {
            return;
        }

        // Run a standalone artisan command (e.g. telescope:install)
        if (! empty($postInstall['artisan'])) {
            $artisanCommand = $postInstall['artisan'];
            $this->command->line("  → Running: php artisan {$artisanCommand}");
            [$command, $args] = $this->parseArtisanCommand($artisanCommand);
            $this->command->call($command, $args);
        }

        // Publish vendor assets
        if (! empty($postInstall['publish'])) {
            $this->command->line('  → Publishing assets...');
            $args = $this->parsePublishArgs($postInstall['publish']);
            $this->command->call('vendor:publish', $args);
        }

        // Run migrations
        if (! empty($postInstall['migrate'])) {
            $this->command->line('  → Running migrations...');
            $this->command->call('migrate', ['--force' => true]);
        }
    }

    /**
     * Parse a publish string like:
     *   --provider="Foo\Bar" --tag="some-tag"
     * into an artisan argument array.
     */
    private function parsePublishArgs(string $publishString): array
    {
        $args = [];

        if (preg_match('/--provider="([^"]+)"/', $publishString, $matches)) {
            $args['--provider'] = $matches[1];
        }

        if (preg_match('/--tag="([^"]+)"/', $publishString, $matches)) {
            $args['--tag'] = $matches[1];
        }

        return $args;
    }

    /**
     * Split an artisan command string like "telescope:install --force"
     * into ['telescope:install', ['--force' => true]].
     */
    private function parseArtisanCommand(string $command): array
    {
        $parts = explode(' ', $command);
        $name  = array_shift($parts);
        $args  = [];

        foreach ($parts as $part) {
            if (str_starts_with($part, '--')) {
                $args[trim($part)] = true;
            }
        }

        return [$name, $args];
    }
}
