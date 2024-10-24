# Open Graph Image Generator for Laravel

> [!WARNING]
> This package is not ready for general consumption

## Requirements

Requires PHP 8.1+ and a Chrome/Chromium 65+ executable.

## Installation

You can install the package via Composer:

```sh
composer require aniftyco/laravel-og-image:dev-master
```

## Usage

There are multiple ways you can utilize this.

### `GET /og-image` Route

This package registers a `GET` route to `/og-image` which accepts a varadic amount of parameters where only `template` is required and special. This tells the image generator what template to use relavent to `resources/views/og-image/`.

Take this example:

```
https://example.com/og-image?template=post&title=You%20Can%20Just%20Do%20Things
```

It is telling the generator to use the `post` template and pass the `$title` property set to `You Can Just Do Things`.

### `og_image()` Helper

You can also load up your own route and do anything you want and just return `og_image()` and generate an image on the fly.

```php
Route::get('/{user}.png', function(User $user) {
    return og_image('user', compact('user'));
});
```

## Contributing

Thank you for considering contributing to the Attachments for Laravel package! You can read the contribution guide [here](CONTRIBUTING.md).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
