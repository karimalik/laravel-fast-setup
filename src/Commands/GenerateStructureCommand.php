<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateStructureCommand extends Command
{
    protected $signature = 'fast:generate-structure';

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

        $choice = $this->choice(
            'Which structure do you want to generate?',
            array_keys($structures)
        );

        $folders  = $structures[$choice];
        $created  = 0;
        $skipped  = 0;

        $this->newLine();
        $this->info("Generating <fg=cyan>{$choice}</> structure...");
        $this->newLine();

        foreach ($folders as $folder) {
            $path = base_path($folder);

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
        $this->info("Done! {$created} folder(s) created, {$skipped} skipped.");

        return self::SUCCESS;
    }
}