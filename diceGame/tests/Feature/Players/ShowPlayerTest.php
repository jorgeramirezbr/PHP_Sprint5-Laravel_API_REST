<?php

namespace Tests\Feature\Players;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowPlayerTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test  */
    public function it_returns_list_of_games_for_a_player(): void
    {
        $this->withoutExceptionHandling(); 

        $user = User::factory()->create();
        Game::factory(10)->create([
            'player_id' => $user->id
        ]);

        $response = $this->get('/api/players/'.$user->id.'/games');

        $response->assertStatus(200);
        $this->assertCount(10, Game::all());

        $response->assertJsonStructure([
            '*' => [
                'id',
                'dice1',
                'dice2',
                'game_result',
                'player_id',
                'created_at',
                'updated_at'
            ],
        ]);
    }

    /** @test  */
    public function it_returns_list_of_games_for_a_specific_player(): void
    {
        $this->withoutExceptionHandling(); 

        $user = User::factory()->create();
        Game::factory(5)->create([
            'player_id' => $user->id
        ]);
        $user2 = User::factory()->create();
        Game::factory(10)->create([
            'player_id' => $user2->id
        ]);

        $response = $this->get('/api/players/'.$user->id.'/games');

        $response->assertStatus(200);
        $this->assertCount(15, Game::all());
        $response->assertJsonCount(5);

        $response->assertJson(function (AssertableJson $json) use ($user) {
            $json->where('0.player_id', $user->id);
        });
    }

    /** @test  */
    public function it_returns_list_of_games_for_a_player_with_no_games(): void
    {
        $this->withoutExceptionHandling(); 

        $user = User::factory()->create();
        $user2 = User::factory()->create();
        Game::factory(5)->create([
            'player_id' => $user2->id
        ]);

        $response = $this->get('/api/players/'.$user->id.'/games');

        $response->assertStatus(200);
        $this->assertCount(5, Game::all());
        $response->assertJsonCount(0);
    }

    /** @test  */
    public function it_returns_list_of_games_for_a_non_existent_player(): void
    {
        $this->withoutExceptionHandling(); 

        $user = User::factory()->create();
        Game::factory(5)->create([
            'player_id' => $user->id
        ]);

        $response = $this->get('/api/players/2/games');

        $response->assertStatus(404);
        $response->assertJson([]);
    }
}
