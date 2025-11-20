<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // N'ajoutez que les colonnes qui n'existent pas déjà
            $table->string('nom')->after('id');
            $table->string('prenom')->after('nom');
            // 'role' existe déjà via la migration 2025_11_17_093403
            $table->string('service')->nullable()->after('role');
            $table->string('telephone')->nullable()->after('service');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ne supprimez que les colonnes ajoutées par cette migration
            $table->dropColumn(['nom', 'prenom', 'service', 'telephone']);
        });
    }
};
