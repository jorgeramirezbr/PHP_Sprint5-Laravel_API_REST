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
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games
        
        $user = User::where('nickname', 'Jorge')->first(); //asignado como admin por el seeder

        $response = $this->actingAs($user)->get('/api/players/ranking');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'average_success_percentage',
            'ranked_players',
        ]);
    }
}