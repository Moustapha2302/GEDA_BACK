<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('permis_construire', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('nom_demandeur');
            $table->string('prenom_demandeur');
            $table->string('adresse_terrain');
            $table->decimal('superficie', 8, 2);
            $table->string('type_projet');
            $table->enum('statut', ['Brouillon', 'En attente', 'Visé', 'Rejeté', 'Validé'])->default('Brouillon');
            $table->foreignId('createur_id')->constrained('users');
            $table->foreignId('viseur_id')->nullable()->constrained('users');
            $table->timestamp('date_visa')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permis_construires');
    }
};
