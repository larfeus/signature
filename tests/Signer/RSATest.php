<?php

use Larfeus\Signature\Signer\RSA;

class RSATest extends PHPUnit_Framework_TestCase
{
    protected $publicKey;

    protected $privateKey;

    public function testSetConfig()
    {
        $config = [
            'publicKey' => 'public.key',
            'privateKey' => 'private.key',
            'algo' => 'sha1',
        ];

        $rsa = new RSA($config);
        $this->assertEquals($rsa->getAlgo(), 'sha1');
        $this->assertEquals($rsa->getPublicKey(), 'public.key');
        $this->assertEquals($rsa->getPrivateKey(), 'private.key');

        // default
        $rsa = new RSA();
        $this->assertEquals($rsa->getAlgo(), 'sha256');

        // setters
        $rsa->setAlgo('md5');
        $this->assertEquals($rsa->getAlgo(), 'md5');
    }

    public function testSign()
    {
        $this->generatePems();
        $config = [
            'privateKey' => $this->privateKey,
            'algo' => 'sha256',
            'normalize' => true,
            'base64' => true,
        ];

        $rsa = new RSA($config);

        $data = 'foobar';
        openssl_sign($data, $signature, $this->privateKey, 'sha256');
        $target = base64_encode($signature);
        $this->assertEquals($target, $rsa->sign($data));

        // array
        $data = [
            'b' => 'b',
            'c' => [
                'd' => 'd',
                'e' => 1,
            ],
            'a' => 'a',
        ];
        $dataString = json_encode([
            'a' => 'a',
            'b' => 'b',
            'c' => [
                'd' => 'd',
                'e' => 1,
            ],
        ]);
        openssl_sign($dataString, $signature, $this->privateKey, 'sha256');
        $target = base64_encode($signature);
        $this->assertEquals($target, $rsa->sign($data));
    }

    public function testVerify()
    {
        $this->generatePems();

        $config = [
            'publicKey' => $this->publicKey,
            'algo' => 'sha256',
            'normalize' => false,
            'base64' => true,
        ];

        $rsa = new RSA($config);

        // string
        $data = 'foobar';
        openssl_sign($data, $signature, $this->privateKey, 'sha256');
        $target = base64_encode($signature);
        $this->assertTrue($rsa->verify($target, $data));

        $this->assertFalse($rsa->verify($target, 'fooba'));

        // array
        $data = [
            'b' => 'b',
            'c' => [
                'd' => 'd',
                'e' => 1,
            ],
            'a' => 'a',
        ];
        $dataString = json_encode($data);
        openssl_sign($dataString, $signature, $this->privateKey, 'sha256');
        $target = base64_encode($signature);

        $this->assertTrue($rsa->verify($target, $data));
    }

    protected function generatePems()
    {
        $config = array();
        $config['config'] = dirname(__FILE__) . '/openssl.cnf';

        $pkey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ] + $config);
        openssl_pkey_export($pkey, $private_key_pem, null, $config);

        $details = openssl_pkey_get_details($pkey);

        $this->publicKey = $details['key'];
        $this->privateKey = $private_key_pem;
    }
}
