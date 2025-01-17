<?php

use Dev3bdulrahman\Installer\Http\Controllers\InstallerController;
use Dev3bdulrahman\Installer\Middleware\InstallerMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([InstallerMiddleware::class])->group(function () {
    Route::group(['prefix' => 'install', 'middleware' => ['web'], 'as' => 'installer.'], function () {
        Route::get('requirements', [InstallerController::class, 'showRequirements'])->name('requirements');
        Route::get('database', [InstallerController::class, 'showDatabaseForm'])->name('database');
        Route::post('database', [InstallerController::class, 'configureDatabaseAndEnv'])->name('database.save');
    });
});
