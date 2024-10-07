<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\AuthController;

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

Route::middleware(['auth:sanctum', 'check.auth'])->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users', [AuthController::class, 'index']);

    Route::resource('books', BookController::class);

    Route::post('books/{id}/borrow', [BookController::class, 'borrow']);
    Route::post('books/{id}/return', [BookController::class, 'returnBook']);
    Route::get('books/search', [BookController::class, 'search']);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});






