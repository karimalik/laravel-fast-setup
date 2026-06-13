<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;

class FastSetupCommand extends Command
{
    protected $signature = 'fast:setup
        {--preset= : Run a named preset configuration without interaction}
        {--dry-run : Preview all changes without applying them}
        {--skip-interaction : Run all steps without confirmation prompts}';

    protected $description = 'Run the full interactive Laravel project setup wizard';

    public function handle(): int
    {
        $this->displayBanner();

        $preset = $this->option('preset');
        $dryRun = (bool) $this->option('dry-run');

        if ($preset !== null) {
            return $this->runPreset($preset, $dryRun);
        }

        return $this->runInteractive($dryRun);
    }

    private function runPreset(string $presetName, bool $dryRun): int
    {
        $presets = config('fast-setup.presets', []);

        if (! array_key_exists($presetName, $presets)) {
            $available = implode(', ', array_keys($presets));
            $this->error("Unknown preset \"{$presetName}\". Available: {$available}");
            return self::FAILURE;
        }

        $preset = $presets[$presetName];

        $this->line("Loading preset: <fg=cyan>{$preset['name']}</>");

        if ($dryRun) {
            $this->line('<fg=yellow>[DRY RUN]</> No changes will be applied.');
        }

        $this->newLine();

        $args = ['--dry-run' => $dryRun];

        if (! empty($preset['packages'])) {
            $this->call('fast:install-packages', array_merge($args, ['--packages' => $preset['packages']]));
        }

        if (! empty($preset['structure'])) {
            $this->call('fast:generate-structure', array_merge($args, ['--structure' => $preset['structure']]));
        }

        if (! empty($preset['envs'])) {
            $this->call('fast:generate-env', array_merge($args, ['--envs' => $preset['envs']]));
        }

        $this->newLine();
        $this->info($dryRun ? 'Dry run complete. No changes were made.' : '✅ Project setup complete! Happy coding 🚀');

        return self::SUCCESS;
    }

    private function runInteractive(bool $dryRun): int
    {
        $noInteraction = $this->option('skip-interaction');

        if ($dryRun) {
            $this->line('<fg=yellow>[DRY RUN]</> No changes will be applied.');
            $this->newLine();
        }

        $args = ['--dry-run' => $dryRun];

        if ($noInteraction || $this->confirm(' Do you want to install packages?', true)) {
            $this->call('fast:install-packages', $args);
        }

        if ($noInteraction || $this->confirm(' Do you want to generate a folder structure?', true)) {
            $this->call('fast:generate-structure', $args);
        }

        if ($noInteraction || $this->confirm(' Do you want to generate .env files?', true)) {
            $this->call('fast:generate-env', $args);
        }

        $this->newLine();
        $this->info($dryRun ? 'Dry run complete. No changes were made.' : '✅ Project setup complete! Happy coding 🚀');

        return self::SUCCESS;
    }

    private function displayBanner(): void
    {
        $this->newLine();
        $this->line('<fg=#FF2D20>
  ███████╗ █████╗ ███████╗████████╗    ███████╗███████╗████████╗██╗   ██╗██████╗
  ██╔════╝██╔══██╗██╔════╝╚══██╔══╝    ██╔════╝██╔════╝╚══██╔══╝██║   ██║██╔══██╗
  █████╗  ███████║███████╗   ██║       ███████╗█████╗     ██║   ██║   ██║██████╔╝
  ██╔══╝  ██╔══██║╚════██║   ██║       ╚════██║██╔══╝     ██║   ██║   ██║██╔═══╝
  ██║     ██║  ██║███████║   ██║       ███████║███████╗   ██║   ╚██████╔╝██║
  ╚═╝     ╚═╝  ╚═╝╚══════╝   ╚═╝       ╚══════╝╚══════╝   ╚═╝    ╚═════╝ ╚═╝
</>');
        $this->line('<fg=cyan>  Laravel Fast Setup — by Karim Kompissi</>');
        $this->newLine();
    }
}
