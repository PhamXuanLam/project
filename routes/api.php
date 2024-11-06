<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
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
    Route::post("/add",[CategoryController::class, "create"])->middleware("auth:account_api");
    Route::get("/list",[CategoryController::class, "index"])->middleware("auth:account_api");
});
