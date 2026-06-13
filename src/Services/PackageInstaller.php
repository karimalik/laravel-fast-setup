<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Services;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class PackageInstaller
{
    public function __construct(private Command $command) {}

    /**
     * @param  array<string>  $selectedPackages
     * @param  array<string, array>  $allPackages
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
            $this->command->warn('The following packages faile  d to install:');
            foreach ($failed as $pkg) {
                $this->command->line("  <fg=red>✘ {$pkg}</>");
            }
        }
    }

    private function requirePackage(string $package): bool
    {
        $process = new Process(['composer', 'require', $package]);
        $process->setTimeout(null);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->command->error("  ✘ Failed to install {$package}.");
            $this->command->line('  <fg=gray>' . $process->getErrorOutput() . '</>');
            return false;
        }

        return true;
    }

    private function runPostInstall(string $_package, array $postInstall): void
    {
        if (empty($postInstall)) {
            return;
        }

        // Run as a new process so freshly installed ServiceProviders are discovered
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
