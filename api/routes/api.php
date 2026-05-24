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
Route::post('/socios/enviar-aviso-email', [AdminController::class, 'enviarAvisoEmail']);
Route::post('/socios/enviar-whatsapp', [AdminController::class, 'enviarAvisoWhatsapp']);
// Route::middleware('auth:sanctum')->post("/admin", [AdminController::class, "admin"]);
Route::post('/socios/ingresos', [AdminController::class, "ingresos"]);
Route::get('/socios/get_ingresos', function () {
    return DB::table('ingresos')->orderBy('fecha_ingreso', 'desc')->get();
});

// Ruta manual para disparar los avisos de WhatsApp (puedes usarla desde el navegador)
Route::get('/socios/run-whatsapp-cron', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('app:send-expiration-whats-app');
        return response()->json([
            'message' => 'Comando ejecutado manualmente con éxito.',
            'output'  => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error'   => 'Error al ejecutar el comando',
            'details' => $e->getMessage()
        ], 500);
    }
});
