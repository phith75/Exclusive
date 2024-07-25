<?php

use App\Http\Controllers\Api\v1\Client\AddressController;
use App\Http\Controllers\Api\v1\Client\AuthController;
use App\Http\Controllers\Api\v1\Client\AuthorController;
use App\Http\Controllers\Api\v1\Client\BookController;
use App\Http\Controllers\Api\v1\Client\CategoryController;
use App\Http\Controllers\Api\v1\Client\LendTicketController;
use App\Http\Controllers\Api\v1\Client\PublisherController;
use App\Http\Controllers\Api\v1\Client\ReviewController;
use App\Http\Controllers\Api\v1\Client\WishlistController;
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

Route::prefix('v1')->group(function () {
    Route::prefix('client')->middleware('api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('register', [AuthController::class, 'register']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
            Route::put('update-profile', [AuthController::class, 'updateProfile']);
            Route::put('change-password', [AuthController::class, 'changePassword']);
            Route::get('google', [AuthController::class, 'redirectToGoogle']);
            Route::get('google/callback', [AuthController::class, 'handleGoogleCallback']);
        });
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::put('reset-password', [AuthController::class, 'resetPassword']);
        Route::prefix('author')->group(function () {
            Route::get('/', [AuthorController::class, 'index']);
            Route::get('{id}', [AuthorController::class, 'show']);
        });
        Route::prefix('book')->group(function () {
            Route::get('/', [BookController::class, 'index']);
            Route::get('{id}', [BookController::class, 'show']);
        });
        Route::get('most-borrowed/book', [BookController::class, 'getMostBorrowed']);

        Route::prefix('publisher')->group(function () {
            Route::get('/', [PublisherController::class, 'index']);
            Route::get('{id}', [PublisherController::class, 'show']);
        });
        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::get('{id}', [CategoryController::class, 'show']);
        });

        Route::middleware('auth:api')->group(function () {
            Route::apiResource('wishlist', WishlistController::class)->except('edit');
            Route::get('wishlist-user', [WishlistController::class, 'getAll']);
            Route::get('suggestion', [WishlistController::class, 'getSuggestion']);
            Route::put('update-ticket-detail-status/{id}', [LendTicketController::class, 'updateStatus']);
            Route::put('update-lend-ticket-stats/{id}', [LendTicketController::class, 'updateTicketStatus']);
            Route::prefix('ticket')->group(function () {
                Route::get('/', [LendTicketController::class, 'findByUser']);
                Route::post('/', [LendTicketController::class, 'store']);
            });
            Route::post('review', [ReviewController::class, 'store']);
        });
        Route::apiResource('review', ReviewController::class)->except('edit', 'store');

        Route::prefix('getAll')->group(function () {
            Route::get('authors', [AuthorController::class, 'getAll']);
            Route::get('publishers', [PublisherController::class, 'getAll']);
            Route::get('categories', [CategoryController::class, 'getAll']);
        });
        Route::get('provinces', [AddressController::class, 'getProvinces']);
        Route::get('districts/{provinceId}', [AddressController::class, 'getDistricts']);
        Route::get('wards/{districtId}', [AddressController::class, 'getWards']);
        Route::get('addresses', [AddressController::class, 'getAllAddresses']);
    });
});
