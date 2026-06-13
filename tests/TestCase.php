<?php

declare(strict_types=1);

namespace karimalik\FastSetup\Tests;

use karimalik\FastSetup\FastSetupServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [FastSetupServiceProvider::class];
    }
}
