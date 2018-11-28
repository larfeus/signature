<?php

return [
	// default signer
	'default' => env('SIGNATURE_DRIVER', 'hmac'),
	'hmac' => [
		'driver' => 'HMAC',
		'options' => [
			'algo' => env('SIGNATURE_HMAC_ALGO', 'sha1'),
			'secret' => env('SIGNATURE_HMAC_SECRET'),
		],
	],
	'rsa' => [
		'driver' => 'RSA',
		'options' => [
			'algo' => env('SIGNATURE_RSA_ALGO', 'sha1'),
			// default primary key (if file should be absolute address)
			'publicKey' => env('SIGNATURE_RSA_PUBLIC_KEY'),
			// default primary key (if file should be absolute address)
			'privateKey' => env('SIGNATURE_RSA_PRIVATE_KEY'),
		],
	],
];