<?php

namespace Tests\Feature\Permissions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AssignRolePlayerTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test  */
    public function a_new_user_is_assigned_role_player_automatically(): void
    {
        $this->withoutExceptionHandling(); 
        // Ejecutar los seeders antes de realizar las pruebas
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea 11 users 

        $response = $this->post('/api/players', [  //este seria el user 12
            'nickname' => 'saida',
            'email' => 'saida@mail.com',
            'password' => '123456789@'
        ]);

        $response->assertStatus(200);
        $this->assertCount(12, User::all());
        $user = User::where('email', 'saida@mail.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Player'));
        $this->assertFalse($user->hasRole('Admin'));
    }

    /** @test */
    public function first_two_users_have_both_admin_and_player_roles()
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('db:seed'); // Ejecuta todos los seeders, que crea 11 users y a los 2 primeros Jorge y Ruben se les asigna el rol de Admin 

        // Verifica que los dos primeros usuarios tengan ambos roles
        $user1 = User::where('nickname', 'Jorge')->first();
        $user2 = User::where('nickname', 'Ruben')->first();

        $this->assertNotNull($user1);
        $this->assertNotNull($user2);
        $this->assertTrue($user1->hasRole('Admin'));
        $this->assertTrue($user1->hasRole('Player'));
        $this->assertTrue($user2->hasRole('Admin'));
        $this->assertTrue($user2->hasRole('Player'));
    }
}
