<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CostcoScraper;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, "dahsboardIndex"]);
Route::post("/store-product", [HomeController::class, "storeProduct"])->name("product.store");
Route::get("/check-warnings", [HomeController::class, "checkWarnings"]);
Route::get("/delete-warning", [HomeController::class, "removeWarning"]);

Route::get("/costco/get-product", [CostcoScraper::class, "fetchProduct"]);
Route::post("/costco/add-product", [CostcoScraper::class, "insertProduct"]);
Route::get('/delete-all-warnings', [HomeController::class, 'destroyAll']);
Route::get('/remove-product', [HomeController::class, 'removeProduct']);
Route::post('/delete-all-selected', [HomeController::class, 'deletSelected']);
