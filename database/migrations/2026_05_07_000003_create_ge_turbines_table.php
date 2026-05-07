<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ge_turbines', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('conc')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('load_mw', 10, 2)->nullable();
            $table->string('status')->nullable();
            $table->date('date_on')->nullable();
            $table->date('date_off')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ge_turbines');
    }
};
