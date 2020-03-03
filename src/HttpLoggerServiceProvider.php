<?php
namespace Xaamin\HttpLogger;

use Illuminate\Support\ServiceProvider;
use Xaamin\HttpLogger\HttpLoggerManager;

class HttpLoggerServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/http-logger.php' => config_path('http-logger.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations')
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/http-logger.php', 'http-logger');

        $this->app->singleton('http-logger', function ($app) {
            return new HttpLoggerManager($app);
         });

        $this->app->singleton(HttpLoggerManager::class, function ($app) {
            return $app['http-logger'];
        });
    }

    public function provides()
    {
        return [
            'http-logger',
            HttpLoggerManager::class
        ];
    }
}