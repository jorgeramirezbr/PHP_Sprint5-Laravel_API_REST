<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request){
        if ($request->user()->tokenCan('Admin')) {
            $users = User::select('id', 'nickname', 'success_percentage')->get();
            return response()->json($users);
        }   
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('nickname', request('nickname'))->orWhereNull('nickname');
                }),
            ],
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:9',  
                'regex:/^(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', // al menos un carácter especial
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Los datos no son válidos. El password debe tener al menos un caracter especial', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['nickname'] = $data['nickname'] ?? 'Anonymous';
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        event(new UserCreated($user));

        return response()->json($user, 200);
    }

    public function update(UpdateUserRequest $request, $id){
        $user = User::find($id);
        
        if ($request->user()->tokenCan('Admin')) {
            $user->nickname = $request->input('nickname');
        } 
        elseif ($request->user()->tokenCan('Player')) {
            // un Player solo puede actualizar su propio nickname
            if ($user->id !== $request->user()->id) {
                return response()->json(['error' => 'No tienes permiso para actualizar este usuario.'], 403);
            }
            $user->nickname = $request->input('nickname');
        } 
        else {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
        }        
        $user->save();
    }

    public function show($id, Request $request){
        $user = User::with('games')->find($id);
    
        if (!$user) {
            return response()->json(['error' => 'El usuario no existe.'], 404);
        }
        // verificar si el usuario autenticado es un administrador 
        if ($request->user()->tokenCan('Admin')) { 
            $games = $user->games;
            return response()->json($games);
        } elseif ($request->user()->tokenCan('Player')) {
            if ($user->id !== $request->user()->id) {   //un player solo puede ver sus propios juegos
                return response()->json(['error' => 'No tienes permiso para mostrar los juegos de otro jugador.'], 403);
            }
            $games = $user->games;
            return response()->json($games);
        } else {
            return response()->json(['error' => 'No tienes permisos para esta solicitud.'], 403);
        }
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
