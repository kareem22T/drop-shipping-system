<?php

use App\Http\Controllers\AmazonController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CostcoScraper;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;

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

