<?php

namespace Karimalik\FastSetup\Commands;

use Illuminate\Console\Command;

class FastSetupCommand extends Command
{
    protected $signature = 'fast:setup';
    protected $description = 'Run the full interactive Laravel project setup wizard';

    public function handle(): int
    {
        $this->displayBanner();

        if ($this->confirm(' Do you want to install packages?', true)) {
            $this->call('fast:install-packages');
        }

        if ($this->confirm(' Do you want to generate a folder structure?', true)) {
            $this->call('fast:generate-structure');
        }

        if ($this->confirm(' Do you want to generate .env files?', true)) {
            $this->call('fast:generate-env');
        }

        $this->newLine();
        $this->info('✅ Project setup complete! Happy coding 🚀');

        return self::SUCCESS;
    }

    private function displayBanner(): void
    {
        $this->newLine();
        $this->line('<fg=cyan>
  ██████╗  █████╗ ███████╗████████╗    ███████╗███████╗████████╗██╗   ██╗██████╗
  ██╔══██╗██╔══██╗██╔════╝╚══██╔══╝    ██╔════╝██╔════╝╚══██╔══╝██║   ██║██╔══██╗
  ██████╔╝███████║███████╗   ██║       ███████╗█████╗     ██║   ██║   ██║██████╔╝
  ██╔══██╗██╔══██║╚════██║   ██║       ╚════██║██╔══╝     ██║   ██║   ██║██╔═══╝
  ██║  ██║██║  ██║███████║   ██║       ███████║███████╗   ██║   ╚██████╔╝██║
  ╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝   ╚═╝       ╚══════╝╚══════╝   ╚═╝    ╚═════╝ ╚═╝
</>');
        $this->line('<fg=cyan>  Laravel Fast Setup — by karimalik</> ');
        $this->newLine();
    }
}
