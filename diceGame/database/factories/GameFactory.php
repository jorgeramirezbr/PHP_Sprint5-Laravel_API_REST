<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dice1' => $this->faker->numberBetween(1, 6),
            'dice2' => $this->faker->numberBetween(1, 6),
            'game_result' => function (array $attributes) {
                $sum = $attributes['dice1'] + $attributes['dice2'];
                return ($sum === 7) ? 'won' : 'lost';
            },
            'player_id' => function () {
                $player_id = User::inRandomOrder()->value('id');  //usa id de users existentes, aleatoriamente
                return $player_id;
            }
        ];
    }
}
