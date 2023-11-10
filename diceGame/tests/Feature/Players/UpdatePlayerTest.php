<?php

namespace Tests\Feature\Players;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdatePlayerTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test   */
    public function a_nickname_can_be_changed_by_admin(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Admin']   //seria el user 12
        );

        $response = $this->put('/api/players/11', [   //cambia nickname de el player 11
            'nickname' => 'coco'
        ]); 

        $response->assertStatus(200);
        $updateduser = User::find(11);

        $this->assertEquals($updateduser->nickname, 'coco'); //cambia el nick
    }

    /** @test   */
    public function a_nickname_can_be_changed_by_his_admin(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Admin']   //seria el user 12
        );

        $response = $this->put('/api/players/12', [   //cambia su nickname de Admin
            'nickname' => 'coco'
        ]); 

        $response->assertStatus(200);
        $updateduser = User::find(12);

        $this->assertEquals($updateduser->nickname, 'coco'); //cambia el nick
    }

    /** @test   */
    public function a_nickname_can_be_changed_by_his_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->put('/api/players/12', [   //cambia su propio nickname
            'nickname' => 'coco'
        ]); 

        $response->assertStatus(200);
        $updateduser = User::find(12);

        $this->assertEquals($updateduser->nickname, 'coco'); //cambia el nick
    }

    /** @test   */
    public function a_nickname_cannot_be_changed_by_another_player(): void
    {
        //$this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->put('/api/players/11', [  //cambia nickname de el player 11
            'nickname' => 'coco'
        ]); 

        $response->assertStatus(403); // Debería devolver un error de validación 403, forbidden no tiene los privilegios necesarios
        //$this->assertEquals($user->email, 'jorge@mail.com');  //email permanece
    }

    /** @test   */
    public function a_nickname_can_be_changed_with_null_nickname_by_his_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->put('/api/players/12', [   //cambia su propio nickname
            'nickname' => null
        ]); 

        $response->assertStatus(200);
        $updateduser = User::find(12);

        $this->assertEquals($updateduser->nickname, 'Anonymous'); //cambia el nick
    }

    /** @test   */
    public function a_nickname_can_be_changed_with_empty_nickname_by_his_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->put('/api/players/12', [   //cambia su propio nickname
            'nickname' => ''
        ]); 

        $response->assertStatus(200);
        $updateduser = User::find(12);

        $this->assertEquals($updateduser->nickname, 'Anonymous'); //cambia el nick
    }

    /** @test   */
    public function a_nickname_can_be_changed_with_Anonymous_nickname_by_his_player(): void
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games

        Passport::actingAs(
            User::factory()->create(),
            ['Player']   //seria el user 12
        );

        $response = $this->put('/api/players/12', [   //cambia su propio nickname
            'nickname' => 'Anonymous'
        ]); 

        $response->assertStatus(200);
        $updateduser = User::find(12);

        $this->assertEquals($updateduser->nickname, 'Anonymous'); //cambia el nick
    }

}
