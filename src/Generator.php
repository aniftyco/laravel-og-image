<?php

namespace NiftyCo\OgImage;

use HeadlessChromium\Browser\ProcessAwareBrowser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use Illuminate\Http\Response;
use Illuminate\View\Factory;

class Generator
{
  private ProcessAwareBrowser $browser;

  public Page $page;

  public function __construct(private Factory $view, array $options = [], array $flags = [])
  {
    $this->browser = (new BrowserFactory())->createBrowser([
      ...$options,
      'noSandbox' => false,
      'ignoreCertificateErrors' => true,
      'customFlags' => [
        ...$flags,
        '--disable-gpu',
        '--disable-dev-shm-usage',
        '--disable-setuid-sandbox',
      ],
    ]);
  }

  public function make(string $view, array $data = []): Response
  {
    $this->page = $this->browser->createPage();
    $this->page->setHtml($this->view->make('og-image.' . $view, $data)->render(), eventName: Page::NETWORK_IDLE);

    $this->page->evaluate($this->injectJs());

    $this->page->setViewport(1200, 630);

    return response(base64_decode($this->page->screenshot()->getBase64()), headers: [
      'content-type' => 'image/png',
      'cache-control' => 'public, immutable, no-transform, s-maxage=31536000, max-age=31536000'
    ]);
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
