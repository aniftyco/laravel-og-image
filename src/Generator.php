<?php

namespace NiftyCo\OgImage;

use HeadlessChromium\Browser\ProcessAwareBrowser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use Illuminate\View\Factory;

class Generator
{
  private ProcessAwareBrowser $browser;

  public Page $page;

  public function __construct(private Factory $view, private array $config = [])
  {
    $this->browser = (new BrowserFactory($config['binary']))->createBrowser([
      'noSandbox' => $config['no_sandbox'],
      'ignoreCertificateErrors' => $config['ignore_certificate_errors'],
      'customFlags' => $config['custom_flags'],
    ]);
  }

  public function make(string $view, array $data = []): Image
  {
    $this->page = $this->browser->createPage();
    $this->page->setHtml($this->view->make('og-image.' . $view, $data)->render(), eventName: $this->config['event_name']);

    $this->page->evaluate($this->injectJs());

    $this->page->setViewport($this->config['view_port']['width'], $this->config['view_port']['height']);

    return new Image($this->page->screenshot(), $this->config);
  }

  private function injectJs(): string
  {
    // Wait until all images and fonts have loaded
    // Taken from: https://github.com/svycal/og-image/blob/main/priv/js/take-screenshot.js#L42C5-L63
    // See: https://github.blog/2021-06-22-framework-building-open-graph-images/#some-performance-gotchas

    return <<<JS
      const selectors = Array.from(document.querySelectorAll("img"));
      await Promise.all([
        document.fonts.ready,
        ...selectors.map((img) => {
          // Image has already finished loading, let’s see if it worked
          if (img.complete) {
            // Image loaded and has presence
            if (img.naturalHeight !== 0) return;
            // Image failed, so it has no height
            throw new Error("Image failed to load");
          }
          // Image hasn’t loaded yet, added an event listener to know when it does
          return new Promise((resolve, reject) => {
            img.addEventListener("load", resolve);
            img.addEventListener("error", reject);
          });
        })
      ]);
    JS;
  }
}
