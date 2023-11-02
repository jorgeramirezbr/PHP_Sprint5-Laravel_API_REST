<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = ['dice1', 'dice2', 'game_result', 'player_id'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            $dice1 = rand(1, 6);
            $dice2 = rand(1, 6);
            $game->dice1 = $dice1;
            $game->dice2 = $dice2;
            $game->game_result = ($dice1 + $dice2 === 7) ? 'won' : 'lost';
        });

        static::created(function ($game) {
            // encuentra al usuario del juego
            $user = User::find($game->player_id);
            // actualiza el success_percentage del user cuando se crea un game
            $user->updateSuccessPercentage();
        });

            // actualiza el success_percentage del user cuando se elimina un game
        static::deleted(function ($game) {
            $user = User::find($game->player_id);        
            $user->updateSuccessPercentage();
        });
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
