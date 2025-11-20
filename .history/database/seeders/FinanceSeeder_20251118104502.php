<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // Chef du Service Finance (S02)
        User::create([
            'name' => 'Amadou Diallo',
            'email' => 'chef.finance@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'chef_s02',
        ]);

        // Agent Finance 1
        User::create([
            'name' => 'Fatou Sané',
            'email' => 'fatou.sane@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'agent_s02',
        ]);

        // Agent Finance 2
        User::create([
            'name' => 'Mamadou Bah',
            'email' => 'mamadou.bah@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'agent_s02',
        ]);

        // Comptable
        User::create([
            'name' => 'Aissatou Diop',
            'email' => 'comptable@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'agent_s02',
        ]);

        $this->command->info('✅ Utilisateurs du Service Finance créés avec succès');
    }
}
