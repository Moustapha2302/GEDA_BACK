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
        $table->json('donnees');                    // toutes les infos
        $table->boolean('valide')->default(false);
        $table->foreignId('created_by')->constrained('users');
        $table->foreignId('valide_par')->nullable()->constrained('users');
        $table->timestamp('date_validation')->nullable();
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('actes');
    }
};
