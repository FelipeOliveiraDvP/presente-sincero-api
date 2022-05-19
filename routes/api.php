<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContestController;
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
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Contests
Route::group(['prefix' => 'contests'], function ($routes) {
    Route::get('/', [ContestController::class, 'index']);
    Route::post('/', [ContestController::class, 'create']);
});

// Upload
Route::post('upload', [UploadController::class, 'index']);
