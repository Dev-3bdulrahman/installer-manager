<?php

namespace YourNamespace\Installer\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

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
            // Update .env file first
            $this->updateEnvironmentFile([
                'DB_HOST' => $request->db_host,
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_user,
                'DB_PASSWORD' => $request->db_password,
            ]);

            // Test database connection with new credentials
            config(['database.connections.mysql.host' => $request->db_host]);
            config(['database.connections.mysql.database' => $request->db_name]);
            config(['database.connections.mysql.username' => $request->db_user]);
            config(['database.connections.mysql.password' => $request->db_password]);
            
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Create database if it doesn't exist
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$request->db_name}`");
            
            // Run migrations
            Artisan::call('migrate:fresh', ['--force' => true]);
            
            // Mark as installed
            $this->markAsInstalled();
            
            return redirect('/')->with('success', 'Installation completed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Database configuration failed: ' . $e->getMessage());
        }
    }

    private function updateEnvironmentFile($data)
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        foreach ($data as $key => $value) {
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $content
            );
        }

        file_put_contents($path, $content);
    }

    private function markAsInstalled()
    {
        file_put_contents(storage_path('installed'), 'Installation completed on ' . date('Y-m-d H:i:s'));
    }
}