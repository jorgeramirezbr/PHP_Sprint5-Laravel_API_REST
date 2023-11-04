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

Route::post('/players', [UserController::class, 'store'])->name('players.store');
Route::put('/players/{id}', [UserController::class, 'update'])->name('players.update');
Route::post('players/{id}/games/', [GameController::class, 'store'])->name('games.store');
Route::delete('players/{id}/games', [GameController::class, 'destroy'])->name('games.destroy');
Route::get('/players', [UserController::class, 'index'])->name('players.index');
Route::get('players/{id}/games', [UserController::class, 'show'])->name('players.show');
Route::get('/players/ranking', [UserController::class, 'ranking'])->name('players.ranking');
Route::get('/players/ranking/loser', [UserController::class, 'getLoser'])->name('players.getLoser');
Route::get('/players/ranking/winner', [UserController::class, 'getWinner'])->name('players.getWinner');