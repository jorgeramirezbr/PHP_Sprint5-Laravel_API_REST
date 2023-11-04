<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1=Role::create(['name'=>'Admin']);
        $role2=Role::create(['name'=>'Player']);
        //Permission::create(['name'=>'players.store'])->syncRoles([$role1, $role2]);  //cualquiera puede crear un usuario
        Permission::create(['name'=>'players.update'])->syncRoles([$role1, $role2]);
        Permission::create(['name'=>'games.store'])->syncRoles([$role1, $role2]);
        Permission::create(['name'=>'games.destroy'])->syncRoles([$role1, $role2]);
        Permission::create(['name'=>'players.index'])->assignRole($role1);
        Permission::create(['name'=>'players.show'])->syncRoles([$role1, $role2]);
        Permission::create(['name'=>'players.ranking'])->assignRole($role1);
        Permission::create(['name'=>'players.getLoser'])->assignRole($role1);
        Permission::create(['name'=>'players.getWinner'])->assignRole($role1);
    }
}
