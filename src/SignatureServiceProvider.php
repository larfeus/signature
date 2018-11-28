<?php

namespace Larfeus\Signature;

use Larfeus\Signature\SignatureManager;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Larfeus\Signature\Middleware\Signature as SignatureMiddleware;

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
        $this->bootConfig();
        $this->registerMiddleware();
    }

    /**
     * Setup config.
     */
    protected function bootConfig()
    {
        $source = realpath(__DIR__.'/config/signature.php');

        if ($this->isLumen()) {
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
     * Register middlewares.
     */
    protected function registerMiddleware()
    {
        $middlewares = [
            'signature' => SignatureMiddleware::class,
        ];

        if ($this->isLumen()) {
            $this->app->routeMiddleware($middlewares);
        } else {
            $router = $this->app['router'];
            $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';
            foreach ($middlewares as $alias => $middleware) {
                $router->$method($alias, $middleware);
            }
        }
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

    /**
     * Return isLumen.
     *
     * @return boolean
     */
    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen');
    }
}
