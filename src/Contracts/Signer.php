<?php

namespace Larfeus\Signature\Contracts;

interface Signer
{
	/**
	 * Generate signature
	 * 
	 * @param mixed $data 
	 * @return string
	 */
    public function sign($data);

    /**
     * Verification with specified signature
     * 
     * @param string $signature 
     * @param mixed $data 
     * @return boolean
     */
    public function verify($signature, $data);
}
