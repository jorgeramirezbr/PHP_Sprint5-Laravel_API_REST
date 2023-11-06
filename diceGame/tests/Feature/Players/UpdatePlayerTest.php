<?php

namespace Tests\Feature\Players;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdatePlayerTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test   */
    public function nickname_can_be_changed(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        $user = User::where('nickname', 'Jorge')->first(); //asignado como admin por el seeder
        $response = $this->actingAs($user)->put('/api/players/'.$user->id, [
            'nickname' => 'coco'
        ]);

        $response->assertStatus(200);
        $updateduser = User::first();

        $this->assertEquals($updateduser->nickname, 'coco'); //cambia el nick
        $this->assertEquals($user->email, 'jorge@mail.com');  //email permanece
    }
}
