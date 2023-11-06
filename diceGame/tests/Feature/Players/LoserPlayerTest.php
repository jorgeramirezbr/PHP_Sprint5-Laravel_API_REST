<?php

namespace Tests\Feature\Players;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoserPlayerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_returns_the_player_with_the_worst_success_percentage(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games
        
        $user = User::where('nickname', 'Jorge')->first(); //asignado como admin por el seeder

        $response = $this->actingAs($user)->get('/api/players/ranking/loser');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'nickname',
            'success_percentage',
        ]);
    }
}
