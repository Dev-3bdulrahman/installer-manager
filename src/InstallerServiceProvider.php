<?php

namespace Dev3bdulrahman\Installer;

use Dev3bdulrahman\Installer\Middleware\InstallerMiddleware;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Add updateEnvFile method to handle environment file updates

        if (env('DB_CONNECTION') === 'sqlite') {
            $this->updateEnvironmentValue('DB_CONNECTION', 'mysql');
        }
        if (env('SESSION_DRIVER') === 'database') {
            $this->updateEnvironmentValue('SESSION_DRIVER', 'file');
        }
        if (env('CACHE_STORE') === 'database') {
            $this->updateEnvironmentValue('CACHE_STORE', 'file');
        }
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

    /**
     * Update .env file with new values.
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    protected function updateEnvironmentValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key.'='.env($key),
                $key.'='.$value,
                file_get_contents($path)
            ));
        }
    }
}
