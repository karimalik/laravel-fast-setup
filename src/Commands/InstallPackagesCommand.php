<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;
use karimalik\FastSetup\Services\PackageInstaller;

use function Laravel\Prompts\multiselect;

class InstallPackagesCommand extends Command
{
    protected $signature = 'fast:install-packages
        {--packages=* : Packages to install (skips interactive prompt)}
        {--dry-run : Preview installations without running composer}';

    protected $description = 'Interactively select and install packages for your Laravel project.';

    public function handle(): int
    {
        $allPackages = config('fast-setup.packages');
        $dryRun      = (bool) $this->option('dry-run');

        if (empty($allPackages)) {
            $this->warn('No packages defined in config/fast-setup.php.');
            return self::FAILURE;
        }

        $preSelected = $this->option('packages');

        if (! empty($preSelected)) {
            $invalid = array_diff($preSelected, array_keys($allPackages));

            if (! empty($invalid)) {
                $this->error('Unknown package(s): ' . implode(', ', $invalid));
                return self::FAILURE;
            }

            $selected = $preSelected;
        } else {
            $options = collect($allPackages)
                ->mapWithKeys(fn($config, $package) => [$package => $config['name']])
                ->toArray();

            $selected = multiselect(
                label: 'Select the packages you want to install',
                options: $options,
                hint: 'Space to select, Enter to confirm.',
                required: false,
            );
        }

        if (empty($selected)) {
            $this->warn('No packages selected. Skipping installation.');
            return self::SUCCESS;
        }

        $this->newLine();

        if ($dryRun) {
            $this->line('<fg=yellow>[DRY RUN]</> The following packages would be installed:');
            foreach ($selected as $pkg) {
                $this->line("  - <fg=cyan>{$pkg}</>");
            }
            $this->newLine();
            return self::SUCCESS;
        }

        $this->info('The following packages will be installed:');
        foreach ($selected as $pkg) {
            $this->line("  - <fg=cyan>{$pkg}</>");
        }
        $this->newLine();

        if (! $this->confirm('Proceed with installation?', true)) {
            $this->warn('Installation cancelled.');
            return self::SUCCESS;
        }

        $installer = new PackageInstaller($this);
        $installer->install($selected, $allPackages);

        return self::SUCCESS;
    }
}
