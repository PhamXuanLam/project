<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("/login", [AuthController::class, "login"]);
Route::get("/logout", [AuthController::class, "logout"])->middleware("auth:account_api");

Route::prefix("/customer")->group(function() {
    Route::post("/register",[CustomerController::class, "register"]);

    Route::get("/show", [CustomerController::class, "show"])->middleware("auth:account_api");
    Route::put("/update", [CustomerController::class, "update"])->middleware("auth:account_api");
});

Route::prefix("/user")->group(function() {
    Route::post("/register",[UserController::class, "register"]);
    Route::put("/update", [UserController::class, "update"])->middleware("auth:account_api");
});

Route::prefix("/category")->group(function() {
    Route::post("/create",[CategoryController::class, "create"])->middleware("auth:account_api");
    Route::put("/update:{category_id}",[CategoryController::class, "update"])->middleware("auth:account_api");
    Route::put("/active:{category_id}",[CategoryController::class, "active"])->middleware("auth:account_api");
    Route::get("/list",[CategoryController::class, "index"]);
    Route::get("/list-name",[CategoryController::class, "list"]);
    Route::get("/search",[CategoryController::class, "search"]);
    Route::get("/search-name",[CategoryController::class, "searchName"]);
    Route::delete("/delete:{category_id}",[CategoryController::class, "delete"])->middleware("auth:account_api");
});

Route::prefix("/product")->group(function() {
    Route::post("/create",[ProductController::class, "create"])->middleware("auth:account_api");
    Route::get("/productsInactive",[ProductController::class, "productsInactive"])->middleware("auth:account_api");
    Route::get("/productsActive",[ProductController::class, "productsActive"])->middleware("auth:account_api");
    Route::put("/update:{product_id}",[ProductController::class, "update"])->middleware("auth:account_api");
    Route::put("/status:{product_id}",[ProductController::class, "status"])->middleware("auth:account_api");
    Route::delete("/delete:{product_id}",[ProductController::class, "delete"])->middleware("auth:account_api");
    Route::get("/show:{product_id}",[ProductController::class, "show"]);
    Route::get("/search",[ProductController::class, "search"])->middleware("auth:account_api");
    Route::get("/search-shop:{keyword}",[ProductController::class, "searchByShop"])->middleware("auth:account_api");
});
