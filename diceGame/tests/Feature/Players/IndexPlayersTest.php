<?php

namespace Tests\Feature\Players;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexPlayersTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test  */
    public function list_of_players_can_be_retrieved(): void
    {
        $this->withoutExceptionHandling(); 

            // 2 users con sus games
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        Game::factory(10)->create([
            'player_id' => $user->id
        ]);
        Game::factory(20)->create([
            'player_id' => $user2->id
        ]);

        $response = $this->get('/api/players');

        $response->assertStatus(200);
        $this->assertCount(30, Game::all());

        $response->assertJsonStructure([
            '*' => [
                'id',
                'nickname',
                'success_percentage',
            ],
        ]);
    }

    /** @test  */
    public function list_of_players_can_be_retrieved_with_success_percentage(): void
    {
        $this->withoutExceptionHandling(); 

            // 2 users con sus games
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        Game::factory(10)->create([
            'dice1' => 2,
            'dice2' => 2,
            'player_id' => $user->id
        ]);
        Game::factory(10)->create([
            'dice1' => 5,
            'dice2' => 2,
            'player_id' => $user2->id
        ]);

            // porcentaje de éxito esperado
        $expectedSuccessPercentageUser = ($user->games->where('game_result', 'won')->count() / $user->games->count()) * 100;
        $expectedSuccessPercentageUser2 = ($user2->games->where('game_result', 'won')->count() / $user2->games->count()) * 100;

        $response = $this->get('/api/players');

        $response->assertStatus(200);

            // comprueba que los porcentajes en la respuesta sean iguales a los esperados
        $response->assertJson([
            [
                'id' => $user->id,
                'nickname' => $user->nickname,
                'success_percentage' => $expectedSuccessPercentageUser
            ],
            [
                'id' => $user2->id,
                'nickname' => $user2->nickname,
                'success_percentage' => $expectedSuccessPercentageUser2
            ],
        ]);
    }
}
