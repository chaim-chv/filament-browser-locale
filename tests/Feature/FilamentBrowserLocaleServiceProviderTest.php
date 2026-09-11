<?php

use ChaimChv\FilamentBrowserLocale\FilamentBrowserLocaleServiceProvider;
use Illuminate\Support\ServiceProvider;

it('merges the package config into the application config', function () {
    expect(config('filament-browser-locale.supported_locales'))->toBe([])
        ->and(config('filament-browser-locale.normalize'))->toBeTrue();
});

it('registers a publish group mapping the package config to the app config path', function () {
    $provider = new FilamentBrowserLocaleServiceProvider(app());
    $provider->boot();

    $paths = ServiceProvider::pathsToPublish(
        FilamentBrowserLocaleServiceProvider::class,
        'filament-browser-locale-config',
    );

    $expectedSource = realpath(__DIR__ . '/../../config/filament-browser-locale.php');
    $expectedDestination = config_path('filament-browser-locale.php');

    $normalized = [];
    foreach ($paths as $source => $destination) {
        $normalized[realpath($source)] = $destination;
    }

    expect($normalized)->toHaveKey($expectedSource)
        ->and($normalized[$expectedSource] ?? null)->toBe($expectedDestination);
});

it('registers the provider in the laravel extra block', function () {
    $composer = json_decode(file_get_contents(__DIR__ . '/../../composer.json'), true);

    expect($composer['extra']['laravel']['providers'])
        ->toContain(FilamentBrowserLocaleServiceProvider::class);
});
