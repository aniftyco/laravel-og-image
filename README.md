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

Using the `og_image()` helper you have more control over what happens with the OG Images generated. This helper accepts a `template` and an array of data to pass to the view.

#### Returning the OG image as a response:

```php
Route::get('/{user}.png', function(User $user) {
    return og_image('user', compact('user'));
});
```

#### Saving the OG image to a disk:

```php
$image = og_image('user', compact('user'));

$image->storeAs('og-images', ['disk' => 's3']);
```

## Contributing

Thank you for considering contributing to the Attachments for Laravel package! You can read the contribution guide [here](CONTRIBUTING.md).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
