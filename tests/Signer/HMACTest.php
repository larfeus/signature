<?php

use Larfeus\Signature\Signer\HMAC;

class HMACTest extends PHPUnit_Framework_TestCase
{
    public function testSetConfig()
    {
        $config = [
            'algo' => 'sha1',
            'secret' => '123456',
        ];
        $hmac = new HMAC($config);
        $this->assertEquals($hmac->getSecret(), 123456);
        $this->assertEquals($hmac->getAlgo(), 'sha1');

        // default
        $hmac = new HMAC();
        $this->assertEquals($hmac->getAlgo(), 'sha256');

        // setters
        $hmac->setSecret('test');
        $this->assertEquals($hmac->getSecret(), 'test');
    }

    public function testSign()
    {
        $config = [
            'algo' => 'sha256',
            'secret' => '123456',
            'normalize' => true,
            'base64' => true,
            'raw' => true,
        ];
        $hmac = new HMAC($config);

        // string
        $data = 'foobar';
        $target = base64_encode(hash_hmac('sha256', $data, '123456', true));
        $this->assertEquals($hmac->sign($data), $target);

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
        $target = base64_encode(hash_hmac('sha256', $dataString, '123456', true));
        $this->assertEquals($hmac->sign($data), $target);
    }

    public function testVerify()
    {
        $config = [
            'algo' => 'sha256',
            'secret' => '123456',
            'normalize' => false,
            'base64' => false,
            'raw' => false,
        ];
        $hmac = new HMAC($config);

        // string
        $data = 'foobar';
        $target = hash_hmac('sha256', $data, '123456', false);
        $ret = $hmac->verify($target, $data);
        $this->assertTrue($ret);

        $data = 'fooba';
        $target = hash_hmac('sha256', 'foobar', '123456', false);
        $ret = $hmac->verify($target, $data);
        $this->assertFalse($ret);

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
        $target = hash_hmac('sha256', $dataString, '123456', false);
        $ret = $hmac->verify($target, $data);
        $this->assertTrue($ret);
    }
}
