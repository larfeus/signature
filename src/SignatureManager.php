<?php

namespace Larfeus\Signature;

use Closure;
use InvalidArgumentException;
use Larfeus\Signature\Signer\RSA;
use Larfeus\Signature\Signer\HMAC;
use Larfeus\Signature\Contracts\Signer;

class SignatureManager
{
    /**
     * The application instance.
     *
     * @var @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved signers.
     *
     * @var array
     */
    protected $signers = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Constructor.
     *
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Resolve a signer instance.
     *
     * @param string $name
     *
     * @return \Larfeus\Signature\Constracts\Signer
     */
    public function signer($name = null)
    {
        $name = $name ? : $this->getDefaultSigner();

        if (! isset($this->signers[$name])) {
            $this->signers[$name] = $this->resolve($name);
        }

        return $this->signers[$name];
    }

    /**
     * Get the name of the default signer.
     *
     * @return string
     */
    public function getDefaultSigner()
    {
        return $this->app['config']['signature.default'];
    }

    /**
     * Resolve a signer.
     *
     * @param string $name
     * @return \Larfeus\Signature\Constracts\Signer
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Signer [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config['options']);
        }

        throw new InvalidArgumentException("Signer [{$name}] is not defined.");
    }

    /**
     * Create custom driver
     * 
     * @param array $config 
     * @return \Larfeus\Signature\Constracts\Signer
     */
    public function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config['options']);
    }

    /**
     * Create hmac signer.
     *
     * @param array $config
     * @return \Larfeus\Signature\Signer\HMAC
     */
    public function createHMACDriver(array $config)
    {
        return new HMAC($config);
    }

    /**
     * create rsa signer.
     *
     * @param array $config
     * @return \Larfeus\Signature\Signer\RSA
     */
    public function createRSADriver(array $config)
    {
        return new RSA($config);
    }

    /**
     * Define custom driver constructor
     * 
     * @param string $driver 
     * @param Closure $callback 
     * @return type
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get the signer configuration.
     *
     * @param string $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["signature.{$name}"];
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->signer()->$method(...$parameters);
    }
}
