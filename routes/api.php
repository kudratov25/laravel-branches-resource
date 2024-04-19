<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\User\UserController;
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
});
Route::post('/register', [RegisterController::class, 'register'])->name('/register');
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
Route::post('/verifyMobile', [LoginController::class, 'verifyCode'])->name('verifyMobile')->middleware(['guest', 'check.login.attempts']);

Route::apiResource('/users', UserController::class);
Route::apiResource('/brands', BrandController::class)->middleware('auth:sanctum');
Route::apiResource('/branches', BranchController::class)->middleware('auth:sanctum');
Route::get('/distric-branches', [DistrictController::class, 'search'])->middleware('auth:sanctum');

// chek verified user route and active ones
Route::get('/auth', function (): string {
    return 'You have been authenticated and your account active';
})->middleware(['auth:sanctum', 'is_active']);
