<?php

namespace Larfeus\Signature;

use Larfeus\Signature\SignatureManager;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class SignatureServiceProvider extends LaravelServiceProvider
{
    /**
     * @var boolean
     */
    protected $defer = false;

    /**
     * Bootstrap.
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/config/signature.php');

        if (preg_match('/Lumen/', $this->app->version())) {
            $this->app->configure('signature');
        } else {
            if ($this->app->runningInConsole()) {
                $this->publishes([
                    $source => config_path('signature.php'),
                ]);
            }
        }

        $this->mergeConfigFrom($source, 'signature');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(SignatureManager::class, function ($app) {
            return new SignatureManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function providers()
    {
        return [
            SignatureManager::class
        ];
    }
}
