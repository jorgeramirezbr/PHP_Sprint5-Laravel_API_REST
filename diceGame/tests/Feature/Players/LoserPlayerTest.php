<?php

namespace Tests\Feature\Players;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoserPlayerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_returns_the_player_with_the_worst_success_percentage(): void
    {
        $this->withoutExceptionHandling(); 
        
        User::factory()->create(['success_percentage' => 30]);
        User::factory()->create(['success_percentage' => 10]);
        User::factory()->create(['success_percentage' => 50]);

        $response = $this->get('/api/players/ranking/loser');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'nickname',
            'success_percentage',
        ]);
        
        // verifica que el peor jugador sea el que se devuelva.
        $response->assertJson([
            'success_percentage' => 10
        ]);
    }

    /** @test */
    public function it_returns_the_player_with_the_worst_success_percentage_among_players_with_no_games(): void
    {
        $this->withoutExceptionHandling(); 
        
        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => 0]);
        User::factory()->create(['success_percentage' => 20]);
        User::factory()->create(['success_percentage' => 50]);

        $response = $this->get('/api/players/ranking/loser');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'nickname',
            'success_percentage',
        ]);
        
        // verifica que el peor jugador sea uno que ha participado de los games, de lo contrario no los incluye para elegir al peor
        $response->assertJson([
            'success_percentage' => 0
        ]);
    }
}
