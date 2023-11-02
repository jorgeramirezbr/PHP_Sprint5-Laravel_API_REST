<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class GameController extends Controller
{
    
    public function store($id){
        // Busca el usuario con el ID proporcionado.
        $user = User::find($id);

        // Verifica si el usuario existe.
        if (!$user) {
            return response()->json(['error' => 'El usuario no existe.'], 404);
        }
        $game = new Game();
        $game->player_id = $id;
        $game->save();

    }
}
