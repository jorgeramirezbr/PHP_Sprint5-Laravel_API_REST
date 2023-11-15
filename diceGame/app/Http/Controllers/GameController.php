<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class GameController extends Controller
{
    
    public function store($id, Request $request){
        $user = $request->user();  // usuario autenticado
        // buscamos el usuario que va a lanzar el juego con el ID
        $player = User::find($id);
    
        if (!$player) {
            return response()->json(['error' => 'El usuario no existe.'], 404);
        }
        // vemos si el usuario autenticado es el mismo que el jugador al que se quiere asignar el juego
        if ($user->id !== $player->id) {
            return response()->json(['error' => 'No tienes permiso para asignar un juego a este jugador.'], 403);
        }
    
        $game = new Game();
        $game->player_id = $id;
        $game->save();
        $game = Game::find($game->id);
        $gameData = [
            'dice1' => $game->dice1,
            'dice2' => $game->dice2,
            'game_result' => $game->game_result,
        ];
        return response()->json(['message' => 'Juego asignado correctamente.', 'game' => $gameData]);
    }

    public function destroy($id, Request $request){
        $user = $request->user();  // usuario autenticado
        // busca el usuario con el ID
        $player = User::find($id);
        if (!$player) {
            return response()->json(['error' => 'El usuario no existe.'], 404);
        }
        // Comprueba si el usuario autenticado es el mismo que el jugador al que se quiere eliminar los juegos,
        // o si el usuario autenticado es un administrador
        if ($user->id === $player->id || $user->tokenCan('Admin')) {
            $player->games()->delete();
            return response()->json(['message' => 'Juegos eliminados correctamente.']);
        } else {
            return response()->json(['error' => 'No tienes permiso para eliminar los juegos de este jugador.'], 403);
        }
    }
}
