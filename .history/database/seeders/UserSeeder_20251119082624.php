<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            // ============================================
            // UTILISATEURS ADMINISTRATIFS
            // ============================================
            [
                'name' => 'Maire de Ziguinchor',
                'email' => 'maire@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'maire',
                'service_code' => null,
            ],
            [
                'name' => 'Secrétaire Général',
                'email' => 'sg@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'sg',
                'service_code' => null,
            ],

            // ============================================
            // SERVICE S01 - ÉTAT CIVIL
            // ============================================
            [
                'name' => 'Chef Service État Civil',
                'email' => 'chef.s01@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S01',
            ],
            [
                'name' => 'Agent État Civil',
                'email' => 'agent.s01@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S01',
            ],

            // ============================================
            // SERVICE S02 - FINANCE
            // ============================================
            [
                'name' => 'Contrôleur Financier',
                'email' => 'controleur@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'controleur_financier',
                'service_code' => 'S02',
            ],
            [
                'name' => 'Chef Service Finance',
                'email' => 'chef.s02@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_s02',
                'service_code' => 'S02',
            ],
            [
                'name' => 'Agent Finance',
                'email' => 'agent.s02@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent_s02',
                'service_code' => 'S02',
            ],

            // ============================================
            // SERVICE S03 - URBANISME
            // ============================================
            [
                'name' => 'Chef Service Urbanisme',
                'email' => 'chef.s03@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S03',
            ],
            [
                'name' => 'Agent Urbanisme',
                'email' => 'agent.s03@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S03',
            ],

            // ============================================
            // SERVICE S04 - TRAVAUX PUBLICS
            // ============================================
            [
                'name' => 'Chef Service Travaux Publics',
                'email' => 'chef.s04@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S04',
            ],
            [
                'name' => 'Agent Travaux Publics',
                'email' => 'agent.s04@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S04',
            ],

            // ============================================
            // SERVICE S05 - ENVIRONNEMENT
            // ============================================
            [
                'name' => 'Chef Service Environnement',
                'email' => 'chef.s05@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S05',
            ],
            [
                'name' => 'Agent Environnement',
                'email' => 'agent.s05@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S05',
            ],

            // ============================================
            // SERVICE S06 - EAU ET ASSAINISSEMENT
            // ============================================
            [
                'name' => 'Chef Service Eau et Assainissement',
                'email' => 'chef.s06@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S06',
            ],
            [
                'name' => 'Agent Eau et Assainissement',
                'email' => 'agent.s06@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S06',
            ],

            // ============================================
            // SERVICE S07 - TRANSPORT
            // ============================================
            [
                'name' => 'Chef Service Transport',
                'email' => 'chef.s07@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S07',
            ],
            [
                'name' => 'Agent Transport',
                'email' => 'agent.s07@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S07',
            ],

            // ============================================
            // SERVICE S08 - VOIRIE
            // ============================================
            [
                'name' => 'Chef Service Voirie',
                'email' => 'chef.s08@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S08',
            ],
            [
                'name' => 'Agent Voirie',
                'email' => 'agent.s08@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S08',
            ],

            // ============================================
            // SERVICE S09 - ÉLECTRICITÉ
            // ============================================
            [
                'name' => 'Chef Service Électricité',
                'email' => 'chef.s09@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S09',
            ],
            [
                'name' => 'Agent Électricité',
                'email' => 'agent.s09@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S09',
            ],

            // ============================================
            // SERVICE S10 - ÉCLAIRAGE PUBLIC
            // ============================================
            [
                'name' => 'Chef Service Éclairage Public',
                'email' => 'chef.s10@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S10',
            ],
            [
                'name' => 'Agent Éclairage Public',
                'email' => 'agent.s10@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S10',
            ],

            // ============================================
            // SERVICE S11 - RESSOURCES HUMAINES
            // ============================================
            [
                'name' => 'Chef Service Ressources Humaines',
                'email' => 'chef.s11@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S11',
            ],
            [
                'name' => 'Agent Ressources Humaines',
                'email' => 'agent.s11@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S11',
            ],

            // ============================================
            // SERVICE S12 - COMMUNICATION
            // ============================================
            [
                'name' => 'Chef Service Communication',
                'email' => 'chef.s12@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S12',
            ],
            [
                'name' => 'Agent Communication',
                'email' => 'agent.s12@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S12',
            ],

            // ============================================
            // SERVICE S13 - ARCHIVES
            // ============================================
            [
                'name' => 'Chef Service Archives',
                'email' => 'chef.s13@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S13',
            ],
            [
                'name' => 'Agent Archives',
                'email' => 'agent.s13@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S13',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ ' . count($users) . ' utilisateurs créés/mis à jour avec succès !');
        $this->command->info('📧 Mot de passe par défaut : 123456');
        $this->command->newLine();
        $this->command->info('📋 Liste des comptes :');
        $this->command->table(
            ['Email', 'Rôle', 'Service'],
            collect($users)->map(fn($u) => [
                $u['email'],
                $u['role'],
                $u['service_code'] ?? 'N/A'
            ])->toArray()
        );
    }
}
