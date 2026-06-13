<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;

class FastSetupCommand extends Command
{
    protected $signature = 'fast:setup {--no-interaction}';
    protected $description = 'Run the full interactive Laravel project setup wizard';


    public function handle(): int
    {
        $this->displayBanner();

        $noInteraction = $this->option('no-interaction');

        if ($noInteraction || $this->confirm(' Do you want to install packages?', true)) {
            $this->call('fast:install-packages', ['--no-interaction' => true]);
        }

        if ($noInteraction || $this->confirm(' Do you want to generate a folder structure?', true)) {
            $this->call('fast:generate-structure', ['--no-interaction' => true]);
        }

        if ($noInteraction || $this->confirm(' Do you want to generate .env files?', true)) {
            $this->call('fast:generate-env', ['--no-interaction' => true]);
        }

        $this->newLine();
        $this->info('✅ Project setup complete! Happy coding 🚀');

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
