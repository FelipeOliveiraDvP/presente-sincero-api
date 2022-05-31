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
    Route::get('profile', [AuthController::class, 'getProfile'])->middleware(['has_token']);
    Route::put('profile', [AuthController::class, 'editProfile'])->middleware(['has_token']);
});

// Contests
Route::group(['prefix' => 'contests'], function () {
    Route::get('/', [ContestController::class, 'index']);
    Route::post('/', [ContestController::class, 'create'])->middleware(['has_token']);
    Route::get('/{id}', [ContestController::class, 'details']);
    Route::get('/{slug}', [ContestController::class, 'getContestBySlug']);
    Route::put('/{id}', [ContestController::class, 'edit'])->middleware(['has_token']);
    Route::put('/{id}/finished', [ContestController::class, 'finishContest'])->middleware(['has_token']);
});

// Numbers
Route::group(['prefix' => 'numbers'], function () {
    Route::put('free', [NumberController::class, 'free'])->middleware(['has_token']);
    Route::put('reserved', [NumberController::class, 'reserved'])->middleware(['has_token']);
    Route::put('paid', [NumberController::class, 'paid'])->middleware(['has_token']);
});

// Bank Accounts
Route::group(['prefix' => 'bank-accounts'], function () {
    Route::get('/', [ContestController::class, 'index'])->middleware(['has_token']);
    Route::post('/', [ContestController::class, 'create'])->middleware(['has_token']);
    Route::put('/{id}', [ContestController::class, 'edit'])->middleware(['has_token']);
    Route::delete('/{id}', [ContestController::class, 'remove'])->middleware(['has_token']);
});

// Upload
Route::group(['prefix' => 'upload'], function () {
    Route::post('image', [UploadController::class, 'uploadImage'])->middleware(['has_token']);
});
