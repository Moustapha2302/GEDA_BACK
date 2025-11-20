<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_xx_xx_create_documents_financiers_table.php
    public function up(): void
    {
        Schema::create('documents_financiers', function (Blueprint $table) {
            $table->id();
            $table->string('numero_document')->unique();
            $table->enum('type', ['facture', 'bon_commande', 'mandat', 'ordre_paiement', 'etat_depense', 'budget']);
            $table->json('donnees')->nullable(); // ← AJOUTE nullable() ICI
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('valide')->default(false);
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_financiers');
    }
};
