<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::select('id', 'nickname', 'success_percentage')->get();
        return response()->json($users);
    }

    public function store(StoreUserRequest $request){
        $data = $request->validated();
        User::create($data);
    }

    public function update(UpdateUserRequest $request, $id){
        $user = User::find($id);
        $user->nickname = $request->input('nickname');
        $user->save();
    }

    public function show($id){
        $user = User::with('games')->find($id);

        if (!$user) {
            return response()->json(['error' => 'El usuario no existe.'], 404);
        }

        $games = $user->games;
        return response()->json($games);
    }

    public function ranking()
    {
        $averageSuccessPercentage = User::average('success_percentage');
        $rankedPlayers = User::orderBy('success_percentage', 'desc')->get();

        return response()->json([
            'average_success_percentage' => $averageSuccessPercentage,
            'ranked_players' => $rankedPlayers,
        ]);
    }

    public function getLoser()
    {
        // no considero a los jugadores que tienen 'succes_percentage' de null, ya que no han participado en ningun juego y no lo consideraria el PEOR
        $loser = User::whereNotNull('success_percentage')->orderBy('success_percentage', 'asc')->first();  

        if (!$loser) {
            return response()->json(['message' => 'No players found.'], 404);
        }

        return response()->json($loser);
    }

    public function getWinner()
    {
        // no consideramos a los jugadores con 'success_percentage' null
        $winner = User::whereNotNull('success_percentage')->orderBy('success_percentage', 'desc')->first();

        if (!$winner) {
            return response()->json(['message' => 'No players found.'], 404);
        }

        return response()->json($winner);
    }
}
