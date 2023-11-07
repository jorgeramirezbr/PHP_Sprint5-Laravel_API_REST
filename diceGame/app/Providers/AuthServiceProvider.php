<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'Player' =>  'players.update', 'games.store', 'games.destroy', 'players.show',
            'Admin' => 'players.index', 'players.ranking', 'players.getLoser', 'players.getWinner', 'players.update', 'games.store', 'games.destroy', 'players.show',
        ]);
    }
}
