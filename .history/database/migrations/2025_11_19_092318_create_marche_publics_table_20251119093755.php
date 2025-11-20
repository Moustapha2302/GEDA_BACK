<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marches_publics', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('objet');
            $table->decimal('montant', 15, 2);
            $table->string('attributaire');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['En attente', 'Visé', 'Rejeté', 'En cours', 'Terminé'])->default('En attente');
            $table->foreignId('viseur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_visa')->nullable();
            $table->enum('avis', ['Favorable', 'Défavorable'])->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marches_publics');
    }
};
