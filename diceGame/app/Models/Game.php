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
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
