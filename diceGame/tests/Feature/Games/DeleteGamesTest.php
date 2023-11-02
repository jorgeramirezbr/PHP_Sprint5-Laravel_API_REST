<?php

namespace Tests\Feature\Games;

use App\Models\Game;
use App\Models\User;
use Database\Factories\GameFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteGamesTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test   */
    public function games_from_a_user_can_be_deleted(): void
    {
        $this->withoutExceptionHandling(); 

            // 2 users con sus games
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        Game::factory(3)->create([
            'player_id' => $user->id
        ]);
        Game::factory(2)->create([
            'player_id' => $user2->id
        ]);

            //aseguramos que se creo todo
        $this->assertCount(5, Game::all());
        $game1 = Game::first();
        $this->assertEquals($game1->player_id, $user->id);
        $game4 = Game::find(4);
        $this->assertEquals($game4->player_id, $user2->id);

            //borramos los de 1 user y valoramos los que quedan
        $response = $this->delete('/api/players/'.$user->id.'/games');
        $response->assertStatus(200);
        $this->assertCount(2, Game::all());
        
            //borramos los del 2do user y valoramos que queda vacio
        $response2 = $this->delete('/api/players/'.$user2->id.'/games');
        $response2->assertStatus(200);
        $this->assertCount(0, Game::all());
    }
}
