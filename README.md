# Laravel signature

Use HMAC or RSA to sign data for Laravel and lumen;

[![Latest Stable Version](https://poser.pugx.org/larfeus/signature/version)](https://packagist.org/packages/larfeus/signature)
[![Total Downloads](https://poser.pugx.org/larfeus/signature/downloads)](https://packagist.org/packages/larfeus/signature)
[![StyleCI](https://styleci.io/repos/76261016/shield)](https://styleci.io/repos/76261016096)

## Install

```shell
composer require larfeus/signature
```

#### laravel

```shell
php artisan vendor:publish
```

#### lumen

```shell
copy vendor/larfeus/signature/src/config/signature.php config/signature.php
```
```php
// bootstrap/app.php
$app->withFacades(true, [
    Larfeus\Signature\Facade\Signature::class => 'Signature'
]);

$app->register(Larfeus\Signature\SignatureServiceProvider::class);
```

## Configuration

```php
// config/signature.php
return [
	'api' => [
		'driver' => 'HMAC',
		'options' => [
		    // hash algorithm
			'algo' => env('SIGNATURE_API_ALGO', 'sha1'),
			// hash secret key
			'secret' => env('SIGNATURE_API_SECRET'),
			// base64 encoding generating signature
			'base64' => true,
			// sort and filter array data (only for arrays)
			'normalize' => false,
			// raw_output parameter for hash_hmac function calling
			'raw' => true,
		],
	],
];
```

## Usage

Make signature:

```php
// using default signer (see configuration file)
$signature = Signature::sign('foobar');
// using specified signer
$signature = Signature::signer('hmac')
    ->sign(['foo'=>'bar']);

$signature = Signature::signer('rsa')
    ->setPrivateKey('./private.pem')
    ->sign(['foo'=>'bar']);
```

Manual verification:

```php
Signature::verify($signature, 'foobar');

Signature::signer('hmac')
    ->verify($signature, ['foo'=>'bar']);

Signature::signer('rsa')
    ->setPublicKey('./public.pem')
    ->verify($signature, ['foo'=>'bar']);
```

Middleware in laravel or lumen application:
```php
Route::group(['prefix' => 'api', 'middleware' => 'signature:api'], function () {
	Route::get('/user/:id', function($id) {
		return User::findOrFail($id);
	});
});
```

## License

[MIT LICENSE](https://github.com/larfeus/signature/blob/master/LICENSE)
