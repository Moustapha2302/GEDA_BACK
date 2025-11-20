<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->integer('annee');
            $table->decimal('budget_initial', 15, 2);
            $table->decimal('engage', 15, 2)->default(0);
            $table->decimal('disponible', 15, 2);
            $table->timestamps();

            $table->unique(['service', 'annee']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('budgets');
    }
};
