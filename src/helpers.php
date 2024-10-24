<?php

if (!function_exists('og_image')) {
  /**
   * Get the evaluated image for the given open graph template.
   *
   * @param  string|null  $view
   * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
   * @param  array  $mergeData
   * @return    * @return ($view is null ? \NiftyCo\OgImage\Generator : \Illuminate\Http\Response)

   */
  function og_image($view = null, $data = [])
  {
    $generator = app('og-image.generator');

    if (func_num_args() === 0) {
      return $generator;
    }

    return $generator->make($view, $data);
  }
}
