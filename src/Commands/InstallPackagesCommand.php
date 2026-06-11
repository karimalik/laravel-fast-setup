<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;
use karimalik\FastSetup\Services\PackageInstaller;

class InstallPackagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fast:install-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively select and install packages for your Laravel project.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $allPackages = config('fast-setup.packages');

        if (empty($allPackages)) {
            $this->warn('No packages defined in config/fast-setup.php.');
            return self::FAILURE;
        }

        $choices = collect($allPackages)
            ->map(fn($config, $package) => "{$config['name']}  [{$package}]")
            ->values()
            ->toArray();

        $this->info('Select the packages you want to install:');
        $this->line('<fg=gray>Use space to select multiple, enter to confirm.</fg=gray>');
        $this->newLine();

        $selected = $this->choice(
            'Packages',
            $choices,
            null,
            null,
            true 
        );

        if (empty($selected)) {
            $this->warn('No packages selected. Skipping installation.');
            return self::SUCCESS;
        }

        $packageKeys = array_keys($allPackages);
        $selectedPackages = collect($selected)
            ->map(function ($label) use ($packageKeys, $allPackages) {
                foreach ($packageKeys as $key) {
                    $expectedLabel = "{$allPackages[$key]['name']}  [{$key}]";
                    if ($label === $expectedLabel) {
                        return $key;
                    }
                }
                return null;
            })
            ->filter()
            ->values()
            ->toArray();

        $this->newLine();
        $this->info('The following packages will be installed:');
        foreach ($selectedPackages as $pkg) {
            $this->line("  - <fg=cyan>{$pkg}</>");
        }
        $this->newLine();

        if (! $this->confirm('Proceed with installation?', true)) {
            $this->warn('Installation cancelled.');
            return self::SUCCESS;
        }

        $installer = new PackageInstaller($this);
        $installer->install($selectedPackages, $allPackages);

        return self::SUCCESS;
    }
}
