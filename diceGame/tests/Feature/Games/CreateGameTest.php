<?php

namespace Tests\Feature\Games;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateGameTest extends TestCase
{
    use RefreshDatabase;  
    
    /** @test  */
    public function a_game_can_be_created_by_his_admin(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            $user = User::factory()->create(),
            ['Admin']   //seria el user 12
        );

        $response = $this->post('/api/players/'.$user->id.'/games'); 

        $response->assertStatus(200);
        $this->assertCount(1, $user->games);
        $game = Game::where('player_id', $user->id)->first();  // por ahora seria el player 12 y juego 101
        $this->assertEquals($game->player_id, $user->id);
    }

    /** @test  */
    public function a_game_can_be_created_by_his_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            $user = User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->post('/api/players/'.$user->id.'/games'); 

        $response->assertStatus(200);
        $this->assertCount(1, $user->games);
        $game = Game::where('player_id', $user->id)->first();  // por ahora seria el player 12 y juego 101
        $this->assertEquals($game->player_id, $user->id);
    }

    /** @test  */
    public function a_game_cannot_be_created_by_admin_for_another_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            $user = User::factory()->create(),
            ['Admin']   //seria el user 12
        );

        $response = $this->post('/api/players/11/games');  // genera un game al player 11

        $response->assertStatus(403);
    }

    /** @test  */
    public function a_game_cannot_be_created_by_a_player_for_another_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            $user = User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->post('/api/players/11/games');  // genera un game al player 11

        $response->assertStatus(403);
    }
}
