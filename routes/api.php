<?php

use App\Http\Controllers\AuthController;
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
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot', [AuthController::class, 'forgot']);
    Route::get('verify/{code}', [AuthController::class, 'verify']);
    Route::put('reset', [AuthController::class, 'reset']);
    Route::get('profile', [AuthController::class, 'getProfile'])->middleware(['auth.token']);
    Route::put('profile', [AuthController::class, 'editProfile'])->middleware(['auth.token']);
});

// Contests
Route::group(['prefix' => 'contests'], function () {
    Route::get('/', [ContestController::class, 'index']);
    Route::post('/', [ContestController::class, 'create'])->middleware(['auth.token', 'auth.admin']);
    Route::get('/{id}', [ContestController::class, 'details']);
    Route::get('/{slug}', [ContestController::class, 'getContestBySlug']);
    Route::put('/{id}', [ContestController::class, 'edit'])->middleware(['auth.token', 'auth.admin']);
    Route::put('/{id}/finished', [ContestController::class, 'finishContest'])->middleware(['auth.token', 'auth.admin']);
});

// Numbers
Route::group(['prefix' => 'numbers'], function () {
    Route::put('free', [NumberController::class, 'free'])->middleware(['auth.token']);
    Route::put('reserved', [NumberController::class, 'reserved'])->middleware(['auth.token']);
    Route::put('paid', [NumberController::class, 'paid'])->middleware(['auth.token']);
});

// Bank Accounts
Route::group(['prefix' => 'bank-accounts'], function () {
    Route::get('/', [ContestController::class, 'index'])->middleware(['auth.token', 'auth.admin']);
    Route::post('/', [ContestController::class, 'create'])->middleware(['auth.token', 'auth.admin']);
    Route::put('/{id}', [ContestController::class, 'edit'])->middleware(['auth.token', 'auth.admin']);
    Route::delete('/{id}', [ContestController::class, 'remove'])->middleware(['auth.token', 'auth.admin']);
});

// Upload
Route::group(['prefix' => 'upload'], function () {
    Route::post('image', [UploadController::class, 'uploadImage'])->middleware(['auth.token', 'auth.admin']);
});
