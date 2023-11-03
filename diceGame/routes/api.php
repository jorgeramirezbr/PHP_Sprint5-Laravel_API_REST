<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/players', [UserController::class, 'store']);
Route::put('/players/{id}', [UserController::class, 'update']);

Route::post('players/{id}/games/', [GameController::class, 'store']);
Route::delete('players/{id}/games', [GameController::class, 'destroy']);
Route::get('/players', [UserController::class, 'index']);
Route::get('players/{id}/games', [UserController::class, 'show']);

Route::get('/players/ranking', [UserController::class, 'ranking']);
Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);