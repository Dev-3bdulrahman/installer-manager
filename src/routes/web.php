<?php

use Dev3bdulrahman\Installer\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'install', 'middleware' => ['web'], 'as' => 'installer.'], function () {
    Route::get('welcome', [InstallerController::class, 'welcome'])->name('welcome');
    // language
    Route::get('language/{locale}', [InstallerController::class, 'SelectLanguage'])->name('language');
    Route::get('requirements', [InstallerController::class, 'showRequirements'])->name('requirements');
    Route::get('database', [InstallerController::class, 'showDatabaseForm'])->name('database');
    Route::post('database', [InstallerController::class, 'configureDatabaseAndEnv'])->name('database.save');
    Route::get('complete', [InstallerController::class, 'complete'])->name('userdata');
    Route::post('complete', [InstallerController::class, 'insertFirstUserData'])->name('userdata.save');
    Route::get('final-step', [InstallerController::class, 'finalStep'])->name('final-step');
    // Add more routes for other steps of the installation process
});
