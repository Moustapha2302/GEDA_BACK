<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_autorisations_table.php
    public function up()
    {
        Schema::create('autorisations', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('type'); // ex: "Autorisation de couper un arbre", etc.
            $table->string('nom_demandeur');
            $table->string('prenom_demandeur');
            $table->text('description');
            $table->enum('statut', ['En attente', 'Approuvée', 'Rejetée'])->default('En attente');
            $table->foreignId('createur_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autorisations');
    }
};
