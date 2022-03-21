<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/')->group(function() {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('jwt.auth')->prefix('/')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});

Route::middleware('jwt.auth')->group(function() {
    Route::apiResource('clientes', 'App\Http\Controllers\ClienteController');
    Route::apiResource('carros', 'App\Http\Controllers\CarroController');
    Route::apiResource('locacoes', 'App\Http\Controllers\LocacaoController');
    Route::apiResource('marcas', 'App\Http\Controllers\MarcaController');
    Route::apiResource('modelos', 'App\Http\Controllers\ModeloController');
});