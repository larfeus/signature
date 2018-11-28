<?php

namespace Larfeus\Signature\Signer;

abstract class AbstractSigner
{
    /**
     * Constructor.
     * 
     * @param array $config 
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Config setters
     * 
     * @param string $method 
     * @param array $arguments 
     */
    public function __call($method, $arguments)
    {
        if (preg_match('/^set(.*)$/', $method, $matches)) {

            $getMethod = 'get' . $matches[1];

            if (method_exists($this, $getMethod)) {
                $this->{ lcfirst($matches[1]) } = array_pop($arguments);
            }
        }
    }

    /**
     * Set configuration.
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $method = 'get' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * Sorting
     * 
     * @return boolean
     */
    public function getNormalize()
    {
        return $this->normalize ?? false;
    }

    /**
     * Json
     * 
     * @return boolean
     */
    public function getJson()
    {
        return $this->json ?? JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    }

    /**
     * Convert signature data
     *
     * @param mixed $data
     * @return string
     */
    public function convert($data)
    {
        if (is_scalar($data)) {
            return $data;
        }
        
        if ($this->getNormalize()) {
            $data = $this->normalize($data);
        }

        return json_encode($data, $this->getJson());
    }

    /**
     * Normalization
     * 
     * @param mixed $data 
     * @return mixed
     */
    public function normalize($data)
    {
        $deepSort = function (&$data) use (&$deepSort) {
            if (is_array($data)) {
                $data = array_filter($data);
                ksort($data);
                array_walk($data, $deepSort);
            }
            return $data;
        };

        return $deepSort($data);
    }
}
