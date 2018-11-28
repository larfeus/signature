<?php

namespace Larfeus\Signature\Signer;

use Larfeus\Signature\Contracts\Signer;

class RSA extends AbstractSigner implements Signer
{
    /**
     * Public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        $publicKey = $this->publicKey ?? null;

        if (is_file($publicKey)) {
            return file_get_contents($publicKey);
        }

        return $publicKey;
    }

    /**
     * Private key
     *
     * @return string
     */
    public function getPrivateKey()
    {
        $privateKey = $this->privateKey ?? null;

        if (is_file($privateKey)) {
            return file_get_contents($privateKey);
        }

        return $privateKey;
    }

    /**
     * Algo
     *
     * @return string
     */
    public function getAlgo()
    {
        $default = 'sha256';
        $algo = $this->algo ?? $default;

        if ($algo && in_array($algo, openssl_get_md_methods(true))) {
            return $algo;
        }

        return $default;
    }

    /**
     * Base64
     * 
     * @return boolean
     */
    public function getBase64()
    {
        return $this->base64 ?? true;
    }

    /**
     * {@inheritdoc}
     */
    public function sign($data)
    {
        $data = $this->convert($data);

        $pkeyid = openssl_pkey_get_private($this->getPrivateKey());

        openssl_sign($data, $signature, $pkeyid, $this->getAlgo());

        openssl_free_key($pkeyid);

        if ($this->getBase64()) {
            $signature = base64_encode($signature);
        }

        return $signature;
    }

    /**
     * {@inheritdoc}
     */
    public function verify($signature, $data)
    {
        if ($this->getBase64()) {
            $signature = base64_decode($signature);
        }

        $data = $this->convert($data);

        $pubkeyid = openssl_pkey_get_public($this->getPublicKey());

        return openssl_verify($data, $signature, $pubkeyid, $this->getAlgo()) == 1;
    }
}
