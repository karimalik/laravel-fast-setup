<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateStructureCommand extends Command
{
    protected $signature = 'fast:generate-structure {--structure= : The structure to generate (skip prompt)}';

    protected $description = 'Scaffold your preferred folder architecture inside the app directory';

    public function handle(): int
    {
        $structures = config('fast-setup.structures');

        if (empty($structures)) {
            $this->warn('No structures defined in config/fast-setup.php.');
            return self::FAILURE;
        }

        $this->info('📁 Available folder structures:');
        $this->newLine();

        foreach ($structures as $name => $folders) {
            $this->line("<fg=cyan>  [{$name}]</>");
            foreach ($folders as $folder) {
                $this->line("    <fg=gray>└── {$folder}</>");
            }
            $this->newLine();
        }

        $choice = $this->option('structure') ?? $this->choice(
            'Which structure do you want to generate?',
            array_keys($structures)
        );

        if (! array_key_exists($choice, $structures)) {
            $this->error("Unknown structure: {$choice}");
            return self::FAILURE;
        }

        $folders = $structures[$choice];
        $created = 0;
        $skipped = 0;
        $blocked = 0;

        $this->newLine();
        $this->info("Generating <fg=cyan>{$choice}</> structure...");
        $this->newLine();

        foreach ($folders as $folder) {

            $folder = ltrim($folder, '/\\');
            $path   = base_path($folder);

            if (
                ! str_starts_with(realpath(base_path()) . DIRECTORY_SEPARATOR, realpath(base_path()) . DIRECTORY_SEPARATOR)
                || str_contains($folder, '..')
            ) {
                $this->line("  <fg=red>✘ Blocked unsafe path:</>  {$folder}");
                $blocked++;
                continue;
            }

            if (File::isDirectory($path)) {
                $this->line("  <fg=yellow>⚠  Already exists:</>  {$folder}");
                $skipped++;
                continue;
            }

            File::makeDirectory($path, 0755, true);
            File::put("{$path}/.gitkeep", '');

            $this->line("  <fg=green>✔ Created:</>  {$folder}");
            $created++;
        }

        $this->newLine();
        $this->info("Done! {$created} folder(s) created, {$skipped} skipped, {$blocked} blocked.");

        return $blocked > 0 ? self::FAILURE : self::SUCCESS;
    }
}
