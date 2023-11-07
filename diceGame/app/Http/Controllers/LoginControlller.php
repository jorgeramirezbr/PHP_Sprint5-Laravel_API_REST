<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginControlller extends Controller
{
    public function login(Request $request){
        $login = $request->validate([
            'email' => 'required|string',
            'password' =>'required|string'
        ]);
        if (!Auth::attempt($login)) {
            return response(['message' =>'Invalid password']);
        }

        $user = User::where('email', $login['email'])->first();   
        if (!$user) {
            return response(['message' =>'User not found']);
        }

        $scope = []; 
        if ($user->hasRole('Admin')) {
            $scope[] = 'Admin';
        } elseif ($user->hasRole('Player')) {
            $scope[] = 'Player';
        } 
        $accessToken = $user->createToken('authToken', $scope)->accessToken;
    
        return response(['user' => $user, 'access_token' => $accessToken]);
    }
}
