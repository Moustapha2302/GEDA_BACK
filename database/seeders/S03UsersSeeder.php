<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class S03UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'nom' => 'Chef Service Urbanisme',
                'prenom' => 'Amadou',
                'email' => 'chef.s03@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S03',
            ],
            [
                'nom' => 'Agent Urbanisme',
                'prenom' => 'Aminata',
                'email' => 'agent.s03@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S03',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Utilisateurs S03 (Urbanisme) créés avec succès !');
    }
}
