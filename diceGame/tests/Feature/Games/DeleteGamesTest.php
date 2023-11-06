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
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

            // 2 users con sus games
        $this->post('/api/players', [  // Se cambia forma de crear el user en el test porque con factory no se asigna el rol de player automaticamente
            'nickname' => 'saida',
            'email' => 'saida@mail.com',
            'password' => '123456789'
        ]);
        $user = User::where('email', 'saida@mail.com')->first();

        $this->post('/api/players', [  // Se cambia forma de crear el user en el test porque con factory no se asigna el rol de player automaticamente
            'nickname' => 'carlos',
            'email' => 'carlos@mail.com',
            'password' => '123456789'
        ]);
        $user2 = User::where('email', 'carlos@mail.com')->first();
            //se crea 3 games para $user y 2 para $user2, aparte de los 100 del seeder 
        Game::factory(3)->create([
            'player_id' => $user->id
        ]);
        Game::factory(2)->create([
            'player_id' => $user2->id
        ]);

            //aseguramos que se creo todo
        $this->assertCount(105, Game::all());
        $game101 = Game::find(101);
        $this->assertEquals($game101->player_id, $user->id);
        $game104 = Game::find(104);
        $this->assertEquals($game104->player_id, $user2->id);

            //borramos los de 1 user y valoramos los que quedan
        $response = $this->actingAs($user)->delete('/api/players/'.$user->id.'/games');
        $response->assertStatus(200);
        $this->assertCount(102, Game::all());
        
            //borramos los del 2do user y valoramos que queda vacio
        $response2 = $this->actingAs($user2)->delete('/api/players/'.$user2->id.'/games');
        $response2->assertStatus(200);
        $this->assertCount(100, Game::all());
    }
}
