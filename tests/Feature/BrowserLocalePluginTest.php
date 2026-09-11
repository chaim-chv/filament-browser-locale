<?php

use ChaimChv\FilamentBrowserLocale\BrowserLocalePlugin;
use Filament\Panel;
use Illuminate\Http\Request;

function bootPlugin(
    BrowserLocalePlugin $plugin,
    ?string $acceptLanguage = null,
    string $initialLocale = 'en',
): void {
    app()->setLocale($initialLocale);

    $server = ['HTTP_ACCEPT_LANGUAGE' => $acceptLanguage];
    app()->instance('request', Request::create('/panel', 'GET', [], [], [], $server));

    $plugin->boot(Mockery::mock(Panel::class));
}

it('has a stable plugin id', function () {
    expect(BrowserLocalePlugin::make()->getId())->toBe('chaim-chv-browser-locale');
});

it('returns the same instance from fluent setters', function () {
    $plugin = BrowserLocalePlugin::make();

    expect($plugin->supportedLocales(['en']))->toBe($plugin)
        ->and($plugin->normalize(false))->toBe($plugin);
});

it('sets the locale from the browser preferred language', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['en', 'he']),
        acceptLanguage: 'he-IL,he;q=0.9,en;q=0.8',
    );

    expect(app()->getLocale())->toBe('he');
});

it('normalizes language codes to their base language by default', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['en']),
        acceptLanguage: 'en-US,en;q=0.9',
    );

    expect(app()->getLocale())->toBe('en');
});

it('normalizes a regional language code even without a bare fallback', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['he']),
        acceptLanguage: 'he-IL',
    );

    expect(app()->getLocale())->toBe('he');
});

it('matches a regional locale configured with a dash when normalization is disabled', function () {
    bootPlugin(
        BrowserLocalePlugin::make()
            ->supportedLocales(['en-US'])
            ->normalize(false),
        acceptLanguage: 'en-US',
    );

    expect(app()->getLocale())->toBe('en-US');
});

it('keeps full language codes when normalization is disabled', function () {
    bootPlugin(
        BrowserLocalePlugin::make()
            ->supportedLocales(['en-US', 'en-GB'])
            ->normalize(false),
        acceptLanguage: 'en-GB,en;q=0.9',
    );

    expect(app()->getLocale())->toBe('en-GB');
});

it('does not match a full language code when normalization is disabled and only the base is supported', function () {
    bootPlugin(
        BrowserLocalePlugin::make()
            ->supportedLocales(['en'])
            ->normalize(false),
        acceptLanguage: 'en-US',
        initialLocale: 'xx',
    );

    expect(app()->getLocale())->toBe('xx');
});

it('falls back to config supported locales when none are passed to the plugin', function () {
    config()->set('filament-browser-locale.supported_locales', ['fr', 'de']);

    bootPlugin(
        BrowserLocalePlugin::make(),
        acceptLanguage: 'de-DE,de;q=0.9,fr;q=0.8',
    );

    expect(app()->getLocale())->toBe('de');
});

it('uses the config normalize value when not set on the plugin', function () {
    config()->set('filament-browser-locale.supported_locales', ['en-US']);
    config()->set('filament-browser-locale.normalize', false);

    bootPlugin(
        BrowserLocalePlugin::make(),
        acceptLanguage: 'en-US,en;q=0.9',
    );

    expect(app()->getLocale())->toBe('en-US');
});

it('does not change the locale when no supported locales are configured', function () {
    bootPlugin(
        BrowserLocalePlugin::make(),
        acceptLanguage: 'en-US,en;q=0.9',
        initialLocale: 'xx',
    );

    expect(app()->getLocale())->toBe('xx');
});

it('does not change the locale when there is no accept-language header', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['en']),
        acceptLanguage: null,
        initialLocale: 'xx',
    );

    expect(app()->getLocale())->toBe('xx');
});

it('respects browser preference order (q-values)', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['fr', 'he']),
        acceptLanguage: 'fr;q=0.8,he;q=0.9',
    );

    expect(app()->getLocale())->toBe('he');
});

it('skips unsupported languages and falls through to a supported one', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['en']),
        acceptLanguage: 'de-DE,de;q=0.9,en;q=0.8',
    );

    expect(app()->getLocale())->toBe('en');
});

it('leaves the locale untouched when no browser language is supported', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['en', 'he']),
        acceptLanguage: 'fr-FR,fr;q=0.9,de;q=0.8',
        initialLocale: 'xx',
    );

    expect(app()->getLocale())->toBe('xx');
});

it('handles wildcard and malformed accept-language headers gracefully', function () {
    bootPlugin(
        BrowserLocalePlugin::make()->supportedLocales(['en']),
        acceptLanguage: '*',
        initialLocale: 'xx',
    );

    expect(app()->getLocale())->toBe('xx');
});
