<?php

namespace ChaimChv\FilamentBrowserLocale\Tests;

use ChaimChv\FilamentBrowserLocale\FilamentBrowserLocaleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentBrowserLocaleServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.locale', 'en');
        $app['config']->set('filament-browser-locale.supported_locales', []);
        $app['config']->set('filament-browser-locale.normalize', true);
    }
}
