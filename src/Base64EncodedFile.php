<?php

namespace NiftyCo\OgImage;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mime\MimeTypes;
use Illuminate\Http\UploadedFile;

/**
 * derived from https://github.com/hshn/base64-encoded-file/blob/master/src/HttpFoundation/File/Base64EncodedFile.php
 */
class Base64EncodedFile extends UploadedFile
{
  /**
   * @param string $encoded
   * @param bool $strict
   * @param bool $checkPath
   */
  public function __construct(string $encoded, string $originalName, ?string $mimeType = null, ?int $error = null, bool $test = false)
  {
    parent::__construct($this->restoreToTemporary($encoded, true), $originalName, $mimeType, $error, $test);
  }

  /**
   * @param string $encoded
   * @param bool $strict
   *
   * @return string
   * @throws FileException
   */
  private function restoreToTemporary(string $encoded, $strict = true): string
  {
    if (strpos($encoded, 'data:') === 0) {
      if (strpos($encoded, 'data://') !== 0) {
        $encoded = substr_replace($encoded, 'data://', 0, 5);
      }

      $source = @fopen($encoded, 'rb');
      if ($source === false) {
        throw new FileException('Unable to decode strings as base64');
      }

      $meta = stream_get_meta_data($source);

      if ($strict) {
        if (!isset($meta['base64']) || $meta['base64'] !== true) {
          throw new FileException('Unable to decode strings as base64');
        }
      }

      if (false === $path = tempnam($directory = sys_get_temp_dir(), 'Base64EncodedFile')) {
        throw new FileException(sprintf('Unable to create a file into the "%s" directory', $path));
      }

      if (null !== $extension = (MimeTypes::getDefault()->getExtensions($meta['mediatype'])[0] ?? null)) {
        $path .= '.' . $extension;
      }

      if (false === $target = @fopen($path, 'w+b')) {
        throw new FileException(sprintf('Unable to write the file "%s"', $path));
      }

      if (false === @stream_copy_to_stream($source, $target)) {
        throw new FileException(sprintf('Unable to write the file "%s"', $path));
      }

      if (false === @fclose($target)) {
        throw new FileException(sprintf('Unable to write the file "%s"', $path));
      }

      if (false === @fclose($source)) {
        throw new FileException(sprintf('Unable to close data stream'));
      }

      return $path;
    }

    if (false === $decoded = base64_decode($encoded, $strict)) {
      throw new FileException('Unable to decode strings as base64');
    }

    if (false === $path = tempnam($directory = sys_get_temp_dir(), 'Base64EncodedFile')) {
      throw new FileException(sprintf('Unable to create a file into the "%s" directory', $directory));
    }

    if (false === file_put_contents($path, $decoded)) {
      throw new FileException(sprintf('Unable to write the file "%s"', $path));
    }

    return $path;
  }

  public function __destruct()
  {
    if (file_exists($this->getPathname())) {
      unlink($this->getPathname());
    }
  }
}
