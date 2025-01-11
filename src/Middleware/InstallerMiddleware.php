<?php

namespace dev3bdulrahman\Installer\Middleware;

use Closure;
use Illuminate\Http\Request;

class InstallerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
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
}