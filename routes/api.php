<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NumberController;
use App\Http\Controllers\SellerController;
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
        Route::post('simple-login', 'simpleLogin');
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
            Route::middleware(['auth.token', 'auth.blocked', 'auth.seller', 'seller.approved'])->group(function () {
                Route::get('/', 'getContestsByUser');
                Route::get('/{id}', 'details');
                Route::get('/{id}/orders', 'getContestOrders');
                Route::post('/', 'create');
                Route::put('/{id}', 'edit');
                Route::put('/{id}/finish/{number}', 'finishContest');
            });
        });
        /**
         * Route: /contests
         */
        Route::get('/', 'index');
        Route::get('/{username}', 'getContestsByUsername');
        Route::get('/{username}/{slug}', 'getContestBySlug');
    });
});

// Numbers
Route::controller(NumberController::class)->group(function () {
    /**
     * Route: /numbers
     */
    Route::prefix('numbers')->group(function () {
        Route::middleware(['auth.token'])->group(function () {
            Route::get('/{contest_id}/customer/{customer_id}', 'index');
            Route::post('/{contest_id}/reserve', 'reserve');
            Route::delete('/{contest_id}/free', 'free');
            /**
             * Route: /numbers/manage
             */
            Route::middleware(['auth.blocked', 'auth.seller', 'seller.approved'])->group(function () {
                Route::prefix('manage')->group(function () {
                    Route::put('/{contest_id}/orders/{order_id}/paid', 'adminPaidOrder');
                    Route::delete('/{contest_id}/orders/{order_id}/cancel', 'adminCancelOrder');
                });
            });
        });

        Route::get('/{contest_id}/status', 'status');
        Route::post('/{contest_id}/customer-numbers', 'getCustomerNumbers');
        Route::post('/webhook', 'webhook');
    });
});

// Bank Accounts
Route::controller(BankAccountController::class)->group(function () {
    /**
     * Route: /bank-accounts
     */
    Route::prefix('bank-accounts')->group(function () {
        Route::middleware(['auth.token', 'auth.blocked', 'auth.seller', 'seller.approved'])->group(function () {
            Route::put('/mercado-pago', 'saveMPAccessToken');
            Route::get('/', 'index');
            Route::post('/', 'create');
            Route::put('/{id}', 'edit');
            Route::delete('/{id}', 'remove');
        });
    });
});

// Customers
Route::controller(CustomerController::class)->group(function () {
    /**
     * Route: /customers
     */
    Route::prefix('customers')->group(function () {
        Route::middleware(['auth.token', 'auth.blocked', 'auth.seller', 'seller.approved'])->group(function () {
            Route::get('/', 'index');
        });
    });
});

// Sellers
Route::controller(SellerController::class)->group(function () {
    /**
     * Route: /sellers
     */
    Route::prefix('sellers')->group(function () {
        Route::middleware(['auth.token', 'auth.blocked', 'auth.admin'])->group(function () {
            Route::get('/', 'index');
            Route::put('/{id}/approve', 'approveSeller');
            Route::patch('/{id}/toggle', 'toggleSeller');
        });
    });
});

// Upload
Route::controller(UploadController::class)->group(function () {
    /**
     * Route: /upload
     */
    Route::prefix('upload')->group(function () {
        Route::middleware(['auth.token', 'auth.blocked', 'auth.seller'])->group(function () {
            Route::post('image', 'uploadImage');
        });
    });
});
