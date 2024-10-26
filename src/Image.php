<?php

namespace NiftyCo\OgImage;

use Illuminate\Http;
use HeadlessChromium\PageUtils\PageScreenshot;

class Image extends Http\Response
{
    private Base64EncodedFile $file;

    public function __construct(private PageScreenshot $screenshot, private array $config = [])
    {
        parent::__construct(base64_decode($screenshot->getBase64()));

        $this->headers->set('content-type', 'image/png');
        $this->headers->set('cache-control', implode(', ', [
            'public',
            'no-transform',
            'max-age=' . $config['cache']['maxage'],
            'stale-while-revalidate=' . $config['cache']['revalidate'],
        ]));
    }

    private function getFile(): Base64EncodedFile
    {
        return $this->file ??= new Base64EncodedFile($this->screenshot->getBase64(), $this->config['name'] . '.png', 'image/png');
    }

    public function toPng(): string
    {
        return base64_decode($this->screenshot->getBase64());
    }

    public function __call($method, $parameters)
    {
        return $this->getFile()->$method(...$parameters);
    }
}
