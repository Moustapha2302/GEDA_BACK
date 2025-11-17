<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Chef du service État Civil (S01)
        User::create([
            'name' => 'Chef Service État Civil',
            'email' => 'chef.s01@ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'chef_service',
            'service_code' => 'S01'
        ]);

        // Secrétaire Général
        User::create([
            'name' => 'Secrétaire Général',
            'email' => 'sg@ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'sg',
            'service_code' => null
        ]);

        // Maire
        User::create([
            'name' => 'Maire de Ziguinchor',
            'email' => 'maire@ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'maire',
            'service_code' => null
        ]);
    }
}
