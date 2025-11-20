<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_comptables', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Mandat', 'Titre', 'Ordre de Recette', 'Engagement']);
            $table->string('numero')->unique();
            $table->decimal('montant', 15, 2);
            $table->text('description');
            $table->string('service_beneficiaire');
            $table->enum('statut', ['Brouillon', 'Validée', 'Rejetée'])->default('Brouillon');

            // Relations
            $table->foreignId('createur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_comptables');
    }
};
