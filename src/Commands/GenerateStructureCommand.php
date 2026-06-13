<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateStructureCommand extends Command
{
    protected $signature = 'fast:generate-structure
        {--structure= : The structure to generate (skip prompt)}
        {--dry-run : Preview folder creation without applying changes}';

    protected $description = 'Scaffold your preferred folder architecture inside the app directory';

    public function handle(): int
    {
        $structures      = config('fast-setup.structures');
        $structureOption = $this->option('structure');
        $dryRun          = (bool) $this->option('dry-run');

        if (empty($structures)) {
            $this->warn('No structures defined in config/fast-setup.php.');
            return self::FAILURE;
        }

        if (! $structureOption) {
            $this->info('📁 Available folder structures:');
            $this->newLine();

            foreach ($structures as $name => $folders) {
                $this->line("<fg=cyan>  [{$name}]</>");
                foreach ($folders as $folder) {
                    $this->line("    <fg=gray>└── {$folder}</>");
                }
                $this->newLine();
            }
        }

        $choice = $structureOption ?? $this->choice(
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

        if ($dryRun) {
            $this->line("<fg=yellow>[DRY RUN]</> Previewing <fg=cyan>{$choice}</> structure (no changes will be applied)...");
        } else {
            $this->info("Generating <fg=cyan>{$choice}</> structure...");
        }

        $this->newLine();

        foreach ($folders as $folder) {
            $folder = ltrim($folder, '/\\');
            $path   = base_path($folder);

            if (str_contains($folder, '..')) {
                $this->line("  <fg=red>✘ Blocked unsafe path:</>  {$folder}");
                $blocked++;
                continue;
            }

            if (File::isDirectory($path)) {
                $this->line("  <fg=yellow>⚠  Already exists:</>  {$folder}");
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->line("  <fg=cyan>  Would create:</>  {$folder}");
            } else {
                File::makeDirectory($path, 0755, true);
                File::put("{$path}/.gitkeep", '');
                $this->line("  <fg=green>✔ Created:</>  {$folder}");
            }

            $created++;
        }

        $this->newLine();

        if ($dryRun) {
            $this->info("{$created} folder(s) would be created, {$skipped} already exist.");
        } else {
            $this->info("Done! {$created} folder(s) created, {$skipped} skipped, {$blocked} blocked.");
        }

        return $blocked > 0 ? self::FAILURE : self::SUCCESS;
    }
}
