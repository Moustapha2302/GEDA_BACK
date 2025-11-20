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
            // ACTEURS STRATÉGIQUES
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
                'service_code' => 'S11',
            ],
            [
                'name' => 'Directeur de Cabinet',
                'email' => 'directeur.cabinet@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'directeur_cabinet',
                'service_code' => 'S12',
            ],
            [
                'name' => 'Administrateur Système',
                'email' => 'admin@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'admin_systeme',
                'service_code' => 'S10',
            ],

            // ============================================
            // S01 - ÉTAT CIVIL
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
            // S02 - FINANCES
            // ============================================
            [
                'name' => 'Directeur Financier',
                'email' => 'directeur.finances@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'directeur_financier',
                'service_code' => 'S02',
            ],
            [
                'name' => 'Contrôleur Financier',
                'email' => 'controleur.financier@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'controleur_financier',
                'service_code' => 'S02',
            ],
            [
                'name' => 'Chef Service Finance',
                'email' => 'chef.s02@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S02',
            ],
            [
                'name' => 'Agent Finance',
                'email' => 'agent.s02@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S02',
            ],
            [
                'name' => 'Fatou Sane',
                'email' => 'fatou.sane@mairie-ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S02',
            ],

            // ============================================
            // S03 - URBANISME
            // ============================================
            [
                'name' => 'Directeur Urbanisme',
                'email' => 'directeur.urbanisme@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'directeur',
                'service_code' => 'S03',
            ],
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
            // S04 - RESSOURCES HUMAINES
            // ============================================
            [
                'name' => 'Directeur des Ressources Humaines',
                'email' => 'drh@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'drh',
                'service_code' => 'S04',
            ],
            [
                'name' => 'Chef Service RH',
                'email' => 'chef.s04@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S04',
            ],
            [
                'name' => 'Agent RH',
                'email' => 'agent.s04@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S04',
            ],

            // ============================================
            // S05 - COMMUNICATION
            // ============================================
            [
                'name' => 'Chef Service Communication',
                'email' => 'chef.s05@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S05',
            ],
            [
                'name' => 'Agent Communication',
                'email' => 'agent.s05@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S05',
            ],

            // ============================================
            // S06 - SERVICES TECHNIQUES COMMUNAUX
            // ============================================
            [
                'name' => 'Directeur Services Techniques Communaux',
                'email' => 'directeur.stc@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'directeur',
                'service_code' => 'S06',
            ],
            [
                'name' => 'Chef Service STC',
                'email' => 'chef.s06@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S06',
            ],
            [
                'name' => 'Agent STC',
                'email' => 'agent.s06@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S06',
            ],

            // ============================================
            // S07 - PLANIFICATION & COMPÉTENCES TRANSFÉRÉES
            // ============================================
            [
                'name' => 'Directeur DPCT',
                'email' => 'directeur.dpct@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'directeur',
                'service_code' => 'S07',
            ],
            [
                'name' => 'Chef Service DPCT',
                'email' => 'chef.s07@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S07',
            ],
            [
                'name' => 'Agent DPCT',
                'email' => 'agent.s07@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S07',
            ],

            // ============================================
            // S08 - PARTENARIAT & COOPÉRATION DÉCENTRALISÉE
            // ============================================
            [
                'name' => 'Responsable CPCD',
                'email' => 'responsable.cpcd@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'responsable',
                'service_code' => 'S08',
            ],
            [
                'name' => 'Chef Service Partenariat',
                'email' => 'chef.s08@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'chef_service',
                'service_code' => 'S08',
            ],
            [
                'name' => 'Agent Partenariat',
                'email' => 'agent.s08@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S08',
            ],

            // ============================================
            // S09 - CELLULE JURIDIQUE & CONTENTIEUX
            // ============================================
            [
                'name' => 'Chef Cellule Juridique',
                'email' => 'juriste@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'juriste',
                'service_code' => 'S09',
            ],
            [
                'name' => 'Agent Juridique',
                'email' => 'agent.s09@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S09',
            ],

            // ============================================
            // S10 - CELLULE INFORMATIQUE
            // ============================================
            [
                'name' => 'Responsable SI',
                'email' => 'responsable.si@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'responsable_si',
                'service_code' => 'S10',
            ],
            [
                'name' => 'Technicien Informatique',
                'email' => 'technicien.s10@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'technicien',
                'service_code' => 'S10',
            ],

            // ============================================
            // S11 - SECRÉTARIAT GÉNÉRAL
            // ============================================
            [
                'name' => 'Assistant Secrétaire Général',
                'email' => 'assistant.sg@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'assistant',
                'service_code' => 'S11',
            ],
            [
                'name' => 'Agent Secrétariat Général',
                'email' => 'agent.s11@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S11',
            ],

            // ============================================
            // S12 - CABINET DU MAIRE
            // ============================================
            [
                'name' => 'Assistant Cabinet Maire',
                'email' => 'assistant.cabinet@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'assistant',
                'service_code' => 'S12',
            ],
            [
                'name' => 'Agent Cabinet',
                'email' => 'agent.s12@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'service_code' => 'S12',
            ],

            // ============================================
            // S13 - ARCHIVES MUNICIPALES
            // ============================================
            [
                'name' => 'Archiviste Municipal',
                'email' => 'archiviste@ziguinchor.sn',
                'password' => Hash::make('123456'),
                'role' => 'archiviste',
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
        $this->command->info('📋 Récapitulatif par service :');

        $services = [
            'Acteurs Stratégiques' => 4,
            'S01 - État Civil' => 2,
            'S02 - Finances' => 4,
            'S03 - Urbanisme' => 3,
            'S04 - Ressources Humaines' => 3,
            'S05 - Communication' => 2,
            'S06 - Services Techniques Communaux' => 3,
            'S07 - Planification & Compétences Transférées' => 3,
            'S08 - Partenariat & Coopération' => 3,
            'S09 - Cellule Juridique' => 2,
            'S10 - Cellule Informatique' => 2,
            'S11 - Secrétariat Général' => 3,
            'S12 - Cabinet du Maire' => 3,
            'S13 - Archives Municipales' => 2,
        ];

        foreach ($services as $service => $count) {
            $this->command->info("  • $service : $count utilisateurs");
        }
    }
}
