<?php

namespace NiftyCo\OgImage\Controllers;

use Illuminate\Http\Request;

class OGImageController
{
  public function __invoke(Request $request)
  {
    $hash = md5(json_encode($request->query()));
    $dir = storage_path('framework/cache/og-images');
    $path = "{$dir}/{$hash}.png";

    // Ensure the directory exists
    if (!app('files')->exists($dir)) {
      app('files')->makeDirectory($dir);
    }

    // does the image already exist?
    if (app('files')->exists($path)) {
      return response()->file($path, headers: ['Content-Type' => 'image/png']);
    }

    $image = app('og-image.generator')->make($request->get('template'), $request->except('template'));

    app('files')->put($path, $image->toPng());

    return $image;
  }
}
