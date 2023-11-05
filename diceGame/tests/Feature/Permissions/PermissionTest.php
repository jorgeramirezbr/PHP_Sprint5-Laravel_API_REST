<?php

namespace Tests\Feature\Permissions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function player_cannot_access_players_index(): void 
    {
        //$this->withoutExceptionHandling();
        $this->artisan('db:seed'); // Ejecuta los seeders, que crean los roles y permisos y 11 users

        $user = User::factory()->create();  //se crea al user 12
        $user->assignRole('Player');
    
        $this->assertCount(12, User::all());
        $this->assertTrue($user->hasRole('Player'));
        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->can('players.index'));
        $this->assertTrue($user->can('games.store'));

        $response = $this->actingAs($user)->get('/api/players');
        $response->assertStatus(403); // Debería devolver un error 403, unauthorized
    }

    /** @test */
    public function player_cannot_access_rankings_methods(): void 
    {
        //$this->withoutExceptionHandling();
        $this->artisan('db:seed'); // Crea roles, permisos y 11 users

        $response = $this->post('/api/players', [  //este seria el user 12, que automaticamente se le asigna rol de player
            'nickname' => 'saida',
            'email' => 'saida@mail.com',
            'password' => '123456789'
        ]);
        $response->assertStatus(200);
        $this->assertCount(12, User::all());

        $user = User::find(12);
        $this->assertTrue($user->hasRole('Player'));
        $this->assertFalse($user->hasRole('Admin'));
        $this->assertTrue($user->can('players.update'));
        $this->assertTrue($user->can('games.store'));
        $this->assertTrue($user->can('games.destroy'));
        $this->assertTrue($user->can('players.show'));
        $this->assertFalse($user->can('players.ranking'));
        $this->assertFalse($user->can('players.getLoser'));
        $this->assertFalse($user->can('players.getWinner'));

        $response2 = $this->actingAs($user)->get('/api/players/ranking');
        $response2->assertStatus(403); // Debería devolver un error 403
    }

    /** @test */
    public function admin_can_access_rankings_methods(): void 
    {
        //$this->withoutExceptionHandling();
        $this->artisan('db:seed'); // Crea roles, permisos y 11 users (incluye 2 admin)

        $user = User::where('nickname', 'Ruben')->first(); //Ruben y Jorge son admin y player establecidos en el seeder

        $this->assertTrue($user->hasRole('Player'));
        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->can('games.destroy'));
        $this->assertTrue($user->can('players.ranking'));
        $this->assertTrue($user->can('players.getLoser'));
        $this->assertTrue($user->can('players.getWinner'));

        $response = $this->actingAs($user)->get('/api/players/ranking');
        $response->assertStatus(200);
    }
}
