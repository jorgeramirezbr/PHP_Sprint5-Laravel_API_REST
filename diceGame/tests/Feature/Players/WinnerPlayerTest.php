<?php

namespace Tests\Feature\Players;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WinnerPlayerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_returns_the_player_with_the_highest_success_percentage(): void
    {
        $this->withoutExceptionHandling();

        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => 50]);
        User::factory()->create(['success_percentage' => 70]);

        $response = $this->get('/api/players/ranking/winner');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'nickname',
            'success_percentage',
        ]);

        $response->assertJson([
            'success_percentage' => 70,
        ]);
    }

    /** @test */
    public function it_returns_the_error_message_because_players_have_no_games(): void
    {
        $this->withoutExceptionHandling();

        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => null]);

        $response = $this->get('/api/players/ranking/winner');

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'No players found.'
        ]);
    }

    /** @test */
    public function it_returns_a_player_with_no_games_won_because_other_players_have_no_games(): void
    {
        $this->withoutExceptionHandling();

        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => null]);
        User::factory()->create(['success_percentage' => 0]);

        $response = $this->get('/api/players/ranking/winner');

        $response->assertStatus(200);

        $response->assertJson([
            'success_percentage' => 0,
        ]);
    }
}
