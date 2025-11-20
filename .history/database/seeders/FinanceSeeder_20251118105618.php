<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏦 === Création des utilisateurs du Service Finance (S02) ===');

        // 1. Contrôleur Financier (supervise tout)
        User::create([
            'name' => 'Ibrahima Sow',
            'email' => 'controleur.financier@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => null, // Poste transversal
            'role' => 'controleur_financier',
        ]);
        $this->command->info('✅ Contrôleur Financier créé');

        // 2. Chef du Service Finance (S02)
        User::create([
            'name' => 'Amadou Diallo',
            'email' => 'chef.finance@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'chef_s02',
        ]);
        $this->command->info('✅ Chef Finance créé');

        // 3. Agents Finance
        User::create([
            'name' => 'Fatou Sané',
            'email' => 'fatou.sane@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'agent_s02',
        ]);

        User::create([
            'name' => 'Mamadou Bah',
            'email' => 'mamadou.bah@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'agent_s02',
        ]);

        User::create([
            'name' => 'Aissatou Diop',
            'email' => 'comptable@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S02',
            'role' => 'agent_s02',
        ]);
        $this->command->info('✅ 3 Agents Finance créés');

        // 4. Services Techniques (accès lecture S02)
        User::create([
            'name' => 'Ousmane Ndiaye',
            'email' => 'chef.urbanisme@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S03',
            'role' => 'chef_s03',
        ]);

        User::create([
            'name' => 'Moussa Diatta',
            'email' => 'chef.voirie@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S04',
            'role' => 'chef_s04',
        ]);
        $this->command->info('✅ 2 Chefs Services Techniques créés');

        // 5. Autres Services (accès lecture S02)
        User::create([
            'name' => 'Coumba Cissé',
            'email' => 'chef.culture@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S11',
            'role' => 'chef_s11',
        ]);

        User::create([
            'name' => 'Babacar Fall',
            'email' => 'chef.sport@mairie-ziguinchor.sn',
            'password' => Hash::make('password'),
            'service_code' => 'S12',
            'role' => 'chef_s12',
        ]);
        $this->command->info('✅ 2 Chefs Autres Services créés');

        $this->command->info('');
        $this->command->info('🎉 === Tous les utilisateurs Finance créés avec succès ===');
        $this->command->info('🔑 Mot de passe par défaut: password');
        $this->command->info('');
        $this->command->table(
            ['Rôle', 'Email', 'Service'],
            [
                ['Contrôleur Financier', 'controleur.financier@mairie-ziguinchor.sn', 'Transversal'],
                ['Chef Finance', 'chef.finance@mairie-ziguinchor.sn', 'S02'],
                ['Agent Finance 1', 'fatou.sane@mairie-ziguinchor.sn', 'S02'],
                ['Agent Finance 2', 'mamadou.bah@mairie-ziguinchor.sn', 'S02'],
                ['Comptable', 'comptable@mairie-ziguinchor.sn', 'S02'],
                ['Chef Urbanisme', 'chef.urbanisme@mairie-ziguinchor.sn', 'S03'],
                ['Chef Voirie', 'chef.voirie@mairie-ziguinchor.sn', 'S04'],
                ['Chef Culture', 'chef.culture@mairie-ziguinchor.sn', 'S11'],
                ['Chef Sport', 'chef.sport@mairie-ziguinchor.sn', 'S12'],
            ]
        );
    }
}
