<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acte')->unique();
            $table->enum('type', ['naissance', 'mariage', 'deces']);
            $table->json('donnees');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('valide')->default(false);
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index('type');
            $table->index('valide');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actes');
    }
};
