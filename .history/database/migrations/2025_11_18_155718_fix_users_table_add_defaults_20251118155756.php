<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('agent')->after('email');                    // ← default obligatoire
            $table->string('service_code', 10)->nullable()->default(null)->after('role'); // ← nullable car pas tous ont un service
            $table->boolean('is_chef')->default(false)->after('service_code');
            $table->string('name')->nullable()->change(); // au cas où
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'service_code', 'is_chef']);
        });
    }
};
