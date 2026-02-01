<?php

use App\Http\Controllers\Settings\AvatarController;
use App\Http\Controllers\Settings\CountryController;
use App\Http\Controllers\Settings\DateTimeController;
use App\Http\Controllers\Settings\LanguageController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SessionsController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('settings/avatar', [AvatarController::class, 'store'])->name('avatar.store');
    Route::delete('settings/avatar', [AvatarController::class, 'destroy'])->name('avatar.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('settings/language-and-region', [LanguageController::class, 'edit'])
        ->name('language-and-region.edit');

    Route::patch('settings/country', [CountryController::class, 'update'])
        ->name('country.update');

    Route::get('settings/date-time', [DateTimeController::class, 'edit'])
        ->name('date-time.edit');

    Route::patch('settings/date-time', [DateTimeController::class, 'update'])
        ->name('date-time.update');

    Route::delete('settings/sessions/{session}', [SessionsController::class, 'destroy'])
        ->name('sessions.destroy');

    Route::delete('settings/sessions', [SessionsController::class, 'destroyOthers'])
        ->name('sessions.destroy-others');
});
