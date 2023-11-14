<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Passport;

class LoginControlller extends Controller
{
    public function login(Request $request){
        try {
            $login = $request->validate([
                'email' => 'required|string',
                'password' =>'required|string'
            ]);
        } catch (ValidationException $exception) {
            // Maneja la excepción de validación y devolver una respuesta JSON con el código 422
            return response(['message' => $exception->validator->errors()], 422);
        }
        $user = User::where('email', $login['email'])->first();   
        if (!$user) {
            return response(['message' =>'User not found'], 401);
        }
        if (!Auth::attempt($login)) {
            return response(['message' =>'Invalid password'], 401);
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
