<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['guest', 'check.login.attempts']], function () {
    Route::post('/login', [LoginController::class, 'login'])->name('/login');
    Route::post('/register', [RegisterController::class, 'register'])->name('/register');
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
Route::post('/verifyMobile', [LoginController::class, 'verifyCode'])->name('verifyMobile')->middleware(['guest', 'check.login.attempts']);

// chek verified user route
Route::get('/auth', function (): string {
    return 'auth';
})->middleware('auth:sanctum');
