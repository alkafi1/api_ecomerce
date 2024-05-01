<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    // These routes require authentication
    Route::get('/user/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/user/logout', [AuthController::class, 'logout'])->name('logout');

    //category route
    Route::prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::post('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::get('/datatable', [CategoryController::class, 'dataForDataTable']);
    });

    //subcategoried
    Route::prefix('subcategory')->group(function () {
        Route::get('/', [SubCategoryController::class, 'index'])->name('subcategories.index');
        Route::post('/', [SubCategoryController::class, 'store'])->name('subcategories.store');
        Route::get('/{id}', [SubCategoryController::class, 'show'])->name('subcategories.show');
        Route::post('/{id}', [SubcategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('/{id}', [SubcategoryController::class, 'destroy'])->name('subcategories.destroy');
    });
});
