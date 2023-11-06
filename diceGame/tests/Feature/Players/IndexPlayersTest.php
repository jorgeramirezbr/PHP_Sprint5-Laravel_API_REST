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
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        $user = User::where('nickname', 'Jorge')->first(); //asignado como admin por el seeder
        $response = $this->actingAs($user)->get('/api/players');

        $response->assertStatus(200);
        $this->assertCount(100, Game::all());

        $response->assertJsonStructure([
            '*' => [
                'id',
                'nickname',
                'success_percentage',
            ],
        ]);
    }
}
