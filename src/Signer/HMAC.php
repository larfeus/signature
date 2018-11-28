<?php

namespace Larfeus\Signature\Signer;

use Larfeus\Signature\Contracts\Signer;

class HMAC extends AbstractSigner implements Signer
{
    /**
     * Secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret ?? '';
    }

    /**
     * Algo
     *
     * @return string
     */
    public function getAlgo()
    {
        return $this->algo ?? 'sha256';
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
     * Raw
     *
     * @return string
     */
    public function getRaw()
    {
        return $this->raw ?? true;
    }

    /**
     * {@inheritdoc}
     */
    public function sign($data)
    {
        $data = $this->convert($data);
        
        $signature = hash_hmac($this->getAlgo(), $data, $this->getSecret(), $this->getRaw());

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
        $generated = $this->sign($data);

        if ($this->getBase64()) {
            $signature = base64_decode($signature);
            $generated = base64_decode($generated);
        }

        // Timing attack safe string comparison
        return hash_equals($signature, $generated);
    }
}
