<?php
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    // These routes require authentication
    Route::get('/user/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/user/logout', [AuthController::class, 'logout'])->name('logout');

    //category routes
    Route::get('/category', [CategoryController::class, 'index']);
    Route::post('/category/create', [CategoryController::class, 'store']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
    Route::get('/category/datatable', [CategoryController::class, 'dataForDataTable']);

    //subcategory routes
    Route::get('/subcategory', [SubCategoryController::class, 'index'])->name('subcategories.index');
    Route::post('/subcategory/create', [SubCategoryController::class, 'store'])->name('subcategories.store');
    Route::get('/subcategory/{id}', [SubCategoryController::class, 'show'])->name('subcategories.show');
    Route::put('/subcategory/{id}', [SubCategoryController::class, 'update'])->name('subcategories.update');
    Route::delete('/subcategory/{id}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');
});


