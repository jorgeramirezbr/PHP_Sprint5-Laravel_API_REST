<?php

namespace Tests\Feature\Players;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowPlayerTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test  */
    public function it_returns_list_of_games_for_a_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        $this->post('/api/players', [  // Se cambia forma de crear el user en el test porque con factory no se asigna el rol de player automaticamente
            'nickname' => 'saida',
            'email' => 'saida@mail.com',
            'password' => '123456789'
        ]);
        $user = User::where('email', 'saida@mail.com')->first();
        Game::factory(5)->create([
            'player_id' => $user->id
        ]);
        $this->assertTrue($user->hasRole('Player'));
        $response = $this->actingAs($user)->get('/api/players/'.$user->id.'/games');

        $response->assertStatus(200);
        $this->assertCount(5, Game::where('player_id', $user->id)->get());

        $response->assertJsonStructure([
            '*' => [
                'id',
                'dice1',
                'dice2',
                'game_result',
                'player_id',
                'created_at',
                'updated_at'
            ],
        ]);
    }

    /** @test  */
    public function it_returns_list_of_games_for_a_player_with_no_games(): void
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
        $response = $this->actingAs($user)->get('/api/players/'.$user->id.'/games');
        $response->assertJsonCount(0);
    }

    /** @test  */
    public function it_returns_list_of_games_for_a_non_existent_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games
        
        $user = User::where('nickname', 'Jorge')->first(); //asignado como admin por el seeder

        $response = $this->actingAs($user)->get('/api/players/200/games');

        $response->assertStatus(404);
        $response->assertJson([]);
    }
}
