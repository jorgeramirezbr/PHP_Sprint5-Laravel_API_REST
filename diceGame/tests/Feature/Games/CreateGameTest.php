<?php

namespace Tests\Feature\Games;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateGameTest extends TestCase
{
    use RefreshDatabase;  
    
    /** @test  */
    public function a_game_can_be_created(): void
    {
        $this->withoutExceptionHandling(); 

        $user = User::factory()->create();
        $response = $this->post('/api/players/'.$user->id.'/games/');
        $response->assertStatus(200);
        $this->assertCount(1, Game::all());
        $game = Game::first();
        $this->assertEquals($game->player_id, $user->id);

        // probamos con un segundo usuario
        $user2 = User::factory()->create();
        $response2 = $this->post('/api/players/'.$user2->id.'/games/');
        $response2->assertStatus(200);
        $this->assertCount(2, Game::all());
        $game2 = Game::find(2);
        $this->assertEquals($game2->player_id, $user2->id);

        // probamos con un segundo game en un mismo usuario
        $response3 = $this->post('/api/players/'.$user->id.'/games/');
        $response3->assertStatus(200);
        $this->assertCount(3, Game::all());
        $game3 = Game::find(3);
        $this->assertEquals($game3->player_id, $user->id);

        $this->assertCount(2, User::all());   //mantenemos aun 2 usuarios
    }
}
