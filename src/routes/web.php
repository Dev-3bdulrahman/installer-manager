<?php

use YourNamespace\Installer\Controllers\InstallerController;
use YourNamespace\Installer\Middleware\InstallerMiddleware;

Route::middleware([InstallerMiddleware::class])->group(function () {
    Route::group(['prefix' => 'install', 'middleware' => ['web'], 'as' => 'installer.'], function () {
        Route::get('requirements', [InstallerController::class, 'showRequirements'])->name('requirements');
        Route::get('database', [InstallerController::class, 'showDatabaseForm'])->name('database');
        Route::post('database', [InstallerController::class, 'configureDatabaseAndEnv'])->name('database.save');
    });
});