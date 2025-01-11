<?php

namespace Dev3bdulrahman\Installer;

use Illuminate\Support\ServiceProvider;
use Dev3bdulrahman\Installer\Middleware\InstallerMiddleware;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'installer');
        
        $this->publishes([
            __DIR__.'/config/installer.php' => config_path('installer.php'),
            __DIR__.'/views' => resource_path('views/vendor/installer'),
        ]);

        // Register middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('installer', InstallerMiddleware::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/installer.php', 'installer'
        );
    }
}