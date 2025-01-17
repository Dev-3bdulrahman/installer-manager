<?php

namespace Dev3bdulrahman\Installer\Middleware;

use Illuminate\Http\Request;

class InstallerMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        // Check environment settings
        if (env('SESSION_DRIVER') === 'database') {
            $this->updateEnvFile('SESSION_DRIVER', 'file');
        }

        if (env('DB_CONNECTION') === 'sqlite') {
            $this->updateEnvFile('DB_CONNECTION', 'mysql');
        }

        // Check if application is already installed
        if ($this->isInstalled() && !$this->isInstallerRoute($request)) {
            return $next($request);
        }

        // If not installed and not in installer route, redirect to installer
        if (!$this->isInstalled() && !$this->isInstallerRoute($request)) {
            return redirect()->route('installer.requirements');
        }

        // If trying to access installer after installation
        if ($this->isInstalled() && $this->isInstallerRoute($request)) {
            return redirect('/');
        }

        return $next($request);
    }

    private function isInstalled()
    {
        return file_exists(storage_path('installed'));
    }

    private function isInstallerRoute(Request $request)
    {
        return str_starts_with($request->path(), 'install');
    }

    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            file_put_contents($path, $content);
        }
    }
}
