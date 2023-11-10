<?php

namespace Tests\Feature\Games;

use App\Models\Game;
use App\Models\User;
use Database\Factories\GameFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteGamesTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test  */
    public function games_can_be_deleted_by_admin(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Admin']   //seria el user 12
        );

        $response = $this->delete('/api/players/11/games'); //borra los juegos de el user 11

        $user = User::find(11);
        $response->assertStatus(200);
        $this->assertCount(0, $user->games);
    }

    /** @test  */
    public function games_can_be_deleted_by_his_admin(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            User::find(11),
            ['Admin']   //seria el user 11
        );

        $response = $this->delete('/api/players/11/games'); //borra sus propios juegos

        $user = User::find(11);
        $response->assertStatus(200);
        $this->assertCount(0, $user->games);
    }

    /** @test  */
    public function games_can_be_deleted_by_his_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            User::find(11),
            ['Player']   //seria el user 11, el ultimo
        );

        $response = $this->delete('/api/players/11/games'); //borra sus propios games

        $user = User::find(11);
        $response->assertStatus(200);
        $this->assertCount(0, $user->games);
    }

    /** @test  */
    public function games_cannot_be_deleted_by_another_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea roles, 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->delete('/api/players/11/games'); //borra los games de otro player, el 11
        $response->assertStatus(403);
    }
}
