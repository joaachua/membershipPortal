<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\MembershipController;

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

Route::group(['prefix' => 'memberships', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/store', [MembershipController::class, 'store'])->name('memberships.store');
    Route::post('/update', [MembershipController::class, 'update'])->name('memberships.update');
    Route::post('/destroy', [MembershipController::class, 'destroy'])->name('memberships.destroy');
    Route::post('/view', [MembershipController::class, 'view'])->name('memberships.view');
    Route::post('/index', [MembershipController::class, 'index'])->name('memberships.index');
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [RegisterController::class, 'login']);