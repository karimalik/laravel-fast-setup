<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateEnvCommand extends Command
{
    protected $signature = 'fast:generate-env';

    protected $description = 'Generate .env files for one or multiple environments';

    /**
     * Environments available for generation.
     */
    private array $environments = ['local', 'staging', 'production'];

    public function handle(): int
    {
        $this->info('⚙️  Environment file generator');
        $this->newLine();

        $selected = $this->choice(
            'Which environment(s) do you want to generate .env files for?',
            $this->environments,
            0,
            null,
            true 
        );

        $this->newLine();

        foreach ($selected as $env) {
            $this->generateEnv($env);
        }

        $this->newLine();
        $this->info('✔ Environment files generated successfully.');

        return self::SUCCESS;
    }

    private function generateEnv(string $env): void
    {
        $filename   = $env === 'local' ? '.env' : ".env.{$env}";
        $stubPath   = __DIR__ . "/../../stubs/env/.env.{$env}.stub";
        $targetPath = base_path($filename);

        if (File::exists($targetPath)) {
            if (! $this->confirm("  <fg=yellow>{$filename} already exists. Overwrite?</>", false)) {
                $this->line("  <fg=yellow>⚠  Skipped:</>  {$filename}");
                return;
            }
        }

        if (File::exists($stubPath)) {
            File::copy($stubPath, $targetPath);
        } else {
            File::put($targetPath, $this->buildDefaultContent($env));
        }

        $this->line("  <fg=green>✔ Generated:</>  {$filename}");
    }

    private function buildDefaultContent(string $env): string
    {
        $debug  = $env === 'production' ? 'false' : 'true';
        $log    = $env === 'production' ? 'error'  : 'debug';
        $cache  = $env === 'production' ? 'redis'  : 'file';
        $queue  = $env === 'production' ? 'redis'  : 'sync';
        $session = $env === 'production' ? 'redis' : 'file';

        return <<<ENV
APP_NAME=Laravel
APP_ENV={$env}
APP_KEY=
APP_DEBUG={$debug}
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL={$log}

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER={$cache}
QUEUE_CONNECTION={$queue}
SESSION_DRIVER={$session}
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="\${APP_NAME}"
ENV;
    }
}