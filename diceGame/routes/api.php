<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LoginControlller;
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

Route::post('/login', [LoginControlller::class, 'login'])->name('login');

Route::post('/players', [UserController::class, 'store'])->name('players.store');
Route::get('/players', [UserController::class, 'index'])->middleware('can:players.index')->name('players.index');
Route::put('/players/{id}', [UserController::class, 'update'])->middleware('can:players.update')->name('players.update');
Route::get('players/{id}/games', [UserController::class, 'show'])->middleware('can:players.show')->name('players.show');

Route::post('players/{id}/games/', [GameController::class, 'store'])->middleware('can:games.store')->name('games.store');
Route::delete('players/{id}/games', [GameController::class, 'destroy'])->middleware('can:games.destroy')->name('games.destroy');
    
Route::get('/players/ranking', [UserController::class, 'ranking'])->middleware('can:players.ranking')->name('players.ranking');
Route::get('/players/ranking/loser', [UserController::class, 'getLoser'])->middleware('can:players.getLoser')->name('players.getLoser');
Route::get('/players/ranking/winner', [UserController::class, 'getWinner'])->middleware('can:players.getWinner')->name('players.getWinner');