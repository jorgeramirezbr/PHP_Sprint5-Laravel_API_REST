<?php

namespace Tests\Feature\Login;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    private $user;
    private $token;


    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea permisos , 11 users y 100 games
        //$this->artisan('passport:install');  // para instalar las claves de Passport
        // Crea un usuario de ejemplo para la prueba
        $this->post('/api/players', [
            'nickname' => 'cocox',
            'email' => 'cocox@mail.com',
            'password' => '123456789@'
        ]);

        /* $user = User::find(12);  //  el nuevo user es el 12
        // Crea un cliente de acceso personal para el usuario
        $token = $user->createToken('authToken', ['Player'])->accessToken;

        $this->user = $user;
        $this->token = $token; */
    }


    /** @test */
    public function a_user_can_login()
    {
        $this->artisan('passport:install');
        $response = $this->post('/api/login', [
            'email' => 'cocox@mail.com',
            'password' => '123456789@'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
                'user',
                'access_token'
        ]);
    }

    /** @test */
    public function a_user_with_invalid_password_cannot_login()
    {
        $response = $this->post('/api/login', [
            'email' => 'cocox@mail.com',
            'password' => '123456788@'    //password invalido
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid password']);
    }

    /** @test */
    public function a_user_not_found_cannot_login()
    {
        $response = $this->post('/api/login', [
            'email' => 'cocoZ@mail.com',   //email invalido
            'password' => '123456789@'    
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'User not found']);
    }

    /** @test */
    public function a_user_with_null_field_request_cannot_login()
    {
        $this->withoutExceptionHandling(); 
        $response = $this->post('/api/login', [
            'email' => null,   // email null
            'password' => '123456789@'    
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message' => [
                'email'
            ]
        ]);
    }

    /** @test */
    public function a_user_with_empty_field_request_cannot_login()
    {
        $this->withoutExceptionHandling(); 
        $response = $this->post('/api/login', [
            'email' => 'cocox@mail.com',   
            'password' => ''    // campo password vacio
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message' => [
                'password'
            ]
        ]);
    }
}
