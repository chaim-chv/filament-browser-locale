<?php

namespace ChaimChv\FilamentBrowserLocale;

use Illuminate\Support\ServiceProvider;

class FilamentBrowserLocaleServiceProvider extends ServiceProvider
{
  protected const CONFIG_KEY = "filament-browser-locale";

  public function register(): void
  {
    $this->mergeConfigFrom(__DIR__ . "/../config/" . self::CONFIG_KEY . ".php", self::CONFIG_KEY);
  }

  public function boot(): void
  {
    $this->publishes(
      [
        __DIR__ . "/../config/" . self::CONFIG_KEY . ".php" => config_path(self::CONFIG_KEY . ".php"),
      ],
      self::CONFIG_KEY . "-config",
    );
  }
}
