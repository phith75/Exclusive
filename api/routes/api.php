<?php

use App\Http\Controllers\Api\v1\Admin\AuthController;
use App\Http\Controllers\Api\v1\Admin\AuthorController;
use App\Http\Controllers\Api\v1\Admin\BookController;
use App\Http\Controllers\Api\v1\Admin\CategoryController;
use App\Http\Controllers\Api\v1\Admin\DashboardController;
use App\Http\Controllers\Api\v1\Admin\LendTicketController;
use App\Http\Controllers\Api\v1\Admin\PublisherController;
use App\Http\Controllers\Api\v1\Admin\ReviewController;
use App\Http\Controllers\Api\v1\Admin\UserController;
use App\Http\Controllers\Api\v1\Client\AddressController;
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
    Route::prefix('admin')->middleware('api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });
        Route::middleware('auth:api')->group(function () {
            Route::apiResource('author', AuthorController::class);
            Route::apiResource('publisher', PublisherController::class);
            Route::apiResource('category', CategoryController::class);
            Route::apiResource('user', UserController::class);
            Route::apiResource('ticket', LendTicketController::class);
            Route::apiResource('book', BookController::class);
            Route::apiResource('review', ReviewController::class);
            Route::put('update-status-user/{id}', [UserController::class, 'updateStatus']);
            Route::put('update-role-user/{id}', [UserController::class, 'updateRole']);
            Route::put('update-status-review/{id}', [ReviewController::class, 'updateStatus']);
            Route::prefix('getTrashed')->group(function () {
                Route::get('author', [AuthorController::class, 'getTrashed']);
                Route::get('publisher', [PublisherController::class, 'getTrashed']);
                Route::get('category', [CategoryController::class, 'getTrashed']);
                Route::get('book', [BookController::class, 'getTrashed']);
                Route::get('user', [UserController::class, 'getTrashed']);
                Route::get('review', [ReviewController::class, 'getTrashed']);
            });
            Route::prefix('restore')->group(function () {
                Route::put('author', [AuthorController::class, 'restore']);
                Route::put('publisher', [PublisherController::class, 'restore']);
                Route::put('category', [CategoryController::class, 'restore']);
                Route::put('book', [BookController::class, 'restore']);
                Route::put('user', [UserController::class, 'restore']);
                Route::put('review', [ReviewController::class, 'restore']);
            });
            Route::prefix('deleteMany')->group(function () {
                Route::delete('author', [AuthorController::class, 'deleteMany']);
                Route::delete('publisher', [PublisherController::class, 'deleteMany']);
                Route::delete('category', [CategoryController::class, 'deleteMany']);
                Route::delete('book', [BookController::class, 'deleteMany']);
                Route::delete('user', [UserController::class, 'deleteMany']);
                Route::delete('review', [ReviewController::class, 'deleteMany']);
            });
            Route::prefix('getAll')->group(function () {
                Route::get('author', [AuthorController::class, 'getAll']);
                Route::get('publisher', [PublisherController::class, 'getAll']);
                Route::get('category', [CategoryController::class, 'getAll']);
            });
            Route::post('import', [UserController::class, 'import']);
            Route::put('update-ticket-detail-status/{id}', [LendTicketController::class, 'updateStatus']);
            Route::put('update-lend-ticket-status/{id}', [LendTicketController::class, 'updateTicketStatus']);
            Route::prefix('dashboard')->group(function () {
                Route::get('status', [DashboardController::class, 'getStatus']);
                Route::get('top-book-borrowed-the-most/{interval?}', [DashboardController::class, 'getTopBookborrowedtheMost']);
                Route::get('top-customers-borrowing-books/{interval?}', [DashboardController::class, 'getTopCustomersBorrowingBooks']);
                Route::get('monthly-borrowed-books', [DashboardController::class, 'getMonthlyBorrowedBooks']);
                Route::get('books-by-category/{interval?}', [DashboardController::class, 'getBooksByCategory']);
                Route::get('top-30-favorite-books/{interval?}', [DashboardController::class, 'getTop30FavoriteBooks']);
            });

            Route::get('staff-list', [UserController::class, 'staffList']);
        });
    });

    Route::get('/provinces', [AddressController::class, 'getProvinces']);
    Route::get('/districts/{provinceId}', [AddressController::class, 'getDistricts']);
    Route::get('/wards/{districtId}', [AddressController::class, 'getWards']);
    Route::get('/addresses', [AddressController::class, 'getAllAddresses']);
});
