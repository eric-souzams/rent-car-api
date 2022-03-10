<?php

use Illuminate\Http\Request;
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

Route::apiResource('clientes', 'App\Http\Controllers\ClienteController');
Route::apiResource('carros', 'App\Http\Controllers\CarroController');
Route::apiResource('locacoes', 'App\Http\Controllers\LocacaoController');
Route::apiResource('marcas', 'App\Http\Controllers\MarcaController');
Route::apiResource('modelos', 'App\Http\Controllers\ModeloController');