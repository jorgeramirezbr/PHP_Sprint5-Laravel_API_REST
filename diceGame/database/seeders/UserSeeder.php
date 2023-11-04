<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nickname'=>'Jorge',
            'email'=>'jorge@mail.com',
            'password'=>bcrypt('123456')
        ])->syncRoles(['Admin', 'Player']);   //  me asigno como Admin

        User::create([
            'nickname'=>'Ruben',
            'email'=>'ruben@mail.com',
            'password'=>bcrypt('123456')
        ])->syncRoles(['Admin', 'Player']); // asigno al tutor Ruben como Admin

        User::factory(9)->create();  //todos tendran el mismo password
         
    }
}
