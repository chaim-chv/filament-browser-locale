# Filament Browser Locale

Filament panel plugin that Automatically sets the Filament panel locale based on the browser's `Accept-Language` header ("preferred languages").

## Requirements

- PHP 8.2+
- Laravel 11.28+ / 12 / 13
- Filament v4 or v5

## Features
- Filament v4 and v5 compatible
- Normalizes `en-US` → `en` (configurable)
- Configurable supported locales
- No global middleware required

## Installation

```bash
composer require chaim-chv/filament-browser-locale
```

## Usage

To use the plugin, simply add it to your Filament panel's plugins array.

```php
BrowserLocalePlugin::make()
```

To specify supported locales, use the `supportedLocales` method:

```php
BrowserLocalePlugin::make()
    ->supportedLocales(['en', 'he'])
```

To disable language code normalization (e.g., to keep `en-US` as `en-US` instead of normalizing to `en`):

```php
BrowserLocalePlugin::make()
    ->supportedLocales(['en-US', 'en-GB', 'he'])
    ->normalize(false)
```

Full filament panel example:
```php
use ChaimChv\FilamentBrowserLocale\BrowserLocalePlugin;

class ExamplePanel extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('example')
            ->path('/example')
            ->plugins([
                BrowserLocalePlugin::make()
                    ->supportedLocales(['en', 'he']),
            ]);
    }
}
```

You can also set the supported locales via global configuration (see below), in which case you can initialize the plugin without any options.

## Testing

```bash
composer test
```

The test suite runs against both Filament v4 and v5 via the CI matrix.

## Configuration

Publish the configuration file:
```bash
php artisan vendor:publish --tag=filament-browser-locale-config
```

Available options in `config/filament-browser-locale.php`:

- `supported_locales`: Array of supported locale codes (default: `[]`)
- `normalize`: Whether to normalize language codes like `en-US` to `en` (default: `true`)

[@chaim-chv](https://github.com/chaim-chv/) © 2026
