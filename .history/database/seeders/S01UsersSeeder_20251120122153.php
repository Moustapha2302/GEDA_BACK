<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class S01UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'nom' => 'Chef Service État Civil',
                'prenom' => 'Jean',
                'email' => 'chef.s01@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S01',
            ],
            [
                'nom' => 'Agent État Civil',
                'prenom' => 'Marie',
                'email' => 'agent.s01@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S01',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Utilisateurs S01 créés avec succès !');
    }
}
