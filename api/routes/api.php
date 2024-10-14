<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;

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

// Route::group(
//     [
//         "prefix" => "auth",
//         "middleware" => []
//     ],
//     function () {
//         Route::post("/login", [LoginController::class, "Login"]);
//         // Route::post("/password/recovery", [AuthController::class, "passwordRecovery"]);
//         // Route::post("/password/reset", [AuthController::class, "passwordReset"]);
//         // Route::get("/logout", [AuthController::class, "logout"]);
//         // Route::post("/cookie", [AuthController::class, "setCookie"]);
//     }
// );

Route::post("/login", [LoginController::class, "login"]);
Route::post("/logout", [LoginController::class, "logout"]);
Route::get('/socios/get', [AdminController::class, "get_socios"]);
Route::post('/socios/create', [AdminController::class, "create"]);
Route::put('/socios/edit/{id}', [AdminController::class, "edit"]);
Route::delete('/socios/delete/{id}', [AdminController::class, 'delete']);
Route::get('/socios/expiracion', [AdminController::class, 'get_expirations']);
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    // Otras rutas protegidas bajo /admin
});
// Route::middleware('auth:sanctum')->post("/admin", [AdminController::class, "admin"]);
