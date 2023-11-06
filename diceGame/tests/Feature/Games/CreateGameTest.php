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
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        $this->post('/api/players', [  // Se cambia forma de crear el user en el test porque con factory no se asigna el rol de player automaticamente
            'nickname' => 'saida',
            'email' => 'saida@mail.com',
            'password' => '123456789'
        ]);
        $user = User::where('email', 'saida@mail.com')->first();
        $this->assertTrue($user->hasRole('Player'));
        $response = $this->actingAs($user)->post('/api/players/'.$user->id.'/games/');
        $response->assertStatus(200);
        $this->assertCount(1, $user->games);
        $game = Game::where('player_id', $user->id)->first();  // por ahora seria el player 12 y juego 101
        $this->assertEquals($game->player_id, $user->id);

        // probamos con un segundo usuario
        $this->post('/api/players', [  // Se cambia forma de crear el user en el test porque con factory no se asigna el rol de player automaticamente
            'nickname' => 'carlos',
            'email' => 'carlos@mail.com',
            'password' => '123456789'
        ]);
        $user2 = User::where('email', 'carlos@mail.com')->first();
        $response2 = $this->actingAs($user2)->post('/api/players/'.$user2->id.'/games/');
        $response2->assertStatus(200);
        $this->assertCount(102, Game::all());
        $game2 = Game::find(102);
        $this->assertEquals($game2->player_id, $user2->id);

        // probamos con un segundo game en un mismo usuario
        $response3 = $this->actingAs($user)->post('/api/players/'.$user->id.'/games/');
        $response3->assertStatus(200);
        $this->assertCount(2, Game::where('player_id', $user->id)->get());
        $game3 = Game::find(103);
        $this->assertEquals($game3->player_id, $user->id);

        $this->assertCount(13, User::all());   // 2 usuarios + 11 creados por el seeder
    }
}
