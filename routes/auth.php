<?php

namespace Routes;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'store'])
        ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::name('password.')->group(function () {
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('email');

        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('reset');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->name('update');
    });
});

Route::middleware('auth')->group(function () {
    Route::name('verification.')->group(function () {
        Route::get('/verify-email', EmailVerificationPromptController::class)
            ->name('notice');
    
        Route::middleware('throttle:6,1')->group(function () {
            Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
                    ->middleware(['signed'])
                    ->name('verify');
        
            Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->name('send');
        });
    });

    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
