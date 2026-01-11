<?php

namespace ChaimChv\FilamentBrowserLocale;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class BrowserLocalePlugin implements Plugin
{
  protected const PLUGIN_ID = "chaim-chv-browser-locale";
  protected const CONFIG_KEY = "filament-browser-locale";

  protected array $locales = [];
  protected ?bool $normalize = null;

  public static function make(): static
  {
    return new static();
  }

  public function getId(): string
  {
    return self::PLUGIN_ID;
  }

  public function supportedLocales(array $locales): static
  {
    $this->locales = $locales;
    return $this;
  }

  public function normalize(bool $normalize = true): static
  {
    $this->normalize = $normalize;
    return $this;
  }

  public function boot(Panel $panel): void
  {
    $supported = $this->locales ?: config(self::CONFIG_KEY . ".supported_locales", []);
    $normalize = $this->normalize ?? config(self::CONFIG_KEY . ".normalize", true);

    if (empty($supported)) {
      return;
    }

    $languages = request()->getLanguages();

    if (empty($languages)) {
      return;
    }

    foreach ($languages as $language) {
      $locale = $normalize ? Str::before($language, "-") : $language;
      if (in_array($locale, $supported, true)) {
        App::setLocale($locale);
        return;
      }
    }
  }

  public function register(Panel $panel): void {}
}
