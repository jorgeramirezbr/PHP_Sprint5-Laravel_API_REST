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
        $user = User::factory()->create([
            'email' => 'coco@mail.com',
        ]);
        //$user = User::first();
        $response = $this->put('/api/players/'.$user->id, [
            'nickname' => 'coco'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, User::all());
        $updateduser = User::first();

        $this->assertEquals($updateduser->nickname, 'coco'); //cambia el nick
        $this->assertEquals($user->email, 'coco@mail.com');  //email permanece
    }
}
