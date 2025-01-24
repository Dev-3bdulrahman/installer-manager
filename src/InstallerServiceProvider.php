<?php

namespace Dev3bdulrahman\Installer;

use Dev3bdulrahman\Installer\Middleware\InstallerMiddleware;
use Dev3bdulrahman\Installer\Middleware\SetLocaleMiddleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->initializeAppStatus();
        $this->handleEnvironmentSetup();
        $this->setupRoutes();
        $this->setupViews();
        $this->publishAssets();
        $this->registerMiddleware();
    }

    protected function initializeAppStatus()
    {
        $appStatusFile = config_path('appstatus.php');

        if (!File::exists($appStatusFile)) {
            $appStatusContent = "<?php\n\nreturn [\n";

            $appStatusContent .= "    'WelcomePage_checked' => false,\n";
            $appStatusContent .= "    'env_status' => false,\n";
            $appStatusContent .= "    'routes_modified' => false,\n";
            $appStatusContent .= "    'requirements_checked' => false,\n";
            $appStatusContent .= "    'database_configured' => false,\n";
            $appStatusContent .= "    'migrations_executed' => false,\n";
            $appStatusContent .= "    'first_user_created' => false,\n";
            $appStatusContent .= "];\n";

            File::put($appStatusFile, $appStatusContent);
        }
    }

    protected function handleEnvironmentSetup()
    {
        $envFile = base_path('.env');
        if (config('appstatus.env_status') === false) {
            if (!File::exists($envFile)) {
                File::copy(base_path('.env.example'), $envFile);
            }
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate');
            }

            $this->updateDefaultEnvironmentValues();
        }
        $this->updateAppStatus('env_status', 1);
    }

    protected function updateDefaultEnvironmentValues()
    {
        $defaults = [
            'DB_CONNECTION' => ['from' => 'sqlite', 'to' => 'mysql'],
            'DB_HOST' => ['from' => '# DB_HOST', 'to' => '127.0.0.1'],
            'DB_PORT' => ['from' => null, 'to' => '3306'],
            'DB_DATABASE' => ['from' => null, 'to' => ''],
            'DB_USERNAME' => ['from' => null, 'to' => ''],
            'DB_PASSWORD' => ['from' => null, 'to' => ''],
            'SESSION_DRIVER' => ['from' => 'database', 'to' => 'file'],
            'CACHE_STORE' => ['from' => 'database', 'to' => 'file'],
        ];

        foreach ($defaults as $key => $values) {
            if ($values['from'] === null || env($key) === $values['from']
                || (is_string($values['from']) && str_contains(file_get_contents(base_path('.env')), $values['from']))) {
                $this->updateEnvironmentValue($key, $values['to']);
            }
        }
    }

    protected function setupRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        if (config('appstatus.routes_modified') !== 1) {
            $this->modifyRoutesFile();
            $this->updateAppStatus('routes_modified', 1);
        }
    }

    protected function setupViews()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'installer');
    }

    protected function publishAssets()
    {
        $this->publishes([
            __DIR__.'/config/installer.php' => config_path('installer.php'),
            __DIR__.'/views' => resource_path('views/vendor/installer'),
        ]);
        $this->publishes([
            __DIR__.'/views/lang' => resource_path('lang'),
        ], 'lang');
    }

    protected function registerMiddleware()
    {
        // $this->app['router']->aliasMiddleware('language', SetLocaleMiddleware::class);
        $this->app['router']->aliasMiddleware('installer', InstallerMiddleware::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/installer.php', 'installer'
        );
    }

    protected function modifyRoutesFile()
    {
        $routesFilePath = base_path('routes/web.php');

        if (!File::exists($routesFilePath)) {
            throw new \Exception('File routes/web.php does not exist.');
        }

        $content = File::get($routesFilePath);
        $pattern = "/Route::get\('\/'.+?\);/s";
        $replacement = "Route::get('/', function () {\n    return redirect()->route('installer.welcome');\n";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
            File::put($routesFilePath, $content);
        } else {
            File::append($routesFilePath, "\n\n".$replacement);
        }
    }

    protected function updateEnvironmentValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $envContent = file_get_contents($path);

            $envContent = preg_replace("/#\s*$key=/", "$key=", $envContent);

            $envContent = str_replace(
                $key.'='.env($key),
                $key.'='.$value,
                $envContent
            );
            file_put_contents($path, $envContent);
        }
    }

    private function updateAppStatus($key, $value)
    {
        $appStatusFile = config_path('appstatus.php');
        $appStatusContent = file_get_contents($appStatusFile);
        $appStatusContent = preg_replace(
            "/'$key' => (true|false|0|1)/",
            "'$key' => $value",
            $appStatusContent
        );
        file_put_contents($appStatusFile, $appStatusContent);
    }
}
