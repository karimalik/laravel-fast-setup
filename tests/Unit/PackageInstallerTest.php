<?php

use karimalik\FastSetup\Services\PackageInstaller;
use Illuminate\Console\Command;
use Mockery\MockInterface;

uses(\Orchestra\Testbench\TestCase::class);

afterEach(function () {
    Mockery::close();
});

it('completes without warning when no packages are selected', function () {
    $command = mock(Command::class, function (MockInterface $mock) {
        $mock->shouldReceive('newLine')->zeroOrMoreTimes();
        $mock->shouldReceive('line')->zeroOrMoreTimes();
        $mock->shouldReceive('info')->once()->with(Mockery::on(
            fn (string $msg) => str_contains($msg, '0/0')
        ));
        $mock->shouldNotReceive('warn');
    });

    $installer = new PackageInstaller($command);
    $installer->install([], []);
});

it('returns false when process fails', function () {
    $command = mock(Command::class, function (MockInterface $mock) {
        $mock->shouldReceive('error')->zeroOrMoreTimes();
        $mock->shouldReceive('line')->zeroOrMoreTimes();
    });

    $installer = new PackageInstaller($command);

    $reflection = new ReflectionClass($installer);
    $method     = $reflection->getMethod('requirePackage');

    expect($method->invoke($installer, 'this-package-does-not-exist/xyz'))
        ->toBeFalse();
});

it('warns when failed packages exist', function () {
    $command = mock(Command::class, function (MockInterface $mock) {
        $mock->shouldReceive('newLine')->zeroOrMoreTimes();
        $mock->shouldReceive('line')->zeroOrMoreTimes();
        $mock->shouldReceive('info')->zeroOrMoreTimes();
        $mock->shouldReceive('error')->zeroOrMoreTimes();
        $mock->shouldReceive('warn')->once();
    });

    $installer = new PackageInstaller($command);

    $installer->install(
        ['invalid/package-xyz'],
        [
            'invalid/package-xyz' => [
                'name'         => 'Invalid Package',
                'post_install' => [],
            ],
        ]
    );
});
