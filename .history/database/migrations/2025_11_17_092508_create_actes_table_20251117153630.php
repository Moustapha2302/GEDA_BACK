<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acte')->unique();
            $table->enum('type', ['naissance', 'mariage', 'deces']);
            $table->json('donnees');
            $table->boolean('valide')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('valide_par')->nullable()->constrained('users');
            $table->timestamp('date_validation')->nullable();
            $table->unsignedBigInteger('service_id')->default(1); // ← AJOUT OBLIGATOIRE
            $table->timestamps();

            // Optionnel : index pour performances
            $table->index('service_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actes');
    }
};
