<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AmazonController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CostcoScraper;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth:web'])->group(function () {
Route::get('/', [HomeController::class, "dahsboardIndex"]);
Route::get('/amazon', [HomeController::class, "amazonIndex"]);
Route::post("/store-product", [HomeController::class, "storeProduct"])->name("product.store");
Route::get("/check-warnings", [HomeController::class, "checkWarnings"]);
Route::get("/delete-warning", [HomeController::class, "removeWarning"]);
Route::get("/warnings", [HomeController::class, "warningsIndex"]);
Route::get("/warning/delete/{id}", [HomeController::class, "removeWarningEver"]);

Route::get("/costco/get-product", [CostcoScraper::class, "fetchProduct"]);
Route::post("/costco/add-product", [CostcoScraper::class, "insertProduct"]);
Route::get('/delete-all-warnings', [HomeController::class, 'destroyAll']);
Route::get('/remove-product', [HomeController::class, 'removeProduct']);
Route::post('/delete-all-selected', [HomeController::class, 'deletSelected']);
Route::get('/testEmail', [TestController::class, 'testSend']);

Route::post("/amazon/add-product", [AmazonController::class, "insertProduct"]);

Route::get('/get-prod/{id}', function ($id) {
    return Product::find($id);
});

Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Route::get('/login',  function () {
    return view('auth.login');
})->name('login');

Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
