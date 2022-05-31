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
    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::put('profile', [AuthController::class, 'editProfile']);
});

// Contests
Route::group(['prefix' => 'contests'], function () {
    Route::get('/', [ContestController::class, 'index']);
    Route::post('/', [ContestController::class, 'create']);
    Route::get('/{id}', [ContestController::class, 'details']);
    Route::get('/{slug}', [ContestController::class, 'getContestBySlug']);
    Route::put('/{id}', [ContestController::class, 'edit']);
    Route::put('/{id}/finished', [ContestController::class, 'finishContest']);
});

// Numbers
Route::group(['prefix' => 'numbers'], function () {
    Route::put('free', [NumberController::class, 'free']);
    Route::put('reserved', [NumberController::class, 'reserved']);
    Route::put('paid', [NumberController::class, 'paid']);
});

// Bank Accounts
Route::group(['prefix' => 'bank-accounts'], function () {
    Route::get('/', [ContestController::class, 'index']);
    Route::post('/', [ContestController::class, 'create']);
    Route::put('/{id}', [ContestController::class, 'edit']);
    Route::delete('/{id}', [ContestController::class, 'remove']);
});

// Upload
Route::group(['prefix' => 'upload'], function () {
    Route::post('image', [UploadController::class, 'uploadImage']);
});
