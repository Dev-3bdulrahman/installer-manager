<?php

namespace dev3bdulrahman\Installer;

use Illuminate\Support\ServiceProvider;

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
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/installer.php', 'installer'
        );
    }
}
