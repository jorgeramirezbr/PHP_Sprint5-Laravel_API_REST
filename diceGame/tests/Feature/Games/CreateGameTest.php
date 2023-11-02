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
        //$this->assertEquals($user->email, 'coco@mail.com');
        //$this->assertEquals($user->password, '123456789');
    }
}
