<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreatePlayerTest extends TestCase
{
    use RefreshDatabase;  

    /** @test   */
    public function it_validates_null_nicknames(): void
    {
        $this->withoutExceptionHandling(); 
        $response = $this->post('/api/players', [
            'nickname' => null,
            'email' => 'cocos@mail.com',
            'password' => '123456789'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, User::all());
        $user = User::first();

        $this->assertEquals($user->nickname, 'Anonymous');
        $this->assertEquals($user->email, 'cocos@mail.com');
        $this->assertEquals($user->password, '123456789');

    }

    /** @test */
    public function it_validates_unique_nicknames(): void
    {
        $this->withoutExceptionHandling(); 
        // usuario con el nickname 'carlos' 
        User::factory()->create(['nickname' => 'carlos']);

        $response = $this->post('/api/players', [
            'nickname' => 'carlos', // el mismo nickname
            'email' => 'carlos@mail.com',
            'password' => '123456789'
        ]);

        $response->assertStatus(422); // Debería devolver un error de validación
        $response->assertJsonValidationErrors('nickname'); // Asegurarse de que el error sea específico para 'nickname'
    }

    /** @test   */
    public function a_player_can_be_created(): void
    {
        $this->withoutExceptionHandling(); 
        $response = $this->post('/api/players', [
            'nickname' => 'coco',
            'email' => 'coco@mail.com',
            'password' => '123456789'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, User::all());
        $user = User::first();

        $this->assertEquals($user->nickname, 'coco');
        $this->assertEquals($user->email, 'coco@mail.com');
        $this->assertEquals($user->password, '123456789');

    }
}
