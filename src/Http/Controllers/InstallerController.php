<?php

namespace Dev3bdulrahman\Installer\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class InstallerController extends Controller
{
    public function showRequirements()
    {
        $requirements = [
            'PHP Version (>= 8.0)' => version_compare(PHP_VERSION, '8.0.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'MySQL Extension' => extension_loaded('pdo_mysql'),
            'JSON Extension' => extension_loaded('json'),
            'OpenSSL Extension' => extension_loaded('openssl'),
        ];

        return view('installer::requirements', compact('requirements'));
    }

    public function showDatabaseForm()
    {
        return view('installer::database-form');
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
                $request->db_password
            );
            // Update .env file
            $this->updateEnvironmentFile([
                'DB_HOST' => '127.0.0.1',
                'DB_PORT' => '3306',
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_user,
                'DB_PASSWORD' => $request->db_password,
            ]);
            // Log::info('Database configuration successful'.$request->db_host.$request->db_name.$request->db_user.$request->db_password);
            // Create database if it doesn't exist
            // $query = 'CREATE DATABASE IF NOT EXISTS '.$request->db_name;
            // mysqli_query($connection, $query);

            // Run migrations
            Artisan::call('migrate:fresh', ['--force' => true]);

            // return redirect()->route('installer.complete')
            //                ->with('success', 'Database configured successfully');
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
}
