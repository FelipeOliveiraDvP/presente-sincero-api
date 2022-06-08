<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\NumberController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth
Route::controller(AuthController::class)->group(function () {
    /**
     * Route: /auth
     */
    Route::prefix('auth')->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('forgot', 'forgot');
        Route::get('verify/{code}', 'verify');
        Route::put('reset', 'reset');

        Route::middleware(['auth.token'])->group(function () {
            Route::get('profile', 'getProfile');
            Route::put('profile', 'editProfile');
        });
    });
});

// Contests
Route::controller(ContestController::class)->group(function () {
    /**
     * Route: /contests
     */
    Route::prefix('contests')->group(function () {
        /**
         * Route: /contests/manage
         */
        Route::prefix('manage')->group(function () {
            Route::middleware(['auth.token', 'auth.admin'])->group(function () {
                Route::get('/', 'getContestsByUser');
                Route::get('/{id}', 'details');
                Route::post('/', 'create');
                Route::put('/{id}', 'edit');
                Route::put('/{id}/finished', 'finishContest');
            });
        });

        Route::get('/', 'index');
        Route::get('/{slug}', 'getContestBySlug');
    });
});

// Numbers
Route::controller(NumberController::class)->group(function () {
    /**
     * Route: /numbers
     */
    Route::prefix('numbers')->group(function () {
        Route::middleware(['auth.token'])->group(function () {
            Route::get('/{contest_id}', 'index');
            Route::post('/{contest_id}/free', 'free');
            Route::post('/{contest_id}/reserve', 'reserved');
        });

        Route::post('/{contest_id}/paid', 'paid');
    });
});

// Bank Accounts
Route::controller(BankAccountController::class)->group(function () {
    /**
     * Route: /bank-accounts
     */
    Route::prefix('bank-accounts')->group(function () {
        Route::middleware(['auth.token', 'auth.admin'])->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'create');
            Route::put('/{id}', 'edit');
            Route::delete('/{id}', 'remove');
        });
    });
});

// Upload
Route::controller(UploadController::class)->group(function () {
    /**
     * Route: /upload
     */
    Route::prefix('upload')->group(function () {
        Route::middleware(['auth.token', 'auth.admin'])->group(function () {
            Route::post('image', 'uploadImage');
        });
    });
});
