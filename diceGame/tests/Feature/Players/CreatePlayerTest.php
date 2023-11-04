<?php

namespace Tests\Feature\Players;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreatePlayerTest extends TestCase
{
    use RefreshDatabase;  

    /** @test   */
    public function a_player_can_be_created(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea 11 users 
    
        $response = $this->post('/api/players', [
            'nickname' => 'cocox',
            'email' => 'cocox@mail.com',
            'password' => '123456789'
        ]);
    
        $response->assertStatus(200);
        $this->assertCount(12, User::all());
        $user = User::find(12);
    
        $this->assertEquals($user->nickname, 'cocox');
        $this->assertEquals($user->email, 'cocox@mail.com');
        $this->assertEquals($user->password, '123456789');
    
    }

    /** @test */
    public function it_validates_unique_nicknames(): void
    {
        //$this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea 11 users 
    
        // usuario con el nickname 'carlos' 
        User::factory()->create(['nickname' => 'carlos']);
    
         $response = $this->post('/api/players', [
            'nickname' => 'carlos', // el mismo nickname
            'email' => 'carlos@mail.com',
            'password' => '123456789'
        ]);
    
        $response->assertStatus(422); // Debería devolver un error de validación 422, pero el test devuelve 302
        $response->assertJsonValidationErrors('nickname'); // Asegurarse de que el error sea específico para 'nickname'
    }
    
    /** @test   */
    public function it_validates_null_nicknames(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea 11 users 

        $response = $this->post('/api/players', [
            'nickname' => null,
            'email' => 'cocoz@mail.com',
            'password' => '123456789'
        ]);

        $response->assertStatus(200);
        $this->assertCount(12, User::all());

        $user = User::find(12);
        $this->assertEquals($user->nickname, 'Anonymous');
        $this->assertEquals($user->email, 'cocoz@mail.com');
        $this->assertEquals($user->password, '123456789');

    }
}
