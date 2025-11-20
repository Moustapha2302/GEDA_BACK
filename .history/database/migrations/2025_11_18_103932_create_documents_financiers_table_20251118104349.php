<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents_financiers', function (Blueprint $table) {
            $table->id();

            // Identifiants du document
            $table->string('numero_document')->unique()->comment('Numéro unique du document');
            $table->enum('type', [
                'facture',
                'bon_commande',
                'mandat',
                'ordre_paiement',
                'etat_depense',
                'budget'
            ])->comment('Type de document financier');

            // Données du document (JSON flexible)
            $table->json('donnees')->comment('Détails du document: objet, montant, bénéficiaire, etc.');

            // Traçabilité
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Agent qui a créé le document');

            // Validation
            $table->boolean('valide')->default(false)->comment('Document validé ou non');
            $table->foreignId('valide_par')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Chef S02 ou SG qui a validé');
            $table->timestamp('date_validation')->nullable()->comment('Date de validation');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Index pour performances
            $table->index('numero_document');
            $table->index('type');
            $table->index('valide');
            $table->index('created_by');
            $table->index('valide_par');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_financiers');
    }
};
