<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Maire
        User::create([
            'name' => 'Maire de Ziguinchor',
            'email' => 'maire@ziguinchor.sn',
            'password' => Hash::make('password'),
            'role' => 'maire',
            'service_code' => 'S12',
            'is_chef' => true,
        ]);

        // Secrétaire Général
        User::create([
            'name' => 'Secrétaire Général',
            'email' => 'sg@ziguinchor.sn',
            'password' => Hash::make('password'),
            'role' => 'secretaire_general',
            'service_code' => 'S11',
            'is_chef' => true,
        ]);

        // Chef Service Finances + Contrôleur Financier + Agent S02
        User::create([
            'name' => 'Fatou Sané',
            'email' => 'fatou.sane@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'service_code' => 'S02',
            'is_chef' => false,
        ]);

        User::create([
            'name' => 'Chef Service Finances',
            'email' => 'chef.finances@ziguinchor.sn',
            'password' => Hash::make('password'),
            'role' => 'chef_s02',
            'service_code' => 'S02',
            'is_chef' => true,
        ]);

        User::create([
            'name' => 'Contrôleur Financier',
            'email' => 'controleur@ziguinchor.sn',
            'password' => Hash::make('password'),
            'role' => 'controleur_financier',
            'service_code' => 'S02',
            'is_chef' => false,
        ]);

        // Quelques agents d'autres services
        User::create([
            'name' => 'Agent État Civil',
            'email' => 'agent.s01@ziguinchor.sn',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'service_code' => 'S01',
            'is_chef' => false,
        ]);
    }
}
