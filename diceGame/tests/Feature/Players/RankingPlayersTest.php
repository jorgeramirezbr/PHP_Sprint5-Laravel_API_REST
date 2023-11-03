<?php

namespace Tests\Feature\Players;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RankingPlayersTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_returns_average_success_percentage_and_ranked_players(): void
    {
        $this->withoutExceptionHandling(); 
        
        // usuarios con diferentes 'success_percentage'.
        User::factory()->create(['success_percentage' => 60]);
        User::factory()->create(['success_percentage' => 50]);
        User::factory()->create(['success_percentage' => 70]);

        $response = $this->get('/api/players/ranking');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'average_success_percentage',
            'ranked_players',
        ]);
        
            // verifica el promedio y que los usuarios estén ordenados.
        $response->assertJson([
            'average_success_percentage' => 60.0,
            'ranked_players' => [
                [
                    'success_percentage' => 70,
                ],
                [
                    'success_percentage' => 60,
                ],
                [
                    'success_percentage' => 50
                ],
            ],
        ]);
    }

    /** @test */
    public function it_returns_average_success_percentage_and_ranked_players_including_players_with_null_values(): void
    {
        $this->withoutExceptionHandling(); 
        
        // usuarios con diferentes 'success_percentage'.
        User::factory()->create(['success_percentage' => 50]);
        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => 0]);
        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => 70]);

        $response = $this->get('/api/players/ranking');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'average_success_percentage',
            'ranked_players',
        ]);
        
            // verifica el promedio y que los usuarios estén ordenados.
        $response->assertJson([
            'average_success_percentage' => 40.0,
            'ranked_players' => [
                [
                    'success_percentage' => 70,
                ],
                [
                    'success_percentage' => 50,
                ],
                [
                    'success_percentage' => 0,
                ],
                [
                    'success_percentage' => null, //se incluye jugadores que no han jugado ningun game en el ranking, pero no se les contabiliza para el promedio general
                ],
                [
                    'success_percentage' => null   
                ],
            ],
        ]);
    }

    /** @test */
    public function it_returns_average_success_percentage_and_ranked_players_including_players_with_no_games_won(): void
    {
        $this->withoutExceptionHandling(); 
        
        // usuarios con diferentes 'success_percentage'.
        User::factory()->create(['success_percentage' => 50]);
        $user = User::factory()->create();
        Game::factory(5)->create([
            'player_id' => $user->id,
            'dice1' => 2,
            'dice2' => 2
        ]);

        $response = $this->get('/api/players/ranking');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'average_success_percentage',
            'ranked_players',
        ]);
        
            // verifica el promedio y que los usuarios estén ordenados.
        $response->assertJson([
            'average_success_percentage' => 25.0,
            'ranked_players' => [
                [
                    'success_percentage' => 50,
                ],                
                [
                    'success_percentage' => 0,
                ]
            ],
        ]);
    }
}
