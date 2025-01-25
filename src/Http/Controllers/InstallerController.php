<?php

namespace Dev3bdulrahman\Installer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class InstallerController extends Controller
{
    public function welcome()
    {
        if (!$this->checkAppStatus('WelcomePage_checked')) {
            $this->updateAppStatus('WelcomePage_checked', true);

            return view('installer::welcome');
        }

        return redirect()->route('installer.requirements');
    }

    public function SelectLanguage($locale)
    {
        if (!file_exists(resource_path('lang'))) {
            Artisan::call('vendor:publish --tag=lang');
        }
        $this->updateEnvironmentValue('APP_LOCALE', $locale);

        return redirect()->back()->withSession('locale', $locale);
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

    public function showRequirements()
    {
        $requirements = [
            'PHP Version (>= 8.0)' => version_compare(PHP_VERSION, '8.0.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'MySQL Extension' => extension_loaded('pdo_mysql'),
            'JSON Extension' => extension_loaded('json'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'XML Extension' => extension_loaded('xml'),
            'Ctype Extension' => extension_loaded('ctype'),
            'BCMath Extension' => extension_loaded('bcmath'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'Curl Extension' => extension_loaded('curl'),
            'Zip Extension' => extension_loaded('zip'),
            'GD Extension' => extension_loaded('gd'),
        ];

        if (!$this->checkAppStatus('requirements_checked')) {
            $this->updateAppStatus('requirements_checked', true);

            return view('installer::requirements', compact('requirements'));
        }

        return redirect()->route('installer.database');
    }

    public function showDatabaseForm()
    {
        if (!$this->checkAppStatus('database_configured')) {
            return view('installer::database-form');
        }

        return redirect()->route('installer.userdata');
    }

    public function configureDatabaseAndEnv(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_password' => 'required',
        ]);
        try {
            // Test database connection
            $connection = mysqli_connect(
                $request->db_host,
                $request->db_user,
                $request->db_password,
                ''
            );

            if (!$connection) {
                throw new \Exception('Could not connect to MySQL server');
            }
            $this->updateEnvironmentFile([
                'DB_HOST' => $request->db_host,
                'DB_PORT' => '3306',
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_user,
                'DB_PASSWORD' => $request->db_password,
            ]);
            $query = 'CREATE DATABASE IF NOT EXISTS `'.mysqli_real_escape_string($connection, $request->db_name).'`';
            if (!mysqli_query($connection, $query)) {
                throw new \Exception('Could not create database');
            }
            mysqli_close($connection);
            $this->updateAppStatus('database_configured', true);

            return redirect()->route('installer.userdata');
        } catch (\Exception $e) {
            return back()->with('error', 'Database configuration failed: '.$e->getMessage());
        }
    }

    public function complate()
    {
        if (!$this->checkAppStatus('first_user_created')) {
            if (!$this->checkAppStatus('migrations_executed')) {
                Artisan::call('migrate');
                $this->updateAppStatus('migrations_executed', true);
            } else {
                Artisan::call('migrate:fresh');
                $this->updateAppStatus('migrations_executed', true);
            }

            return view('installer::user-form');
        }
    }

    public function insertFirstUserData(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => hash('sha256', $request->password),
            ]);

            $this->updateAppStatus('first_user_created', true);
        } catch (\Exception $e) {
            return back()->with('error', 'Database configuration failed: '.$e->getMessage());
        }
    }

    private function updateEnvironmentFile($data)
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        foreach ($data as $key => $value) {
            // Remove comment if key exists with comment
            $content = preg_replace(
                "/^#\s*{$key}=.*/m",
                "{$key}={$value}",
                $content
            );

            // Update or add the key-value pair
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $content
            );
        }

        file_put_contents($path, $content);
    }

    private function updateAppStatus($key, $value)
    {
        $appStatusFile = config_path('appstatus.php');
        $appStatusContent = file_get_contents($appStatusFile);

        $appStatusContent = preg_replace(
            "/'$key' => (true|false)/",
            "'$key' => $value",
            $appStatusContent
        );

        file_put_contents($appStatusFile, $appStatusContent);
    }

    private function checkAppStatus($key)
    {
        $appStatusFile = config_path('appstatus.php');
        $appStatus = include $appStatusFile;

        return isset($appStatus[$key]) ? $appStatus[$key] : false;
    }
}
