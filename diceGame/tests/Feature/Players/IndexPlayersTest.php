<?php

namespace Tests\Feature\Players;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IndexPlayersTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test  */
    public function list_of_players_can_be_retrieved_by_an_admin(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Admin']
        );
     
        $response = $this->get('/api/players');      

        $response->assertStatus(200);
        $this->assertCount(12, User::all());

        $response->assertJsonStructure([
            '*' => [
                'id',
                'nickname',
                'success_percentage',
            ],
        ]);
    }

    /** @test  */
    public function list_of_players_cannot_be_retrieved_by_a_player(): void
    {
        //$this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Player']
        );
     
        $response = $this->get('/api/players');      

        $response->assertStatus(403);
    }
}
