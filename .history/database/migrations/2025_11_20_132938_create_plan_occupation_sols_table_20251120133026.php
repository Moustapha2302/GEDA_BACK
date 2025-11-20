<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_plans_occupation_sol_table.php
    public function up()
    {
        Schema::create('plans_occupation_sol', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('fichier'); // chemin du fichier uploadé
            $table->string('zone');
            $table->foreignId('createur_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_occupation_sols');
    }
};
