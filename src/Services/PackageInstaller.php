<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Services;

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

        if (! empty($postInstall['artisan'])) {
            $this->runArtisan($postInstall['artisan']);
        }

        if (! empty($postInstall['publish'])) {
            $this->runArtisan("vendor:publish {$postInstall['publish']}");
        }

        if (! empty($postInstall['migrate'])) {
            $this->command->call('migrate', ['--force' => true]);
        }
    }

    private function runArtisan(string $artisanCommand): void
    {
        $this->command->line("  → Running: php artisan {$artisanCommand}");
        exec("php artisan {$artisanCommand} 2>&1", $output, $exitCode);

        if ($exitCode !== 0) {
            $this->command->warn("  ⚠ Command failed: php artisan {$artisanCommand}");
            $this->command->line('  <fg=gray>' . implode("\n  ", $output) . '</>');
        }
    }
}
