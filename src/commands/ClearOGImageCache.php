<?php

namespace NiftyCo\OgImage\Commands;

use Illuminate\Console\Command;

class ClearOGImageCache extends Command
{
  protected $signature = 'og-image:clear-cache';

  protected $description = 'Clear the cache of generated OG images';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $dir = storage_path('framework/cache/og-images');

    if ($this->laravel['files']->exists($dir)) {
      $this->laravel['files']->cleanDirectory($dir);
    }

    $this->info('OG image cache cleared!');
  }
}
