<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|e
*/

Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/get-profile', [UserController::class, 'loggedInView'])->name('user.get-profile');
    Route::post('/update-profile', [UserController::class, 'loggedInUpdate'])->name('user.update-profile');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.change-password');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/destroy', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/view', [UserController::class, 'view'])->name('users.view');
    Route::post('/index', [UserController::class, 'index'])->name('users.index');
});

Route::group(['prefix' => 'memberships', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/store', [MembershipController::class, 'store'])->name('memberships.store');
    Route::post('/update', [MembershipController::class, 'update'])->name('memberships.update');
    Route::post('/destroy', [MembershipController::class, 'destroy'])->name('memberships.destroy');
    Route::post('/view', [MembershipController::class, 'view'])->name('memberships.view');
    Route::post('/index', [MembershipController::class, 'index'])->name('memberships.index');
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [RegisterController::class, 'login']);