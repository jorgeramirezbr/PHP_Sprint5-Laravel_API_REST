<?php

namespace Tests\Feature\Players;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ShowPlayerTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test  */
    public function list_of_games_for_another_player_can_be_retrieved_by_an_admin(): void
    {
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Admin']
        );

        $response = $this->get('/api/players/11/games'); //pide al player 11 especificamente

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'dice1',
                'dice2',
                'game_result',
                'created_at',                
            ],
        ]);
    }

    /** @test  */
    public function list_of_games_for_another_player_cannot_be_retrieved_by_a_player(): void
    {
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            $user = User::factory()->create(),
            ['Player']
        );

        $this->assertEquals(12, $user->id);  // el nuevo user es el 12
        $response = $this->get('/api/players/11/games'); //pide al player 11 especificamente, otro player

        $response->assertStatus(403);
    }

    /** @test  */
    public function it_returns_list_of_games_for_the_same_player_with_no_games(): void
    {
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            $user = User::factory()->create(),
            ['Player']
        );

        $this->assertEquals(12, $user->id);
        $response = $this->get('/api/players/12/games');
        $response->assertJsonCount(0);
    }

    /** @test  */
    public function it_returns_list_of_games_for_a_non_existent_player(): void
    {
        //$this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games
        
        Passport::actingAs(
            User::factory()->create(),
            ['Admin']
        );

        $response = $this->get('/api/players/200/games');   //player que no existe 

        $response->assertStatus(404);
    }
}
