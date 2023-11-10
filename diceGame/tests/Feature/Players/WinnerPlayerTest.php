<?php

namespace Tests\Feature\Players;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class WinnerPlayerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function the_player_with_the_best_success_percentage_can_be_retrieved_by_admin(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games
        
        Passport::actingAs(
            User::factory()->create(),
            ['Admin']
        );

        $response = $this->get('/api/players/ranking/winner');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'nickname',
            'success_percentage',
        ]);
    }

    /** @test */
    public function the_player_with_the_best_success_percentage_cannot_be_retrieved_by_a_player(): void
    {
        //$this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['PLayer']
        );
        
        $response = $this->get('/api/players/ranking/winner');

        $response->assertStatus(403);
    }
}
