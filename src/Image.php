<?php

namespace NiftyCo\OgImage;

use Illuminate\Http;
use HeadlessChromium\PageUtils\PageScreenshot;

class Image extends Http\Response
{
    private Base64EncodedFile $file;

    public function __construct(private PageScreenshot $screenshot, $status = 200, $headers = [])
    {
        parent::__construct(base64_decode($screenshot->getBase64()), $status, $headers);

        $this->headers->set('content-type', 'image/png');
        $this->headers->set('cache-control', 'public, immutable, no-transform, s-maxage=31536000, max-age=31536000');
    }

    private function getFile(): Base64EncodedFile
    {
        return $this->file ??= new Base64EncodedFile($this->screenshot->getBase64(), 'og-image.png', 'image/png');
    }

    public function __call($method, $parameters)
    {
        return $this->getFile()->$method(...$parameters);
    }
}
