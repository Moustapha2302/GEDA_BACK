<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bons_commande', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('objet');
            $table->decimal('montant', 15, 2);
            $table->string('fournisseur');
            $table->string('service_demandeur');
            $table->enum('statut', ['En attente', 'Visé', 'Rejeté'])->default('En attente');
            $table->foreignId('createur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('viseur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_visa')->nullable();
            $table->enum('avis', ['Favorable', 'Défavorable'])->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index('statut');
            $table->index('service_demandeur');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bons_commande');
    }
};
