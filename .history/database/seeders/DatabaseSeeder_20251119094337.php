<?php

// =====================================================
// FILE: database/seeders/DatabaseSeeder.php
// =====================================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Budget;
use App\Models\PieceComptable;
use App\Models\BonCommande;
use App\Models\MarchePublic;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==================== CRÉER LES UTILISATEURS ====================

        // 1. Contrôleur Financier
        $controleurFinancier = User::create([
            'nom' => 'Diop',
            'prenom' => 'Mamadou',
            'email' => 'controleur.financier@mairie-ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'Contrôleur Financier',
            'service' => 'S02',
            'telephone' => '+221 77 123 45 67',
        ]);

        // 2. Chef S02
        $chefS02 = User::create([
            'nom' => 'Sarr',
            'prenom' => 'Abdoulaye',
            'email' => 'chef.s02@mairie-ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'Chef S02',
            'service' => 'S02',
            'telephone' => '+221 77 234 56 78',
        ]);

        // 3. Agent S02 - Fatou Sané
        $agentS02 = User::create([
            'nom' => 'Sané',
            'prenom' => 'Fatou',
            'email' => 'fatou.sane@mairie-ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'Agent S02',
            'service' => 'S02',
            'telephone' => '+221 77 345 67 89',
        ]);

        // 4. Secrétaire Général
        $sg = User::create([
            'nom' => 'Ndiaye',
            'prenom' => 'Aissatou',
            'email' => 'sg@mairie-ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'SG',
            'service' => 'ADMINISTRATION',
            'telephone' => '+221 77 456 78 90',
        ]);

        // 5. Maire
        $maire = User::create([
            'nom' => 'Fall',
            'prenom' => 'Ousmane',
            'email' => 'maire@mairie-ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'Maire',
            'service' => 'ADMINISTRATION',
            'telephone' => '+221 77 567 89 01',
        ]);

        // 6. Service Technique
        $serviceTech = User::create([
            'nom' => 'Diallo',
            'prenom' => 'Mariama',
            'email' => 'technique@mairie-ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'Chef Service',
            'service' => 'Services Techniques',
            'telephone' => '+221 77 678 90 12',
        ]);

        // 7. Autre Agent S02
        $autreAgent = User::create([
            'nom' => 'Ba',
            'prenom' => 'Cheikh',
            'email' => 'cheikh.ba@mairie-ziguinchor.sn',
            'password' => Hash::make('123456'),
            'role' => 'Agent S02',
            'service' => 'S02',
            'telephone' => '+221 77 789 01 23',
        ]);

        // ==================== CRÉER LES BUDGETS ====================

        Budget::create([
            'service' => 'S02',
            'annee' => 2024,
            'budget_initial' => 50000000,
            'engage' => 30000000,
            'disponible' => 20000000,
        ]);

        Budget::create([
            'service' => 'S01',
            'annee' => 2024,
            'budget_initial' => 30000000,
            'engage' => 15000000,
            'disponible' => 15000000,
        ]);

        Budget::create([
            'service' => 'Services Techniques',
            'annee' => 2024,
            'budget_initial' => 100000000,
            'engage' => 65000000,
            'disponible' => 35000000,
        ]);

        Budget::create([
            'service' => 'ADMINISTRATION',
            'annee' => 2024,
            'budget_initial' => 40000000,
            'engage' => 20000000,
            'disponible' => 20000000,
        ]);

        // ==================== CRÉER DES PIÈCES COMPTABLES ====================

        // Pièce 1 - Brouillon (créée par Fatou)
        PieceComptable::create([
            'type' => 'Mandat',
            'numero' => 'M2024-001',
            'montant' => 150000,
            'description' => 'Paiement fournitures de bureau - Service État Civil',
            'service_beneficiaire' => 'S01',
            'statut' => 'Brouillon',
            'createur_id' => $agentS02->id,
        ]);

        // Pièce 2 - Validée
        $piece2 = PieceComptable::create([
            'type' => 'Titre',
            'numero' => 'T2024-001',
            'montant' => 500000,
            'description' => 'Recette des taxes municipales',
            'service_beneficiaire' => 'S02',
            'statut' => 'Validée',
            'createur_id' => $chefS02->id,
            'validateur_id' => $controleurFinancier->id,
            'date_validation' => now(),
        ]);

        // Pièce 3 - Brouillon (créée par Cheikh)
        PieceComptable::create([
            'type' => 'Engagement',
            'numero' => 'E2024-001',
            'montant' => 2500000,
            'description' => 'Engagement pour travaux de voirie',
            'service_beneficiaire' => 'Services Techniques',
            'statut' => 'Brouillon',
            'createur_id' => $autreAgent->id,
        ]);

        // Pièce 4 - Validée
        PieceComptable::create([
            'type' => 'Mandat',
            'numero' => 'M2024-002',
            'montant' => 750000,
            'description' => 'Paiement électricité - Hôtel de Ville',
            'service_beneficiaire' => 'ADMINISTRATION',
            'statut' => 'Validée',
            'createur_id' => $agentS02->id,
            'validateur_id' => $controleurFinancier->id,
            'date_validation' => now()->subDays(2),
        ]);

        // ==================== CRÉER DES BONS DE COMMANDE ====================

        // Bon 1 - En attente
        BonCommande::create([
            'numero' => 'BC2024-001',
            'objet' => 'Achat de matériel informatique',
            'montant' => 3500000,
            'fournisseur' => 'SARL TechSénégal',
            'service_demandeur' => 'S02',
            'statut' => 'En attente',
            'createur_id' => $agentS02->id,
        ]);

        // Bon 2 - Visé
        BonCommande::create([
            'numero' => 'BC2024-002',
            'objet' => 'Fournitures de bureau',
            'montant' => 850000,
            'fournisseur' => 'Papeterie Moderne',
            'service_demandeur' => 'S01',
            'statut' => 'Visé',
            'createur_id' => $serviceTech->id,
            'viseur_id' => $controleurFinancier->id,
            'date_visa' => now()->subDays(1),
            'avis' => 'Favorable',
            'observations' => 'Budget disponible confirmé',
        ]);

        // Bon 3 - En attente
        BonCommande::create([
            'numero' => 'BC2024-003',
            'objet' => 'Matériaux de construction',
            'montant' => 5000000,
            'fournisseur' => 'Construction Ziguinchor',
            'service_demandeur' => 'Services Techniques',
            'statut' => 'En attente',
            'createur_id' => $serviceTech->id,
        ]);

        // ==================== CRÉER DES MARCHÉS PUBLICS ====================

        // Marché 1 - En attente
        MarchePublic::create([
            'numero' => 'MP2024-001',
            'objet' => 'Réfection de la voirie principale',
            'montant' => 50000000,
            'attributaire' => 'Entreprise BTP Casamance',
            'date_debut' => now()->addMonth(),
            'date_fin' => now()->addMonths(6),
            'statut' => 'En attente',
        ]);

        // Marché 2 - Visé
        MarchePublic::create([
            'numero' => 'MP2024-002',
            'objet' => 'Construction d\'un marché municipal',
            'montant' => 75000000,
            'attributaire' => 'Société Générale de Construction',
            'date_debut' => now(),
            'date_fin' => now()->addYear(),
            'statut' => 'Visé',
            'viseur_id' => $controleurFinancier->id,
            'date_visa' => now()->subDays(5),
            'avis' => 'Favorable',
            'observations' => 'Dossier complet et conforme',
        ]);

        // Marché 3 - En attente
        MarchePublic::create([
            'numero' => 'MP2024-003',
            'objet' => 'Fourniture et installation d\'éclairage public',
            'montant' => 30000000,
            'attributaire' => 'Électricité Services SARL',
            'date_debut' => now()->addWeeks(2),
            'date_fin' => now()->addMonths(4),
            'statut' => 'En attente',
        ]);

        $this->command->info('✅ Base de données peuplée avec succès!');
        $this->command->info('');
        $this->command->info('📧 Comptes créés:');
        $this->command->info('1. Contrôleur Financier: controleur.financier@mairie-ziguinchor.sn / 123456');
        $this->command->info('2. Chef S02: chef.s02@mairie-ziguinchor.sn / 123456');
        $this->command->info('3. Agent S02 (Fatou): fatou.sane@mairie-ziguinchor.sn / 123456');
        $this->command->info('4. Agent S02 (Cheikh): cheikh.ba@mairie-ziguinchor.sn / 123456');
        $this->command->info('5. SG: sg@mairie-ziguinchor.sn / 123456');
        $this->command->info('6. Maire: maire@mairie-ziguinchor.sn / 123456');
        $this->command->info('7. Service Technique: technique@mairie-ziguinchor.sn / 123456');
    }
}
